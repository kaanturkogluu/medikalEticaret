<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['channelProducts.channel', 'brand', 'category', 'productImages'])
            ->latest()
            ->paginate(15);

        // Map products for Alpine format
        $mappedProducts = $products->getCollection()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => (float)$product->price,
                'stock' => (int)$product->stock,
                'status' => 'synced', // simplified for now
                'marketplaces' => $product->channelProducts->map(fn($cp) => $cp->channel->name)->toArray(),
                'image' => $product->productImages->first()?->url ?? null,
            ];
        });

        // Replace collection with mapped data
        $products->setCollection($mappedProducts);

        return view('admin.products', [
            'products' => $products
        ]);
    }

    public function edit(Product $product)
    {
        $product->load(['brand', 'category', 'productImages', 'productAttributes', 'channelProducts.channel']);
        $brands = Brand::orderBy('name')->get();
        
        return view('admin.products.edit', [
            'product' => $product,
            'brands' => $brands
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        if ($request->filled('brand_id')) {
            $brand = Brand::find($request->brand_id);
            if ($brand) {
                $validated['brand_name'] = $brand->name;
            }
        }

        $validated['active'] = $request->has('active');

        $product->update($validated);

        return redirect()->route('admin.products')->with('success', 'Ürün başarıyla güncellendi.');
    }
}
