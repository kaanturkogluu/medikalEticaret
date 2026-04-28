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

        $paymentContent = $form->getCheckoutFormContent();

        return view('iyzico-pay', compact('paymentContent', 'order'));
    }

    public function callback(Request $request)
    {
        if (!$request->token) {
            return redirect()->route('home')->with('error', 'Geçersiz ödeme isteği.');
        }

        $payment = $this->iyzicoService->getPaymentStatus($request->token);

        if ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') {
            $orderId = $payment->getConversationId();
            $order = Order::findOrFail($orderId);

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

            return redirect()->route('checkout.success', $order->id)->with('success', 'Ödemeniz başarıyla alındı.');
        } else {
            Log::error('Iyzico Payment Failed: ' . $payment->getErrorMessage());
            return redirect()->route('home')->with('error', 'Ödeme başarısız: ' . $payment->getErrorMessage());
        }
    }
}
