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
        // 1. Sipariş Verenler (Misafir + Üye - Web Sitesi Siparişleri)
        $customerQuery = Order::forWebsite()
            ->whereNotNull('customer_email')
            ->where('customer_email', '!=', '')
            ->selectRaw('customer_email, MAX(customer_name) as customer_name, MAX(customer_phone) as customer_phone, MAX(user_id) as user_id, MAX(created_at) as last_order_date, COUNT(id) as total_orders, SUM(total_price) as total_spent')
            ->groupBy('customer_email');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $customerQuery->havingRaw('MAX(customer_name) LIKE ? OR customer_email LIKE ? OR MAX(customer_phone) LIKE ?', ["%$q%", "%$q%", "%$q%"]);
        }

        $customers = $customerQuery->orderByDesc('last_order_date')->paginate(15, ['*'], 'customers_page')->withQueryString();

        // 2. Sisteme Kayıtlı Üyeler
        $userQuery = \App\Models\User::withCount('orders');
        if ($request->filled('q')) {
            $q = trim($request->q);
            $userQuery->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%$q%")
                      ->orWhere('email', 'LIKE', "%$q%");
            });
        }
        $users = $userQuery->orderByDesc('created_at')->paginate(15, ['*'], 'users_page')->withQueryString();

        return view('admin.customers.index', compact('customers', 'users'));
    }
}
