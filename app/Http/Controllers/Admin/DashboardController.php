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
    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders'   => Order::count(),
            'total_sales'    => Order::sum('total_price'),
            'active_channels' => Channel::where('active', true)->count(),
            'error_count'    => 0,
        ];

        // Error count from log
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $stats['error_count'] = substr_count(strtoupper($content), '.ERROR:');
        }

        // Sync Stats
        $syncStats = [
            'success' => Product::whereIn('marketplace_status', ['ACTIVE', 'APPROVED', 'synced'])->count(),
            'failed'  => Product::whereIn('marketplace_status', ['REJECTED', 'ERROR', 'error'])->count(),
            'pending' => Product::whereIn('marketplace_status', ['PENDING', 'IN_REVIEW', 'pending', 'waiting'])->count(),
        ];
        
        // Total for success rate percentage
        $totalSync = array_sum($syncStats);
        $successRate = $totalSync > 0 ? round(($syncStats['success'] / $totalSync) * 100) : 0;

        // Orders Chart (Last 7 Days)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D');
            $chartData[] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        $recentOrders = Order::with('channel')
            ->latest()
            ->take(5)
            ->get();

        $channels = Channel::withCount('channelProducts')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentOrders', 
            'channels', 
            'syncStats', 
            'successRate', 
            'chartData', 
            'chartLabels'
        ));
    }
}
