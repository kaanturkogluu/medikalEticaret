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

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
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
                'image' => $product->productImages->first() ? asset($product->productImages->first()->url) : null,
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
        $returnTemplates = \App\Models\ReturnTemplate::orderBy('name')->get();
        
        return view('admin.products.create', [
            'brands' => $brands,
            'categories' => $categories,
            'returnTemplates' => $returnTemplates
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
            'is_popular' => 'boolean',
            'free_shipping' => 'boolean',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'return_template_id' => 'nullable|exists:return_templates,id',
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
        $validated['is_popular'] = $request->has('is_popular');
        $validated['free_shipping'] = $request->has('free_shipping');
        
        // Handle Marketplace URLs
        $validated['raw_marketplace_data'] = [
            'custom_urls' => $request->input('marketplace_urls', [])
        ];

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->productImages()->create([
                    'url' => \Illuminate\Support\Facades\Storage::url($path),
                    'order' => ($product->productImages()->max('order') ?? 0) + 1
                ]);
            }
        }

        if ($request->has('attribute_names') && $request->has('attribute_values')) {
            $names = $request->input('attribute_names');
            $values = $request->input('attribute_values');
            foreach ($names as $index => $name) {
                if (!empty($name) && !empty($values[$index])) {
                    $product->productAttributes()->create([
                        'attribute_name' => $name,
                        'attribute_value' => $values[$index]
                    ]);
                }
            }
        }

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
        $categories = \App\Models\Category::orderBy('name')->get();
        $returnTemplates = \App\Models\ReturnTemplate::orderBy('name')->get();
        
        return view('admin.products.edit', [
            'product' => $product,
            'brands' => $brands,
            'categories' => $categories,
            'returnTemplates' => $returnTemplates
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
            'category_id' => 'nullable|exists:categories,id',
            'return_template_id' => 'nullable|exists:return_templates,id',
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
        $validated['is_popular'] = $request->has('is_popular');
        $validated['free_shipping'] = $request->has('free_shipping');

        // Handle Marketplace URLs
        $raw = $product->raw_marketplace_data ?? [];
        $raw['custom_urls'] = $request->input('marketplace_urls', []);
        $product->raw_marketplace_data = $raw;

        $product->update($validated);

        // Handle image deletions
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = \App\Models\ProductImage::find($imageId);
                if ($image && $image->product_id == $product->id) {
                    // If it's a local file, delete it
                    if (str_contains($image->url, '/storage/')) {
                        $path = explode('/storage/', $image->url)[1] ?? null;
                        if ($path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                        }
                    }
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->productImages()->create([
                    'url' => \Illuminate\Support\Facades\Storage::url($path),
                    'order' => ($product->productImages()->max('order') ?? 0) + 1
                ]);
            }
        }

        return redirect()->route('admin.products')->with('success', 'Ürün başarıyla güncellendi.');
    }

    public function destroy(Product $product)
    {
        $product->delete(); // Soft delete
        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla silindi.'
        ]);
    }
}
