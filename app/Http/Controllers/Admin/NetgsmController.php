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

        $result = $netgsmService->sendSms($request->phone, $request->message);

        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
