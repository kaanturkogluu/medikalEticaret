<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUpdates()
    {
        $lastReadId = \Illuminate\Support\Facades\Cache::get('admin_last_read_order_id_' . auth()->id(), 0);

        // For demonstration, we treat 'awaiting' status as new/unread notifications
        $newOrders = Order::with('channel')
            ->whereIn('order_status', ['awaiting', 'Awaiting', 'pending_payment', 'Pending_payment', 'Created', 'created'])
            ->latest()
            ->take(5)
            ->get();

        $count = Order::whereIn('order_status', ['awaiting', 'Awaiting', 'pending_payment', 'Pending_payment', 'Created', 'created'])
            ->where('id', '>', $lastReadId)
            ->count();

        return response()->json([
            'count' => $count,
            'latest_id' => $newOrders->first()?->id ?? 0,
            'notifications' => $newOrders->map(function($order) use ($lastReadId) {
                return [
                    'id' => $order->id,
                    'title' => 'Yeni Sipariş: #' . ($order->external_order_id ?? $order->id),
                    'message' => $order->customer_name . ' - ' . number_format($order->total_price, 2) . ' ₺',
                    'time' => $order->created_at->diffForHumans(),
                    'channel' => $order->channel->name ?? 'Web',
                    'url' => '/admin/orders',
                    'is_new' => $order->id > $lastReadId,
                ];
            })
        ]);
    }

    public function markAsRead(Request $request)
    {
        $latestOrder = Order::latest()->first();
        if ($latestOrder) {
            \Illuminate\Support\Facades\Cache::put('admin_last_read_order_id_' . auth()->id(), $latestOrder->id, now()->addDays(30));
        }
        return response()->json(['success' => true]);
    }
}
