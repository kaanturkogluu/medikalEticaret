<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ChannelCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected \App\Services\OrderService $orderService
    ) {}

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with('channel');

        $websiteChannel = \App\Models\Channel::where('slug', 'website')->first();
        $defaultChannelId = $websiteChannel ? (string)$websiteChannel->id : 'all';
        
        $channelId = $request->input('channel_id', $defaultChannelId);

        if ($channelId !== 'all' && $channelId !== null && $channelId !== '') {
            $query->where('channel_id', $channelId);
        }



        $orders = $query->orderByDesc('order_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $channels = \App\Models\Channel::all();
        $shippingCompanies = \App\Models\ShippingCompany::where('active', true)->get();

        $openOrderId = $request->input('open_order_id');
        $openOrder = null;
        if ($openOrderId) {
            $openOrder = Order::with('channel')->find($openOrderId);
        }

        return view('admin.orders', compact('orders', 'channels', 'shippingCompanies', 'channelId', 'openOrder'));
    }

    /**
     * Sync orders from all channels.
     */
    public function sync()
    {
        $this->orderService->fetchAllChannelOrders();
        
        return back()->with('success', 'Siparişler başarıyla senkronize edildi.');
    }

    public function approve(Order $order)
    {
        $order->update(['order_status' => 'Created']);

        // Kuponu kullanıldı olarak işaretle (Eğer daha önce işaretlenmediyse)
        if ($order->coupon_id && !$order->coupon->is_used) {
            $order->coupon->update([
                'is_used' => true,
                'used_at' => now(),
                'order_id' => $order->id,
                'user_id' => $order->user_id ?? auth()->id()
            ]);
        }
        
        // Eğer sipariş EFT ise, puanı şimdi yükle
        if ($order->payment_method === 'eft' && $order->earned_points > 0 && $order->user_id) {
            $user = \App\Models\User::find($order->user_id);
            if ($user) {
                $user->med_puan += $order->earned_points;
                $user->save();
            }
        }

        return back()->with('success', 'Sipariş başarıyla onaylandı.');
    }

    /**
     * Cancel an order and revert points/coupons
     */
    public function cancel(Request $request, Order $order)
    {
        if (in_array(strtolower($order->order_status), ['cancelled', 'iptal edildi'])) {
            return back()->with('error', 'Bu sipariş zaten iptal edilmiş.');
        }

        // EFT Siparişi onaylanmadan önce iptal ediliyorsa, kazanılan puanlar henüz yüklenmediğinden geri alınmamalı.
        $pointsWereAwarded = true;
        if ($order->payment_method === 'eft' && $order->order_status === 'Awaiting') {
            $pointsWereAwarded = false;
        }

        $order->update([
            'order_status' => 'cancelled',
            'canceled_at' => now(), // We can use updated_at, but we set status
            'cancel_reason' => $request->input('cancel_reason'),
        ]);

        // Kuponu iptal et (Tekrar kullanılabilir hale getir)
        if ($order->coupon_id && $order->coupon->is_used) {
            $order->coupon->update([
                'is_used' => false,
                'used_at' => null,
                'order_id' => null,
                'user_id' => null
            ]);
        }

        // Med Puan İade İşlemleri
        if ($order->user_id) {
            $user = \App\Models\User::find($order->user_id);
            if ($user) {
                // Kullanılan puanları geri ver
                if ($order->used_points > 0) {
                    $user->med_puan += $order->used_points;
                }
                
                // Kazanılan puanları geri al (Eğer varsa ve yüklenmişse)
                if ($order->earned_points > 0 && $pointsWereAwarded) {
                    $user->med_puan -= $order->earned_points;
                    // Puanın eksiye düşmemesini sağlayalım
                    if ($user->med_puan < 0) {
                        $user->med_puan = 0;
                    }
                }
                
                $user->save();
            }
        }

        // İptal e-postasını gönder
        try {
            if ($order->customer_email) {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderCancelled($order));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Cancel Email Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Sipariş başarıyla iptal edildi ve müşteriye e-posta gönderildi.');
    }

    /**
     * Update shipping information for an order and mark as shipped.
     */
    public function updateShipping(Request $request, Order $order)
    {
        $request->validate([
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'tracking_code' => 'required|string|max:50',
        ]);

        $order->update([
            'shipping_company_id' => $request->shipping_company_id,
            'tracking_code' => $request->tracking_code,
            'order_status' => 'Shipped' // Kargoya Verildi
        ]);

        $order->load('shippingCompany');

        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderShipped($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shipping Email Error: ' . $e->getMessage());
        }

        // Send SMS to Customer
        try {
            if (!empty($order->customer_phone)) {
                $netgsmService = app(\App\Services\NetgsmService::class);
                $companyName = $order->shippingCompany ? $order->shippingCompany->name : 'Kargo';
                $trackingCode = $order->tracking_code;
                $smsMessage = "Sayın {$order->customer_name} , Ürününüz {$companyName} firmasına . {$trackingCode} kargo takip numarası ile kargoya verilmiştir . Bizi tercih ettiğiniz için teşekkür ederiz. UmutMedikalMarket";
                $netgsmService->sendSms($order->customer_phone, $smsMessage, 'Kargo Bildirimi', $order->customer_name);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shipping SMS Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Kargo bilgileri güncellendi ve müşteriye SMS/E-posta bildirimi gönderildi.');
    }

    public function printLabel(Order $order)
    {
        $order->load(['items.product']);
        $packer = request('packer', 'Bilinmiyor');
        
        return view('admin.orders.print-label', compact('order', 'packer'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['channel', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Test fetching products from Trendyol API (apigw/ecgw format)
     */
    public function testProducts()
    {
        $credential = ChannelCredential::where('channel_id', 1)->first(); // Trendyol
        
        // Daha önce başarılı olan URL formatı:
        $url = "https://apigw.trendyol.com/integration/product/sellers/{$credential->supplier_id}/products";

        $response = Http::withBasicAuth($credential->api_key, $credential->api_secret)
            ->withHeaders([
                'User-Agent' => "{$credential->supplier_id} - SelfIntegration",
                'Accept' => 'application/json'
            ])
            ->timeout(30)
            ->get($url, [
                'page' => 0,
                'size' => 50
            ]);

        $json = $response->json();

        return view('admin.test_products', compact('json', 'url'));
    }

    /**
     * Upload and send invoice PDF
     */
    public function uploadInvoice(Request $request, Order $order)
    {
        $request->validate([
            'invoice_file' => 'required|mimes:pdf|max:5120', // Max 5MB
        ]);

        try {
            if ($request->hasFile('invoice_file')) {
                // Delete old invoice if exists
                if ($order->invoice_file && \Illuminate\Support\Facades\Storage::exists($order->invoice_file)) {
                    \Illuminate\Support\Facades\Storage::delete($order->invoice_file);
                }

                // Store new invoice in storage/app/invoices (private)
                $path = $request->file('invoice_file')->store('invoices');
                
                $order->update([
                    'invoice_file' => $path
                ]);

                // Send Email to Customer
                if ($order->customer_email) {
                    \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderInvoiceMail($order));
                }

                return back()->with('success', 'Fatura başarıyla yüklendi ve müşteriye e-posta olarak gönderildi.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Invoice Upload Error: ' . $e->getMessage());
            return back()->with('error', 'Fatura yüklenirken bir hata oluştu: ' . $e->getMessage());
        }

        return back()->with('error', 'Lütfen geçerli bir PDF dosyası seçin.');
    }
}
