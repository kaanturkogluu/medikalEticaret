<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers from website orders.
     */
    public function index(Request $request): View
    {
        // channel_id null olanlar site siparişleridir
        $query = Order::whereNull('channel_id')
            ->selectRaw('customer_email, MAX(customer_name) as customer_name, MAX(customer_phone) as customer_phone, MAX(user_id) as user_id, MAX(created_at) as last_order_date, COUNT(id) as total_orders, SUM(total_price) as total_spent')
            ->whereNotNull('customer_email')
            ->groupBy('customer_email');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->havingRaw('customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?', ["%$q%", "%$q%", "%$q%"]);
        }

        // We use simplePaginate or get() because groupBy with paginate() sometimes counts incorrectly.
        // To safely paginate a grouped query:
        $customers = $query->orderByDesc('last_order_date')->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }
}
