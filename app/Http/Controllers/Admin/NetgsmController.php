<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NetgsmService;

class NetgsmController extends Controller
{
    /**
     * Display Netgsm integration status and test form.
     */
    public function index()
    {
        $hasCredentials = !empty(config('services.netgsm.usercode')) && !empty(config('services.netgsm.password'));
        
        return view('admin.netgsm.index', compact('hasCredentials'));
    }

    /**
     * Send a test SMS
     */
    public function test(Request $request, NetgsmService $netgsmService)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160'
        ]);

        $result = $netgsmService->sendSms($request->phone, $request->message, 'Test Paneli', 'Admin');

        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Display SMS History logs
     */
    public function history()
    {
        $logs = \App\Models\SmsLog::orderByDesc('created_at')->paginate(20);
        return view('admin.netgsm.history', compact('logs'));
    }

    /**
     * Display bulk SMS page with customer list
     */
    public function bulk(NetgsmService $netgsmService)
    {
        // Get Netgsm balance
        $balanceResult = $netgsmService->checkBalance(3);

        // Siparişleri ürünleriyle çekelim
        $orders = \App\Models\Order::with('items.product')
            ->whereNotNull('customer_phone')
            ->where('customer_phone', '!=', '')
            // İsterseniz sadece başarılı siparişleri getirebilirsiniz. Şimdilik iptal harici diyelim.
            ->whereNotIn('order_status', ['Cancelled', 'Returned'])
            ->get();

        $customers = [];

        foreach ($orders as $order) {
            $phone = trim($order->customer_phone);
            // Numarayı gruplama için basitçe temizleyelim
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (empty($phone)) continue;

            if (!isset($customers[$phone])) {
                $customers[$phone] = [
                    'name' => $order->customer_name,
                    'phone' => $phone,
                    'products' => []
                ];
            }

            // Siparişteki ürün isimlerini topla
            foreach ($order->items as $item) {
                if ($item->product) {
                    $customers[$phone]['products'][$item->product->id] = $item->product->name;
                }
            }
        }

        // Ürünleri virgüle ayırıp string yapalım
        foreach ($customers as &$customer) {
            $customer['products'] = implode(', ', array_unique($customer['products']));
        }

        return view('admin.netgsm.bulk', compact('customers', 'balanceResult'));
    }

    /**
     * Send bulk SMS to selected customers
     */
    public function sendBulk(Request $request, NetgsmService $netgsmService)
    {
        $request->validate([
            'phones' => 'required|array|min:1',
            'names' => 'nullable|array', // We will pass names from the view to log them properly
            'message' => 'required|string|max:912'
        ]);

        $names = $request->input('names', []);
        
        $result = $netgsmService->sendSms($request->phones, $request->message, 'Toplu Gönderim (Kampanya)', $names);

        if ($result['status']) {
            return redirect()->back()->with('success', 'Toplu SMS başarıyla API\'ye iletildi. Görev ID: ' . ($result['bulk_id'] ?? '-'));
        } else {
            return redirect()->back()->with('error', 'Toplu SMS gönderilemedi: ' . $result['message']);
        }
    }
}
