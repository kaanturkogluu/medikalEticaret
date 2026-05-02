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

                $shippingLimit = 700;
                $shippingFee = 89;
                $shipping = ($subtotal >= $shippingLimit || $hasFreeShippingProduct) ? 0 : $shippingFee;

                $total = $subtotal + $shipping - $couponDiscount;

                // EFT İndirimi (%5) - Kupon sonrası tutar üzerinden
                $eftDiscount = 0;
                if ($validated['payment_method'] === 'eft') {
                    $eftDiscount = ($subtotal - $couponDiscount) * 0.05;
                    $total -= $eftDiscount;
                }

                $totalDiscount = $eftDiscount + $couponDiscount;

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
                    'currency' => 'TL'
                ]);

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
