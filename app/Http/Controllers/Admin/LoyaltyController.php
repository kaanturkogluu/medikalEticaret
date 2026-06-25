<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoyaltyRule;
use App\Models\Setting;
use App\Models\User;

class LoyaltyController extends Controller
{
    public function index()
    {
        $rules = LoyaltyRule::orderBy('min_amount')->get();
        $rate = Setting::getValue('med_puan_rate', 1); // 1 Med Puan = 1 TL varsayılan
        $customers = User::where('role', 'user')->orderBy('name')->get();
        
        return view('admin.loyalty.index', compact('rules', 'rate', 'customers'));
    }

    public function storeRule(Request $request)
    {
        $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'points' => 'required|integer|min:1',
        ]);

        LoyaltyRule::create($request->all());

        return redirect()->back()->with('success', 'Sadakat kuralı eklendi.');
    }

    public function destroyRule($id)
    {
        LoyaltyRule::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Sadakat kuralı silindi.');
    }

    public function updateRate(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0.01'
        ]);

        Setting::setValue('med_puan_rate', $request->rate);

        return redirect()->back()->with('success', 'Med Puan TL değeri güncellendi.');
    }

    public function assignManual(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->med_puan += $request->points;
        $user->save();

        return redirect()->back()->with('success', "{$user->name} adlı müşteriye {$request->points} Med Puan eklendi.");
    }
}
