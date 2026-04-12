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
        $query = Product::with(['channelProducts.channel', 'brand', 'category', 'productImages']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

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
                'is_popular' => (bool)$product->is_popular,
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

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        
        return view('admin.products.create', [
            'brands' => $brands,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'marketplace_urls' => 'nullable|array',
        ]);

        if ($request->filled('brand_id')) {
            $brand = Brand::find($request->brand_id);
            if ($brand) {
                $validated['brand_name'] = $brand->name;
            }
        }

        if ($request->filled('category_id')) {
            $cat = \App\Models\Category::find($request->category_id);
            if ($cat) {
                $validated['category_name'] = $cat->name;
            }
        }

        $validated['active'] = $request->has('active');
        
        // Handle Marketplace URLs
        $validated['raw_marketplace_data'] = [
            'custom_urls' => $request->input('marketplace_urls', [])
        ];

        Product::create($validated);

        return redirect()->route('admin.products')->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    public function togglePopular(Product $product)
    {
        $product->is_popular = !$product->is_popular;
        $product->save();

        return response()->json([
            'success' => true,
            'is_popular' => $product->is_popular,
            'message' => $product->is_popular ? 'Ürün popüler ürünlere eklendi.' : 'Ürün popüler ürünlerden çıkarıldı.'
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
            'marketplace_urls' => 'nullable|array',
        ]);

        if ($request->filled('brand_id')) {
            $brand = Brand::find($request->brand_id);
            if ($brand) {
                $validated['brand_name'] = $brand->name;
            }
        }

        $validated['active'] = $request->has('active');

        // Handle Marketplace URLs
        $raw = $product->raw_marketplace_data ?? [];
        $raw['custom_urls'] = $request->input('marketplace_urls', []);
        $product->raw_marketplace_data = $raw;

        $product->update($validated);

        return redirect()->route('admin.products')->with('success', 'Ürün başarıyla güncellendi.');
    }
}
