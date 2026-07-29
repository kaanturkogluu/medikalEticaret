<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CariAccount;
use App\Models\CariTransaction;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CariController extends Controller
{
    /**
     * Display listing of Web Sales, Time Filtering, Chart Data, and Customer Accounts.
     */
    public function index(Request $request)
    {
        // 1. Determine Date Range Filter
        $period = $request->get('period', 'this_month');
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::now()->subDay()->startOfDay();
                $endDate = Carbon::now()->subDay()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                }
                if ($request->filled('end_date')) {
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
            case 'all':
            default:
                // No date restriction
                break;
        }

        // 2. Base Query for Website Sales Orders (channel_id is null or 5)
        $ordersBase = Order::where(function($q) {
            $q->whereNull('channel_id')->orWhere('channel_id', 5);
        });

        if ($startDate) {
            $ordersBase->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $ordersBase->where('created_at', '<=', $endDate);
        }

        // 3. KPI Statistics
        $totalGrossSales = (clone $ordersBase)->sum('total_price');
        $totalNetSales   = (clone $ordersBase)->where('is_paid', true)->whereNotIn('order_status', ['cancelled', 'Cancelled'])->sum(DB::raw('COALESCE(paid_price, total_price)'));
        $totalCancelled  = (clone $ordersBase)->whereIn('order_status', ['cancelled', 'Cancelled'])->sum('total_price');
        $totalOrderCount = (clone $ordersBase)->count();

        $stats = [
            'total_gross_sales' => $totalGrossSales,
            'total_net_sales'   => $totalNetSales,
            'total_cancelled'   => $totalCancelled,
            'total_order_count' => $totalOrderCount,
        ];

        // 4. Daily Chart Data
        $chartQuery = Order::where(function($q) {
            $q->whereNull('channel_id')->orWhere('channel_id', 5);
        });
        if ($startDate) {
            $chartQuery->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $chartQuery->where('created_at', '<=', $endDate);
        }

        $rawChartData = $chartQuery->select(
            DB::raw('DATE(created_at) as date_val'),
            DB::raw('SUM(total_price) as gross'),
            DB::raw('SUM(CASE WHEN is_paid = 1 AND LOWER(COALESCE(order_status, "")) NOT IN ("cancelled", "iptal") THEN COALESCE(paid_price, total_price) ELSE 0 END) as net'),
            DB::raw('SUM(CASE WHEN LOWER(COALESCE(order_status, "")) IN ("cancelled", "iptal") THEN total_price ELSE 0 END) as cancelled')
        )
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy(DB::raw('DATE(created_at)'), 'asc')
        ->get();

        $chartLabels        = [];
        $chartGrossData     = [];
        $chartNetData       = [];
        $chartCancelledData = [];

        // Build continuous day-by-day map for the selected period
        $map = [];
        $start = $startDate ? $startDate->copy() : Carbon::now()->subDays(14);
        $end   = $endDate ? $endDate->copy() : Carbon::now();

        // If span is less than 6 days (e.g. today/yesterday), expand start date to 6 days ago for a nice curve
        if ($start->diffInDays($end) < 6) {
            $start = $end->copy()->subDays(6);
        }

        $current = $start->copy()->startOfDay();
        $endLimit = $end->copy()->endOfDay();

        while ($current->lte($endLimit)) {
            $dKey = $current->format('Y-m-d');
            $map[$dKey] = [
                'label'     => $current->format('d.m'),
                'gross'     => 0,
                'net'       => 0,
                'cancelled' => 0,
            ];
            $current->addDay();
        }

        foreach ($rawChartData as $row) {
            $dKey = Carbon::parse($row->date_val)->format('Y-m-d');
            if (isset($map[$dKey])) {
                $map[$dKey]['gross']     = round((float) $row->gross, 2);
                $map[$dKey]['net']       = round((float) $row->net, 2);
                $map[$dKey]['cancelled'] = round((float) $row->cancelled, 2);
            } else {
                $map[$dKey] = [
                    'label'     => Carbon::parse($row->date_val)->format('d.m'),
                    'gross'     => round((float) $row->gross, 2),
                    'net'       => round((float) $row->net, 2),
                    'cancelled' => round((float) $row->cancelled, 2),
                ];
            }
        }

        ksort($map);

        foreach ($map as $item) {
            $chartLabels[]        = $item['label'];
            $chartGrossData[]     = $item['gross'];
            $chartNetData[]       = $item['net'];
            $chartCancelledData[] = $item['cancelled'];
        }

        $chartData = [
            'labels'    => $chartLabels,
            'gross'     => $chartGrossData,
            'net'       => $chartNetData,
            'cancelled' => $chartCancelledData,
        ];

        // 5. Customer Web Sales List (Cari Accounts)
        $accountsQuery = CariAccount::withCount(['transactions as total_sales_count' => function ($q) {
            $q->where('type', 'debit');
        }]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $accountsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $cariler = $accountsQuery->orderBy('name', 'asc')->paginate(20)->withQueryString();

        return view('admin.cariler.index', compact('cariler', 'stats', 'chartData', 'period', 'startDate', 'endDate'));
    }

    /**
     * Show Cari Account details and chronological Web Sales/Cancel transactions.
     */
    public function show($id)
    {
        $cari = CariAccount::with(['transactions.order', 'user'])->findOrFail($id);

        return view('admin.cariler.show', compact('cari'));
    }

    /**
     * Synchronize all website sales into Cari Accounts and Transactions.
     */
    public function syncWebOrders()
    {
        $webOrders = Order::where(function($q) {
            $q->whereNull('channel_id')->orWhere('channel_id', 5);
        })->get();
        $count = 0;

        foreach ($webOrders as $order) {
            if (self::syncOrder($order)) {
                $count++;
            }
        }

        return redirect()->route('admin.cariler.index')->with('success', "{$count} adet web siparişi cari satış kayıtlarına senkronize edildi.");
    }

    /**
     * Static helper to sync a single Web Order into the Cari system.
     */
    public static function syncOrder(Order $order): bool
    {
        if (!empty($order->channel_id) && $order->channel_id != 5) {
            return false;
        }

        $cariAccount = null;

        if ($order->user_id) {
            $cariAccount = CariAccount::where('user_id', $order->user_id)->first();
        }

        if (!$cariAccount && !empty($order->customer_email)) {
            $cariAccount = CariAccount::where('email', $order->customer_email)->first();
        }

        if (!$cariAccount && !empty($order->customer_phone)) {
            $cariAccount = CariAccount::where('phone', $order->customer_phone)->first();
        }

        if (!$cariAccount) {
            $nextId = (CariAccount::max('id') ?? 0) + 1;
            $code = 'CARI-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $cariAccount = CariAccount::create([
                'user_id' => $order->user_id,
                'code'    => $code,
                'name'    => $order->customer_name ?: 'Web Müşterisi',
                'email'   => $order->customer_email,
                'phone'   => $order->customer_phone,
                'address' => is_array($order->address_info) ? ($order->address_info['address'] ?? null) : ($order->address_info ?? null),
            ]);
        }

        $isCancelled = in_array(strtolower(trim($order->order_status)), ['cancelled', 'iptal']);
        $isPaid      = $order->is_paid || !empty($order->iyzico_payment_id);

        // 1. Web Sales Record (debit)
        $existingSale = CariTransaction::where('cari_account_id', $cariAccount->id)
            ->where('order_id', $order->id)
            ->where('type', 'debit')
            ->first();

        if (!$existingSale && $order->total_price > 0) {
            CariTransaction::create([
                'cari_account_id'  => $cariAccount->id,
                'order_id'         => $order->id,
                'type'             => 'debit',
                'amount'           => $order->total_price,
                'description'      => 'Web Sipariş Satışı #' . ($order->external_order_id ?? $order->id),
                'transaction_date' => $order->order_date ?? $order->created_at ?? now(),
            ]);
        }

        // 2. Cancellation / Refund Record (credit) if cancelled
        if ($isCancelled) {
            $existingCancel = CariTransaction::where('cari_account_id', $cariAccount->id)
                ->where('order_id', $order->id)
                ->where('type', 'credit')
                ->first();

            if (!$existingCancel) {
                CariTransaction::create([
                    'cari_account_id'  => $cariAccount->id,
                    'order_id'         => $order->id,
                    'type'             => 'credit',
                    'amount'           => $order->total_price,
                    'description'      => 'Sipariş İptal / İade #' . ($order->external_order_id ?? $order->id),
                    'transaction_date' => $order->canceled_at ?? now(),
                ]);
            }
        }

        return true;
    }
}
