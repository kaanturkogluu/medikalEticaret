<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'city' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'payment_method' => 'required|in:credit_card,eft',
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

                $order = Order::create([
                    'customer_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'],
                    'total_price' => $total,
                    'shipping_price' => $shipping,
                    'order_status' => 'pending',
                    'address_info' => [
                        'city' => $validated['city'],
                        'district' => $validated['district'],
                        'address' => $validated['address']
                    ],
                    'payment_method' => $validated['payment_method'],
                    'channel_id' => null, // Local order
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

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'message' => 'Siparişiniz başarıyla alındı.'
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
