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

        if ($request->filled('channel_id')) {
            if ($request->channel_id === 'web') {
                $query->whereNull('channel_id');
            } else {
                $query->where('channel_id', $request->channel_id);
            }
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('id', 'LIKE', "%$q%")
                    ->orWhere('customer_name', 'LIKE', "%$q%")
                    ->orWhere('tracking_code', 'LIKE', "%$q%");
            });
        }

        $orders = $query->orderByDesc('order_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $channels = \App\Models\Channel::all();
        $shippingCompanies = \App\Models\ShippingCompany::where('active', true)->get();

        return view('admin.orders', compact('orders', 'channels', 'shippingCompanies'));
    }

    /**
     * Sync orders from all channels.
     */
    public function sync()
    {
        $this->orderService->fetchAllChannelOrders();
        
        return back()->with('success', 'Siparişler başarıyla senkronize edildi.');
    }

    /**
     * Approve an order (e.g. EFT paid)
     */
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
        
        return back()->with('success', 'Sipariş başarıyla onaylandı.');
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

        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderShipped($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shipping Email Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Kargo bilgileri güncellendi ve müşteriye bildirildi.');
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
}
