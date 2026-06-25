<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use App\Models\Coupon;
use Illuminate\Support\Str;
use App\Models\Setting;
use App\Models\LoyaltyRule;

class CheckoutController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $coupon = Coupon::where('code', strtoupper($request->code))->first();
        
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Geçersiz kupon kodu.']);
        }
        
        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Bu kupon daha önce kullanılmış.']);
        }

        // Kategori Kısıtlaması Kontrolü
        if ($coupon->categories->count() > 0 && $request->filled('cart_items')) {
            $eligibleCategoryIds = $coupon->categories->pluck('id')->toArray();
            $hasEligibleItem = false;
            
            foreach ($request->cart_items as $item) {
                if (isset($item['category_id']) && in_array((int)$item['category_id'], $eligibleCategoryIds)) {
                    $hasEligibleItem = true;
                    break;
                }
            }
            
            if (!$hasEligibleItem) {
                $categoryNames = $coupon->categories->pluck('name')->implode(', ');
                return response()->json([
                    'success' => false, 
                    'message' => "Bu kupon sadece şu kategorilerde geçerlidir: $categoryNames. Sepetinizde uygun ürün bulunamadı."
                ]);
            }
        }

        // Limitli Kupon (Min. Harcama) Kontrolü
        if ($coupon->type === 'fixed_limit' && $coupon->min_spend > 0 && $request->filled('cart_items')) {
            $eligibleAmount = 0;
            $eligibleCategoryIds = $coupon->categories->pluck('id')->toArray();

            foreach ($request->cart_items as $item) {
                $productId = isset($item['id']) ? $item['id'] : (isset($item['product_id']) ? $item['product_id'] : null);
                $qty = isset($item['qty']) ? (int)$item['qty'] : (isset($item['quantity']) ? (int)$item['quantity'] : 1);
                
                if ($productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $isEligible = empty($eligibleCategoryIds) || in_array($product->category_id, $eligibleCategoryIds);
                        if ($isEligible) {
                            $eligibleAmount += $product->price * $qty;
                        }
                    }
                }
            }

            if ($eligibleAmount < $coupon->min_spend) {
                return response()->json([
                    'success' => false,
                    'message' => "Bu kuponu kullanabilmek için sepetinizde en az " . number_format($coupon->min_spend, 2) . " TL değerinde geçerli ürün olmalıdır."
                ]);
            }
        }
        
        session(['applied_coupon' => $coupon->code]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Kupon başarıyla uygulandı.',
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'category_ids' => $coupon->categories->pluck('id')->toArray()
            ]
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        return response()->json(['success' => true, 'message' => 'Kupon kaldırıldı.']);
    }

    public function applyPoints(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Lütfen giriş yapın.']);
        }
        $request->validate(['points' => 'required|integer|min:1']);
        
        $userPoints = auth()->user()->med_puan;
        if ($request->points > $userPoints) {
            return response()->json(['success' => false, 'message' => 'Yetersiz Med Puan bakiyesi.']);
        }

        session(['applied_points' => $request->points]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Med Puan başarıyla uygulandı.',
            'points' => $request->points
        ]);
    }

    public function removePoints()
    {
        session()->forget('applied_points');
        return response()->json(['success' => true, 'message' => 'Med Puan iptal edildi.']);
    }

    public function index()
    {
        $provinces = \App\Models\Province::orderBy('name')->get();
        
        // Get bank details from settings
        $bankDetails = [
            'bank_name' => 'Ziraat Bankası',
            'bank_iban' => 'TR 1500 0100 0758 5367 4214 5004',
            'bank_account_holder' => 'Turgay Vural',
        ];

        $agreement = \App\Models\Page::where('slug', 'mesafeli-satis-sozlesmesi')->first();
        
        $savedAddresses = [];
        if (auth()->check()) {
            $savedAddresses = \App\Models\UserAddress::where('user_id', auth()->id())->get();
        }

        return view('checkout', compact('provinces', 'bankDetails', 'agreement', 'savedAddresses'));
    }

    public function success(Order $order)
    {
        // Get bank details from settings
        $bankDetails = [
            'bank_name' => 'Ziraat Bankası',
            'bank_iban' => 'TR 1500 0100 0758 5367 4214 5004',
            'bank_account_holder' => 'Turgay Vural',
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
                $hasFreeShippingProduct = false;

                foreach ($validated['cart_items'] as $itemData) {
                    $product = Product::findOrFail($itemData['id']);
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

                // --- KUPON UYGULAMA ---
                $couponDiscount = 0;
                $coupon = null;
                if (session()->has('applied_coupon')) {
                    $coupon = Coupon::where('code', session('applied_coupon'))->first();
                    if ($coupon && $coupon->isValid()) {
                        $couponDiscount = $coupon->calculateDiscountForItems($items);
                    }
                }

                // --- MED PUAN UYGULAMA ---
                $usedPoints = 0;
                $usedPointsDiscount = 0;
                if (auth()->check() && session()->has('applied_points')) {
                    $pts = session('applied_points');
                    if ($pts <= auth()->user()->med_puan) {
                        $usedPoints = $pts;
                        $rate = Setting::getValue('med_puan_rate', 1);
                        $usedPointsDiscount = $pts * $rate;
                    }
                }

                $shippingLimit = 700;
                $shippingFee = 89;
                $shipping = ($subtotal >= $shippingLimit || $hasFreeShippingProduct) ? 0 : $shippingFee;

                $total = $subtotal + $shipping - $couponDiscount - $usedPointsDiscount;
                if ($total < 0) $total = 0;

                // EFT İndirimi (%5) - Kupon ve Puan sonrası tutar üzerinden
                $eftDiscount = 0;
                if ($validated['payment_method'] === 'eft') {
                    $eftDiscount = ($subtotal - $couponDiscount - $usedPointsDiscount) * 0.05;
                    if ($eftDiscount < 0) $eftDiscount = 0;
                    $total -= $eftDiscount;
                }

                $totalDiscount = $eftDiscount + $couponDiscount + $usedPointsDiscount;

                $earnedPoints = 0;
                $rule = LoyaltyRule::where('min_amount', '<=', $subtotal - $couponDiscount - $usedPointsDiscount)
                                   ->where('max_amount', '>=', $subtotal - $couponDiscount - $usedPointsDiscount)
                                   ->first();
                if ($rule) {
                    $earnedPoints = $rule->points;

                    // Çarpan (Multiplier) Kontrolü
                    if (auth()->check()) {
                        $multipliers = \App\Models\LoyaltyMultiplier::orderBy('multiplier', 'desc')->get();
                        
                        foreach ($multipliers as $multiplier) {
                            $startDate = now()->subDays($multiplier->duration_days);
                            
                            // İlgili periyottaki başarılı (iptal/iade olmayan) sipariş sayısı
                            $pastOrdersCount = Order::where('user_id', auth()->id())
                                ->where('created_at', '>=', $startDate)
                                ->whereNotIn('order_status', ['Cancelled', 'Refunded'])
                                ->count();
                                
                            if ($pastOrdersCount >= $multiplier->order_count) {
                                // En yüksek çarpanı bulduğumuzda puanı çarp ve döngüden çık
                                $earnedPoints = round($earnedPoints * $multiplier->multiplier);
                                break;
                            }
                        }
                    }
                }

                // Determine Order Status (Mapping to Admin statusMap)
                $status = 'Awaiting'; // Default: Onay Bekliyor
                if ($validated['payment_method'] === 'credit_card') {
                    $status = 'pending_payment'; // Ödeme Bekleniyor
                }

                $websiteChannel = \App\Models\Channel::where('slug', 'website')->first();

                $order = Order::create([
                    'channel_id' => $websiteChannel?->id,
                    'coupon_id' => $coupon?->id, // Kupon ID'sini kaydet
                    'user_id' => auth()->id(), // Giriş yapmış kullanıcı ID'si
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
                    'payment_method' => $validated['payment_method'],
                    'order_date' => now(),
                    'discount_amount' => $totalDiscount,
                    'synced' => false,
                    'currency' => 'TL',
                    'used_points' => $usedPoints,
                    'used_points_discount' => $usedPointsDiscount,
                    'earned_points' => $earnedPoints
                ]);

                // Kullanıcının puanlarını güncelle
                if (auth()->check()) {
                    $user = auth()->user();
                    if ($usedPoints > 0) {
                        $user->med_puan -= $usedPoints;
                    }
                    if ($earnedPoints > 0 && $validated['payment_method'] !== 'eft') {
                        $user->med_puan += $earnedPoints;
                    }
                    $user->save();
                    
                    session()->forget('applied_points');
                }

                // Kuponu sessiondan temizle (Artık order_id üzerinden takip edilecek)
                if ($coupon) {
                    session()->forget('applied_coupon');
                }

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }

                // If Credit Card, redirect to Iyzico
                if ($validated['payment_method'] === 'credit_card') {
                    return response()->json([
                        'success' => true,
                        'order_id' => $order->id,
                        'redirect_url' => route('iyzico.pay', $order->id),
                        'payment_method' => 'credit_card',
                        'message' => 'Ödeme sayfasına yönlendiriliyorsunuz...'
                    ]);
                }

                // Send Order Confirmation Email (Asynchronous) - Only for non-CC (EFT/COD)
                try {
                    Mail::to($order->customer_email)->send(new OrderPlaced($order));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Order Email Error: ' . $e->getMessage());
                }

                // Send Admin Notification Email
                $adminEmail = \App\Models\Setting::getValue('admin_order_notification_email') ?: config('mail.from.address');
                if ($adminEmail) {
                    try {
                        Mail::to($adminEmail)->send(new \App\Mail\NewOrderAdminNotification($order));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Admin Order Notification Email Error: ' . $e->getMessage());
                    }
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
