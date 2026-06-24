<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index(\App\Services\NetgsmService $netgsmService): View
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders'   => Order::count(),
            'active_channels' => Channel::where('active', true)->count(),
        ];

        // Orders Chart (Last 7 Days)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D');
            $chartData[] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        // Son 10 Website Siparişi (channel_id null olanlar websayfası sayılıyor)
        $recentOrders = Order::with('channel')
            ->whereNull('channel_id')
            ->latest()
            ->take(10)
            ->get();

        $channels = Channel::withCount('channelProducts')->get();

        // Netgsm Bakiye
        $balanceResult = $netgsmService->checkBalance(3);
        $smsBalance = $balanceResult['status'] ? $balanceResult['data'] : [];

        return view('admin.dashboard', compact(
            'stats', 
            'recentOrders', 
            'channels', 
            'chartData', 
            'chartLabels',
            'smsBalance'
        ));
    }
}
