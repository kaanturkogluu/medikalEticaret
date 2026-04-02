<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders'   => Order::count(),
            'total_sales'    => Order::sum('total_price'),
            'active_channels' => Channel::where('active', true)->count(),
        ];

        $recentOrders = Order::with('channel')
            ->latest()
            ->take(5)
            ->get();

        $channels = Channel::withCount('channelProducts')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'channels'));
    }
}
