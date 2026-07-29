<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\IyzicoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class IyzicoController extends Controller
{
    protected $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }

    public function pay(Order $order)
    {
        $order->load(['items.product.productImages']);

        // Check if already paid
        if (strtolower(trim($order->order_status)) !== 'pending_payment') {
            return redirect()->route('home')->with('error', 'Bu sipariş için ödeme yapılamaz.');
        }

        $items = $order->items()->with('product')->get()->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity
            ];
        });

        $form = $this->iyzicoService->createForm($order, $items);

        if ($form->getStatus() !== 'success') {
            Log::error('Iyzico Form Error: ' . $form->getErrorMessage());
            return redirect()->back()->with('error', 'Ödeme sistemi şu an başlatılamıyor: ' . $form->getErrorMessage());
        }

        // Save token to order for callback verification
        $order->update(['payment_token' => $form->getToken()]);

        $paymentContent = $form->getCheckoutFormContent();

        return view('iyzico-pay', compact('paymentContent', 'order'));
    }

    public function callback(Request $request)
    {
        Log::info('Iyzico Callback received. Token: ' . $request->token);

        if (!$request->token) {
            Log::warning('Iyzico Callback: Missing token.');
            return redirect()->route('home')->with('error', 'Geçersiz ödeme isteği.');
        }

        $payment = $this->iyzicoService->getPaymentStatus($request->token);
        
        Log::info('Iyzico Payment Status: ' . $payment->getStatus() . ' | Payment Status: ' . $payment->getPaymentStatus() . ' | Conversation ID: ' . $payment->getConversationId());

        if ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') {
            // First try to find by token (most reliable)
            $order = Order::where('payment_token', $request->token)->first();
            
            // Fallback to conversationId
            if (!$order) {
                $orderId = $payment->getConversationId();
                $order = Order::find($orderId);
            }

            if (!$order) {
                Log::error('Iyzico Callback: Order not found! Token: ' . $request->token . ' | ID: ' . $payment->getConversationId());
                return redirect()->route('home')->with('error', 'Sipariş bulunamadı.');
            }

            // Process order payment completion
            $this->completeOrder($order, $payment);

            return redirect()->route('payment.success', $order->id)->with('success', 'Ödemeniz başarıyla alındı.');
        } else {
            // Try to find the order even on failure to show a better error page
            $order = Order::where('payment_token', $request->token)->first();
            if (!$order) {
                $orderId = $payment->getConversationId();
                $order = Order::find($orderId);
            }
            
            $orderId = $order ? $order->id : $payment->getConversationId();
            Log::error('Iyzico Payment Failed: ' . $payment->getErrorMessage() . ' | Order ID: ' . $orderId . ' | Token: ' . $request->token);
            return redirect()->route('payment.failed', $orderId)->with('error', $payment->getErrorMessage());
        }
    }

    /**
     * Handle direct server-to-server webhook notification from Iyzico.
     */
    public function webhook(Request $request)
    {
        Log::info('Iyzico Webhook notification received: ' . json_encode($request->all()));

        $token = $request->input('token');

        if (!$token) {
            Log::warning('Iyzico Webhook: Missing token in payload.');
            return response()->json(['status' => 'error', 'message' => 'Missing token'], 400);
        }

        // Fetch payment details directly from Iyzico API to prevent spoofing
        $payment = $this->iyzicoService->getPaymentStatus($token);

        if ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') {
            $order = Order::where('payment_token', $token)->first();

            if (!$order) {
                $orderId = $payment->getConversationId();
                $order = Order::find($orderId);
            }

            if (!$order) {
                Log::error('Iyzico Webhook: Order not found for token: ' . $token);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Process order payment completion
            $processed = $this->completeOrder($order, $payment);

            return response()->json([
                'status' => 'success',
                'message' => $processed ? 'Sipariş ödemesi başarıyla onaylandı ve tüm işlemler tamamlandı.' : 'Sipariş zaten onaylanmış/ödenmiş durumda.',
                'order_id' => $order->id,
                'customer' => $order->customer_name,
                'total_price' => $order->total_price . ' ' . $order->currency,
                'details' => [
                    'payment_id' => $payment->getPaymentId(),
                    'is_paid' => $order->fresh()->is_paid,
                    'order_status' => $order->fresh()->order_status
                ]
            ]);
        }

        Log::warning('Iyzico Webhook: Payment status is not success. Status: ' . $payment->getStatus());
        return response()->json(['status' => 'ignored']);
    }

    /**
     * Shared logic to complete an order's payment safely and idempotently.
     */
    private function completeOrder(Order $order, $payment)
    {
        // 1. Wrap database changes in a TRANSACTION and acquire lock
        $processed = DB::transaction(function () use ($order, $payment) {
            // Retrieve and lock the order row
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            // If order is already paid, skip stock decrement and updates
            if ($lockedOrder->is_paid) {
                return false;
            }

            $status = strtolower(trim($lockedOrder->order_status));
            if ($status !== 'pending_payment' && $status !== 'cancelled') {
                return false;
            }

            $lockedOrder->update([
                'order_status'      => 'Created', // Mapping to "Hazırlanıyor"
                'is_paid'           => true,
                'iyzico_payment_id' => $payment->getPaymentId(),
                'synced'            => false
            ]);

            // Mark coupon as used
            if ($lockedOrder->coupon_id) {
                $lockedOrder->coupon()->update([
                    'is_used' => true,
                    'used_at' => now(),
                    'order_id' => $lockedOrder->id,
                    'user_id' => $lockedOrder->user_id
                ]);
            }

            // Deduct stock and trigger marketplace sync
            $syncService = app(\App\Services\SyncService::class);
            foreach ($lockedOrder->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                    $syncService->syncProductStock($item->product);
                }
            }

            // Med Puan Re-deduction (since cron might have refunded them during cancel, we must deduct them again!)
            if ($status === 'cancelled' && $lockedOrder->user_id && $lockedOrder->used_points > 0) {
                $user = \App\Models\User::find($lockedOrder->user_id);
                if ($user) {
                    $user->med_puan -= $lockedOrder->used_points;
                    if ($user->med_puan < 0) {
                        $user->med_puan = 0;
                    }
                    $user->save();
                }
            }

            return true;
        });

        // 2. IDEMPOTENCY check: If not processed in this run (was already processed by concurrent request), skip notifications
        if (!$processed) {
            Log::info('Iyzico: Order #' . $order->id . ' is already paid/processed under lock. Skipping duplicate processing.');
            return false;
        }

        // 3. Send notifications AFTER successful transaction
        // Send Customer Email
        try {
            Mail::to($order->customer_email)->send(new OrderPlaced($order));
        } catch (\Exception $e) {
            Log::error('Iyzico: Customer email sending failed for Order #' . $order->id . ': ' . $e->getMessage());
        }

        // Send Admin Email
        $adminEmail = \App\Models\Setting::getValue('admin_order_notification_email') ?: config('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new \App\Mail\NewOrderAdminNotification($order));
            } catch (\Exception $e) {
                Log::error('Iyzico: Admin email sending failed for Order #' . $order->id . ': ' . $e->getMessage());
            }
        }

        // Send Customer SMS
        try {
            if (!empty($order->customer_phone)) {
                $netgsmService = app(\App\Services\NetgsmService::class);
                $smsMessage = "Sayın : {$order->customer_name} , Siparişinizi aldık. Kargonuz hazırlandığında kargo bilgileriniz tarafınıza sms olarak iletilecektir.  Bizi tercih ettiğiniz için teşekkür ederiz. \n Umut Medikal Market";
                $netgsmService->sendSms($order->customer_phone, $smsMessage, 'Sipariş Bildirimi', $order->customer_name);
            }
        } catch (\Exception $e) {
            Log::error('Iyzico: Customer SMS sending failed for Order #' . $order->id . ': ' . $e->getMessage());
        }

        return true;
    }

    public function success($order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('iyzico-success', compact('order'));
    }

    public function failed($order_id = null)
    {
        $order = $order_id ? Order::find($order_id) : null;
        return view('iyzico-failed', compact('order'));
    }
}
