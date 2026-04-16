<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUpdates()
    {
        // For demonstration, we treat 'awaiting' status as new/unread notifications
        $newOrders = Order::with('channel')
            ->whereIn('order_status', ['awaiting', 'Awaiting', 'pending_payment', 'Pending_payment', 'Created', 'created'])
            ->latest()
            ->take(5)
            ->get();

        $count = Order::whereIn('order_status', ['awaiting', 'Awaiting', 'pending_payment', 'Pending_payment', 'Created', 'created'])->count();

        return response()->json([
            'count' => $count,
            'latest_id' => $newOrders->first()?->id ?? 0,
            'notifications' => $newOrders->map(function($order) {
                return [
                    'id' => $order->id,
                    'title' => 'Yeni Sipariş: #' . ($order->external_order_id ?? $order->id),
                    'message' => $order->customer_name . ' - ' . number_format($order->total_price, 2) . ' ₺',
                    'time' => $order->created_at->diffForHumans(),
                    'channel' => $order->channel->name ?? 'Web',
                    'url' => '/admin/orders'
                ];
            })
        ]);
    }
}
