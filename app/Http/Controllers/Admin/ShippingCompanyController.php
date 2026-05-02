<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    public function index()
    {
        $companies = ShippingCompany::all();
        return view('admin.shipping_companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tracking_url' => 'nullable|string|max:500',
        ]);

        ShippingCompany::create([
            'name' => $request->name,
            'tracking_url' => $request->tracking_url,
            'active' => true
        ]);

        return back()->with('success', 'Kargo firması başarıyla eklendi.');
    }

    public function update(Request $request, ShippingCompany $shipping_company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tracking_url' => 'nullable|string|max:500',
        ]);

        $shipping_company->update([
            'name' => $request->name,
            'tracking_url' => $request->tracking_url,
        ]);

        return back()->with('success', 'Kargo firması güncellendi.');
    }

    public function destroy(ShippingCompany $shipping_company)
    {
        // Check if company is in use
        if ($shipping_company->orders()->count() > 0) {
            return back()->with('error', 'Bu kargo firmasına ait siparişler bulunduğu için silinemez. Durumunu pasif yapabilirsiniz.');
        }

        $shipping_company->delete();
        return back()->with('success', 'Kargo firması silindi.');
    }

    public function toggleActive(ShippingCompany $shipping_company)
    {
        $shipping_company->update(['active' => !$shipping_company->active]);
        return back()->with('success', 'Kargo firması durumu güncellendi.');
    }
}
