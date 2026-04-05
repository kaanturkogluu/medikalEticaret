<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount('products');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $brands = $query->latest()->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->except('logo');
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $data['active'] = $request->has('active');
        $data['is_featured'] = $request->has('is_featured');

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marka başarıyla eklendi.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $data['active'] = $request->has('active');
        $data['is_featured'] = $request->has('is_featured');

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Marka başarıyla güncellendi.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Bu markaya tanımlı ürünler olduğu için silemezsiniz.');
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Marka başarıyla silindi.');
    }

    public function toggleActive(Brand $brand)
    {
        $brand->update(['active' => !$brand->active]);
        return back()->with('success', 'Marka durumu güncellendi.');
    }

    public function toggleFeatured(Brand $brand)
    {
        $brand->update(['is_featured' => !$brand->is_featured]);
        return back()->with('success', 'Marka öne çıkarılma durumu güncellendi.');
    }
}
