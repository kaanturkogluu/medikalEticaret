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
    /**
     * Display a listing of orders.
     */
    public function index(): View
    {
        $orders = Order::with('channel')
            ->latest()
            ->paginate(15);

        return view('admin.orders', compact('orders'));
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
