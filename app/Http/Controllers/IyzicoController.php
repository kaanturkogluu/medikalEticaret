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
        $this->logIyzico('Callback: Received notification request', 'info', [
            'ip' => $request->ip(),
            'token' => $request->token,
            'payload' => $request->all()
        ]);

        if (!$request->token) {
            $this->logIyzico('Callback Warning: Missing token.', 'warning');
            return redirect()->route('home')->with('error', 'Geçersiz ödeme isteği.');
        }

        try {
            $payment = $this->iyzicoService->getPaymentStatus($request->token);
            
            $this->logIyzico('Callback: Retrieved payment status', 'info', [
                'token' => $request->token,
                'status' => $payment->getStatus(),
                'paymentStatus' => $payment->getPaymentStatus(),
                'conversationId' => $payment->getConversationId()
            ]);

            if ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') {
                // First try to find by token (most reliable)
                $order = Order::where('payment_token', $request->token)->first();
                
                // Fallback to conversationId
                if (!$order) {
                    $orderId = $payment->getConversationId();
                    $order = Order::find($orderId);
                }

                if (!$order) {
                    $this->logIyzico('Callback Error: Order not found for token ' . $request->token, 'error');
                    return redirect()->route('home')->with('error', 'Sipariş bulunamadı.');
                }

                // Process order payment completion
                $this->completeOrder($order, $payment);

                $this->logIyzico('Callback Success: Redirecting to success page', 'info', ['order_id' => $order->id]);
                return redirect()->route('payment.success', $order->id)->with('success', 'Ödemeniz başarıyla alındı.');
            } else {
                // Try to find the order even on failure to show a better error page
                $order = Order::where('payment_token', $request->token)->first();
                if (!$order) {
                    $orderId = $payment->getConversationId();
                    $order = Order::find($orderId);
                }
                
                $orderId = $order ? $order->id : $payment->getConversationId();
                $errMsg = $payment->getErrorMessage() ?: 'Ödeme başarısız.';
                $this->logIyzico('Callback Failed: ' . $errMsg, 'warning', ['order_id' => $orderId]);
                return redirect()->route('payment.failed', $orderId)->with('error', $errMsg);
            }
        } catch (\Exception $e) {
            $this->logIyzico('Callback Exception: ' . $e->getMessage(), 'error');
            return redirect()->route('home')->with('error', 'Sorgulama sırasında bir hata oluştu.');
        }
    }

    /**
     * Handle direct server-to-server webhook notification from Iyzico.
     */
    public function webhook(Request $request)
    {
        $this->logIyzico('Webhook: Received notification request', 'info', [
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        $token = $request->input('token');

        if (!$token) {
            $response = ['status' => 'error', 'message' => 'Missing token'];
            $this->logIyzico('Webhook Response: Missing token', 'warning', $response);
            return response()->json($response, 400);
        }

        try {
            // Fetch payment details directly from Iyzico API to prevent spoofing
            $payment = $this->iyzicoService->getPaymentStatus($token);

            $this->logIyzico('Webhook: Retrieved payment details from Iyzico', 'info', [
                'token' => $token,
                'status' => $payment->getStatus(),
                'paymentStatus' => $payment->getPaymentStatus(),
                'conversationId' => $payment->getConversationId(),
                'paymentId' => $payment->getPaymentId(),
                'errorCode' => $payment->getErrorCode(),
                'errorMessage' => $payment->getErrorMessage()
            ]);

            if ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') {
                $order = Order::where('payment_token', $token)->first();

                if (!$order) {
                    $orderId = $payment->getConversationId();
                    $order = Order::find($orderId);
                }

                if (!$order) {
                    $response = ['status' => 'error', 'message' => 'Order not found'];
                    $this->logIyzico('Webhook Response: Order not found for token ' . $token, 'error', $response);
                    return response()->json($response, 404);
                }

                // Process order payment completion
                $processed = $this->completeOrder($order, $payment);

                $response = [
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
                ];

                $this->logIyzico('Webhook Response: Order processed successfully', 'info', $response);
                return response()->json($response);
            }

            $response = ['status' => 'ignored', 'message' => 'Payment status is not success. Status: ' . $payment->getStatus()];
            $this->logIyzico('Webhook Response: Ignored (not success)', 'warning', $response);
            return response()->json($response);

        } catch (\Exception $e) {
            $response = ['status' => 'error', 'message' => 'Exception occurred: ' . $e->getMessage()];
            $this->logIyzico('Webhook Response: Error Exception', 'error', $response);
            return response()->json($response, 500);
        }
    }

    /**
     * Shared logic to complete an order's payment safely and idempotently.
     */
    private function completeOrder(Order $order, $payment)
    {
        $this->logIyzico('completeOrder: Starting payment completion', 'info', [
            'order_id' => $order->id,
            'payment_id' => $payment->getPaymentId()
        ]);

        // 1. Wrap database changes in a TRANSACTION and acquire lock
        $processed = DB::transaction(function () use ($order, $payment) {
            // Retrieve and lock the order row
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            // If order is already paid, skip stock decrement and updates
            if ($lockedOrder->is_paid) {
                $this->logIyzico('completeOrder: Order already marked as paid, skipping DB updates.', 'info', ['order_id' => $order->id]);
                return false;
            }

            $status = strtolower(trim($lockedOrder->order_status));
            if ($status !== 'pending_payment' && $status !== 'cancelled') {
                $this->logIyzico('completeOrder: Order status is invalid for completion: ' . $status, 'warning', ['order_id' => $order->id]);
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
                    $this->logIyzico('completeOrder: Re-deducted used points for recovered cancelled order.', 'info', [
                        'order_id' => $lockedOrder->id,
                        'user_id' => $lockedOrder->user_id,
                        'points' => $lockedOrder->used_points
                    ]);
                }
            }

            $this->logIyzico('completeOrder: DB transaction completed successfully', 'info', ['order_id' => $order->id]);
            return true;
        });

        // 2. IDEMPOTENCY check: If not processed in this run (was already processed by concurrent request), skip notifications
        if (!$processed) {
            $this->logIyzico('completeOrder: Skip notification dispatch (idempotent)', 'info', ['order_id' => $order->id]);
            return false;
        }

        // 3. Send notifications AFTER successful transaction
        // Send Customer Email
        try {
            Mail::to($order->customer_email)->send(new OrderPlaced($order));
        } catch (\Exception $e) {
            $this->logIyzico('completeOrder: Customer email sending failed', 'error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        // Send Admin Email
        $adminEmail = \App\Models\Setting::getValue('admin_order_notification_email') ?: config('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new \App\Mail\NewOrderAdminNotification($order));
            } catch (\Exception $e) {
                $this->logIyzico('completeOrder: Admin email sending failed', 'error', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Send Customer SMS (Queued to prevent slow page load)
        try {
            if (!empty($order->customer_phone)) {
                $smsMessage = "Sayın : {$order->customer_name} , Siparişinizi aldık. Kargonuz hazırlandığında kargo bilgileriniz tarafınıza sms olarak iletilecektir.  Bizi tercih ettiğiniz için teşekkür ederiz. \n Umut Medikal Market";
                \App\Jobs\SendOrderSmsJob::dispatch($order->customer_phone, $smsMessage, 'Sipariş Bildirimi', $order->customer_name);
            }
        } catch (\Exception $e) {
            $this->logIyzico('completeOrder: Customer SMS queue dispatch failed', 'error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
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

    /**
     * Write logs to storage/logs/iyzico.log using direct single channel config dynamically
     */
    private function logIyzico(string $message, string $level = 'info', array $context = [])
    {
        try {
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/iyzico.log'),
                'level' => 'debug',
            ])->write($level, $message, $context);
        } catch (\Exception $e) {
            Log::write($level, '[Iyzico Log Fallback] ' . $message, $context);
        }
    }
}
