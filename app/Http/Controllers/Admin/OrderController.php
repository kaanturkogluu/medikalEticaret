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

        $orders = $query->orderByDesc('order_date')->paginate(15)->withQueryString();
        $channels = \App\Models\Channel::all();

        return view('admin.orders', compact('orders', 'channels'));
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
        
        return back()->with('success', 'Sipariş başarıyla onaylandı.');
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
