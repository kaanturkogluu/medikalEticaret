<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\IyzicoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;

class IyzicoController extends Controller
{
    protected $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }

    public function pay(Order $order)
    {
        // Check if already paid
        if (strtolower($order->order_status) !== 'pending_payment') {
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

            $order->update([
                'order_status' => 'Created', // Mapping to "Hazırlanıyor"
                'synced' => false
            ]);

            // Send Email
            try {
                Mail::to($order->customer_email)->send(new OrderPlaced($order));
            } catch (\Exception $e) {
                Log::error('Iyzico Callback Email Error: ' . $e->getMessage());
            }

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
