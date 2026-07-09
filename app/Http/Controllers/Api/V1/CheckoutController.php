<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\LoyaltyRule;

class CheckoutController extends Controller
{
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
            'wants_invoice' => 'nullable|boolean',
            'invoice_type' => 'required_if:wants_invoice,true|in:bireysel,kurumsal',
            'tc_no' => 'required_if:invoice_type,bireysel|nullable|digits:11',
            'company_name' => 'required_if:invoice_type,kurumsal|nullable|string',
            'tax_office' => 'required_if:invoice_type,kurumsal|nullable|string',
            'tax_number' => ['required_if:invoice_type,kurumsal', 'nullable', 'regex:/^[0-9]{10,11}$/'],
            'legal_address' => 'required_if:invoice_type,kurumsal|nullable|string',
            'coupon_code' => 'nullable|string',
            'use_points' => 'nullable|integer|min:1',
        ]);

        try {
            return DB::transaction(function() use ($validated, $request) {
                $subtotal = 0;
                $items = [];
                $hasFreeShippingProduct = false;

                foreach ($validated['cart_items'] as $itemData) {
                    $product = Product::findOrFail($itemData['id']);
                    if ($product->stock < $itemData['qty']) {
                        throw new \Exception("{$product->name} ürünü için yeterli stok bulunmamaktadır. (Mevcut stok: {$product->stock})");
                    }
                    if ($product->free_shipping) {
                        $hasFreeShippingProduct = true;
                    }
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

                // --- COUPON ---
                $couponDiscount = 0;
                $coupon = null;
                if (!empty($validated['coupon_code'])) {
                    $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();
                    if ($coupon && $coupon->isValid()) {
                        // Kategori & Limit kontrolleri
                        $eligible = true;
                        if ($coupon->categories->count() > 0) {
                            $eligibleCategoryIds = $coupon->categories->pluck('id')->toArray();
                            $hasEligibleItem = false;
                            foreach ($items as $item) {
                                $p = Product::find($item['product_id']);
                                if ($p && in_array($p->category_id, $eligibleCategoryIds)) {
                                    $hasEligibleItem = true; break;
                                }
                            }
                            if (!$hasEligibleItem) $eligible = false;
                        }
                        if ($coupon->type === 'fixed_limit' && $coupon->min_spend > 0) {
                            if ($subtotal < $coupon->min_spend) $eligible = false;
                        }
                        
                        if ($eligible) {
                            $couponDiscount = $coupon->calculateDiscountForItems($items);
                        }
                    }
                }

                // --- POINTS ---
                $usedPoints = 0;
                $usedPointsDiscount = 0;
                if (!empty($validated['use_points'])) {
                    $pts = $validated['use_points'];
                    if ($pts <= $request->user()->med_puan) {
                        $usedPoints = $pts;
                        $rate = Setting::getValue('med_puan_rate', 1);
                        $usedPointsDiscount = $pts * $rate;
                    } else {
                        throw new \Exception("Yetersiz Med Puan bakiyesi.");
                    }
                }

                $shippingLimit = 700;
                $shippingFee = 89;
                $shipping = ($subtotal >= $shippingLimit || $hasFreeShippingProduct) ? 0 : $shippingFee;

                $total = $subtotal + $shipping - $couponDiscount - $usedPointsDiscount;
                if ($total < 0) $total = 0;

                // EFT İndirimi (%5)
                $eftDiscount = 0;
                if ($validated['payment_method'] === 'eft') {
                    $eftDiscount = ($subtotal - $couponDiscount - $usedPointsDiscount) * 0.05;
                    if ($eftDiscount < 0) $eftDiscount = 0;
                    $total -= $eftDiscount;
                }

                $totalDiscount = $eftDiscount + $couponDiscount + $usedPointsDiscount;

                $earnedPoints = 0;
                $amount = max(0, $subtotal - $couponDiscount - $usedPointsDiscount);
                        
                $rule = \App\Models\LoyaltyRule::where('min_amount', '<=', $amount)
                    ->where(function($query) use ($amount) {
                        $query->where('max_amount', '>=', $amount)
                              ->orWhere('max_amount', 0)
                              ->orWhereNull('max_amount');
                    })
                    ->orderBy('min_amount', 'desc')
                    ->first();

                if ($rule) {
                    $earnedPoints = $rule->points;
                    $multipliers = \App\Models\LoyaltyMultiplier::orderBy('multiplier', 'desc')->get();
                    foreach ($multipliers as $multiplier) {
                        $startDate = now()->subDays($multiplier->duration_days);
                        $pastOrdersCount = Order::where('user_id', $request->user()->id)
                            ->where('created_at', '>=', $startDate)
                            ->whereNotIn('order_status', ['Cancelled', 'Refunded'])
                            ->count();
                            
                        if ($pastOrdersCount >= $multiplier->order_count) {
                            $earnedPoints = round($earnedPoints * $multiplier->multiplier);
                            break;
                        }
                    }
                }

                $status = 'Awaiting';
                if ($validated['payment_method'] === 'credit_card') {
                    $status = 'pending_payment';
                }

                $mobileChannel = \App\Models\Channel::where('slug', 'mobile_app')->first();
                if (!$mobileChannel) {
                    $mobileChannel = \App\Models\Channel::where('slug', 'website')->first();
                }

                $order = Order::create([
                    'channel_id' => $mobileChannel?->id,
                    'coupon_id' => $coupon?->id,
                    'user_id' => $request->user()->id,
                    'customer_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'],
                    'total_price' => max(0, $total),
                    'shipping_price' => $shipping,
                    'order_status' => $status,
                    'address_info' => [
                        'city' => $validated['city'],
                        'district' => $validated['district'],
                        'neighborhood' => $validated['neighborhood'],
                        'address' => $validated['address']
                    ],
                    'invoice_info' => !empty($validated['wants_invoice']) ? [
                        'type' => $validated['invoice_type'],
                        'tc_no' => $validated['invoice_type'] === 'bireysel' ? ($validated['tc_no'] ?? null) : null,
                        'company_name' => $validated['invoice_type'] === 'kurumsal' ? ($validated['company_name'] ?? null) : null,
                        'tax_office' => $validated['invoice_type'] === 'kurumsal' ? ($validated['tax_office'] ?? null) : null,
                        'tax_number' => $validated['invoice_type'] === 'kurumsal' ? ($validated['tax_number'] ?? null) : null,
                        'legal_address' => $validated['invoice_type'] === 'kurumsal' ? ($validated['legal_address'] ?? null) : null,
                    ] : null,
                    'payment_method' => $validated['payment_method'],
                    'order_date' => now(),
                    'discount_amount' => $totalDiscount,
                    'synced' => false,
                    'currency' => 'TL',
                    'used_points' => $usedPoints,
                    'used_points_discount' => $usedPointsDiscount,
                    'earned_points' => $earnedPoints
                ]);

                $user = $request->user();
                if ($usedPoints > 0) {
                    $user->med_puan -= $usedPoints;
                }
                if ($earnedPoints > 0 && $validated['payment_method'] !== 'eft') {
                    $user->med_puan += $earnedPoints;
                }
                $user->save();

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }

                if ($validated['payment_method'] === 'credit_card') {
                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'order_id' => $order->id,
                            'redirect_url' => route('iyzico.pay', $order->id), // Will be handled in WebView in mobile
                            'payment_method' => 'credit_card',
                        ],
                        'message' => 'Ödeme sayfasına yönlendiriliyorsunuz...'
                    ]);
                }

                try {
                    Mail::to($order->customer_email)->send(new OrderPlaced($order));
                } catch (\Exception $e) {}

                $adminEmail = \App\Models\Setting::getValue('admin_order_notification_email') ?: config('mail.from.address');
                if ($adminEmail) {
                    try {
                        Mail::to($adminEmail)->send(new \App\Mail\NewOrderAdminNotification($order));
                    } catch (\Exception $e) {}
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'order_id' => $order->id,
                        'payment_method' => $validated['payment_method'],
                    ],
                    'message' => 'Siparişiniz başarıyla alındı.'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
