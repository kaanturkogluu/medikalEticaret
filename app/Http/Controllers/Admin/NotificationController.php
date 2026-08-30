<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function getUpdates()
    {
        $userId = auth()->id() ?? 1;
        $cacheKey = 'admin_last_read_order_id_' . $userId;

        $latestOrder = Order::latest('id')->first();
        $latestOrderId = $latestOrder ? $latestOrder->id : 0;

        // If not set in cache, initialize to orders from the last 24h
        if (!Cache::has($cacheKey)) {
            $firstOldId = Order::where('created_at', '<', now()->subHours(24))->latest('id')->value('id') ?? $latestOrderId;
            $lastReadId = $firstOldId;
            Cache::put($cacheKey, $lastReadId, now()->addDays(60));
        } else {
            $lastReadId = (int) Cache::get($cacheKey, $latestOrderId);
        }

        // Active new orders query:
        // Exclude stale unpaid orders older than 2 hours and orders older than 7 days
        $ordersQuery = Order::with('channel')
            ->where(function ($q) {
                $q->whereIn('order_status', ['awaiting', 'Awaiting', 'Created', 'created', 'approved', 'Approved', 'picking', 'scanning', 'readytoship'])
                  ->orWhere(function ($sub) {
                      $sub->whereIn('order_status', ['pending_payment', 'Pending_payment', 'pending'])
                          ->where('created_at', '>=', now()->subHours(2));
                  });
            })
            ->where('created_at', '>=', now()->subDays(7));

        $recentOrders = (clone $ordersQuery)->latest('id')->take(8)->get();
        $unreadOrdersCount = (clone $ordersQuery)->where('id', '>', $lastReadId)->count();

        // Pending quote requests from last 7 days
        $pendingQuotes = QuoteRequest::where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest('id')
            ->take(3)
            ->get();

        $notifications = collect();

        // Add quotes to notifications
        foreach ($pendingQuotes as $quote) {
            $notifications->push([
                'id' => 'quote_' . $quote->id,
                'title' => 'Teklif Talebi: #' . $quote->quote_no,
                'message' => $quote->customer_name . ' - ' . number_format($quote->estimated_total, 2, ',', '.') . ' ₺',
                'time' => $quote->created_at->diffForHumans(),
                'channel' => 'Teklif',
                'url' => route('admin.quotes.show', $quote->id),
                'is_new' => true,
                'is_quote' => true,
                'timestamp' => $quote->created_at->timestamp,
            ]);
        }

        // Add orders to notifications
        foreach ($recentOrders as $order) {
            $notifications->push([
                'id' => $order->id,
                'title' => 'Yeni Sipariş: #' . ($order->external_order_id ?? $order->id),
                'message' => $order->customer_name . ' - ' . number_format($order->total_price, 2, ',', '.') . ' ₺',
                'time' => $order->created_at ? $order->created_at->diffForHumans() : '-',
                'channel' => $order->channel->name ?? 'Web',
                'url' => route('admin.orders.show', $order->id),
                'is_new' => $order->id > $lastReadId,
                'is_quote' => false,
                'timestamp' => $order->created_at ? $order->created_at->timestamp : 0,
            ]);
        }

        // Sort notifications by newest first and take 8
        $sortedNotifications = $notifications->sortByDesc('timestamp')->values()->take(8);
        $totalUnread = $unreadOrdersCount + $pendingQuotes->count();

        return response()->json([
            'count' => $totalUnread,
            'latest_id' => $latestOrderId,
            'notifications' => $sortedNotifications,
        ]);
    }

    public function markAsRead(Request $request)
    {
        $userId = auth()->id() ?? 1;
        $latestOrder = Order::latest('id')->first();
        if ($latestOrder) {
            Cache::put('admin_last_read_order_id_' . $userId, $latestOrder->id, now()->addDays(60));
        }
        return response()->json(['success' => true]);
    }
}
