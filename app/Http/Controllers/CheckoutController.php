<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $provinces = \App\Models\Province::orderBy('name')->get();
        
        // Get bank details from settings
        $bankDetails = [
            'bank_name' => \App\Models\Setting::getValue('bank_name', 'Ziraat Bankası'),
            'bank_iban' => \App\Models\Setting::getValue('bank_iban', 'TR00 0000 0000 0000 0000 0000 00'),
            'bank_account_holder' => \App\Models\Setting::getValue('bank_account_holder', 'ABC Medikal LTD. ŞTİ.'),
        ];

        return view('checkout', compact('provinces', 'bankDetails'));
    }

    public function success(Order $order)
    {
        // Get bank details from settings
        $bankDetails = [
            'bank_name' => \App\Models\Setting::getValue('bank_name', 'Ziraat Bankası'),
            'bank_iban' => \App\Models\Setting::getValue('bank_iban', 'TR00 0000 0000 0000 0000 0000 00'),
            'bank_account_holder' => \App\Models\Setting::getValue('bank_account_holder', 'ABC Medikal LTD. ŞTİ.'),
        ];

        return view('order-success', compact('order', 'bankDetails'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'phone' => ['required', 'string', 'regex:/^(\+90|0)?5[0-9]{9}$/'],
            'city' => 'required|string',
            'district' => 'required|string',
            'neighborhood' => 'required|string',
            'address' => 'required|string',
            'payment_method' => 'required|in:credit_card,eft,cash_on_delivery',
            'cart_items' => 'required|array',
            'cart_items.*.id' => 'required|exists:products,id',
            'cart_items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function() use ($validated) {
                $subtotal = 0;
                $items = [];

                foreach ($validated['cart_items'] as $itemData) {
                    $product = Product::findOrFail($itemData['id']);
                    $price = $product->price;
                    $qty = $itemData['qty'];
                    $subtotal += $price * $qty;
                    
                    $items[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'name' => $product->name
                    ];
                }

                $shippingLimit = 700;
                $shippingFee = 89;
                $shipping = $subtotal >= $shippingLimit ? 0 : $shippingFee;

                $total = $subtotal + $shipping;

                // EFT Discount (5%)
                $discount = 0;
                if ($validated['payment_method'] === 'eft') {
                    $discount = $subtotal * 0.05;
                    $total -= $discount;
                }

                // Determine Order Status (Mapping to Admin statusMap)
                $status = 'Awaiting'; // Default: Onay Bekliyor
                if ($validated['payment_method'] === 'credit_card') {
                    $status = 'Created'; // Hazırlanıyor
                }

                $websiteChannel = \App\Models\Channel::where('slug', 'website')->first();

                $order = Order::create([
                    'channel_id' => $websiteChannel?->id,
                    'customer_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'],
                    'total_price' => $total,
                    'shipping_price' => $shipping,
                    'order_status' => $status,
                    'address_info' => [
                        'city' => $validated['city'],
                        'district' => $validated['district'],
                        'neighborhood' => $validated['neighborhood'],
                        'address' => $validated['address']
                    ],
                    'payment_method' => $validated['payment_method'],
                    'discount_amount' => $discount,
                    'synced' => false,
                    'currency' => 'TL'
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }

                // Send Order Confirmation Email (Asynchronous)
                try {
                    Mail::to($order->customer_email)->send(new OrderPlaced($order));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Order Email Error: ' . $e->getMessage());
                }

                $message = 'Siparişiniz başarıyla alındı.';
                if ($validated['payment_method'] === 'eft') {
                    $message = 'Siparişiniz oluşturuldu. Lütfen banka transferini gerçekleştirin.';
                }

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'redirect_url' => route('checkout.success', $order->id),
                    'payment_method' => $validated['payment_method'],
                    'message' => $message
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
