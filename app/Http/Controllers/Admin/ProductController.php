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
                'slug' => $product->slug,
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
            'sku' => 'required|string|max:100|unique:products,sku',
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
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
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

    public function printBarcode(Product $product)
    {
        return view('admin.products.print-barcode', compact('product'));
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $data = [];
        $headers = [];
        
        $content = file_get_contents($path);
        
        // Ensure UTF-8
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-9');
        }
        
        // Detect delimiter
        $firstLine = strtok($content, "\n");
        $delimiter = ',';
        if (strpos($firstLine, "\t") !== false) $delimiter = "\t";
        elseif (strpos($firstLine, ';') !== false) $delimiter = ';';

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $row = 0;
        while (($row_data = fgetcsv($stream, 0, $delimiter)) !== false) {
            if ($row === 0) {
                $headers = $row_data;
                $row++;
                continue;
            }
            
            if (empty(array_filter($row_data))) continue;
                
                $product = [
                    'name' => $row_data[0] ?? null,
                    'sku' => $row_data[1] ?? null,
                    'barcode' => $row_data[2] ?? null,
                    'price' => $row_data[3] ?? 0,
                    'stock' => $row_data[4] ?? 0,
                    'description' => $row_data[5] ?? null,
                    'brand_name' => $row_data[6] ?? null,
                    'category_name' => $row_data[7] ?? null,
                    'active' => isset($row_data[8]) ? (bool)$row_data[8] : true,
                    'is_popular' => isset($row_data[9]) ? (bool)$row_data[9] : false,
                    'free_shipping' => isset($row_data[10]) ? (bool)$row_data[10] : false,
                    'images' => []
                ];
                
                foreach ($headers as $index => $header) {
                    if (stripos(trim($header), 'resim') !== false && !empty($row_data[$index])) {
                        $url = $row_data[$index];
                        $url = str_replace('{size}', '960-1280', $url);
                        $product['images'][] = $url;
                    }
                }
                
                $data[] = $product;
                $row++;
        }
        fclose($stream);
        $importKey = 'import_' . uniqid();
        \Illuminate\Support\Facades\Cache::put($importKey, $data, now()->addHours(2));

        return response()->json([
            'success' => true,
            'import_key' => $importKey
        ]);
    }

    public function previewPage(Request $request)
    {
        $importKey = $request->query('key');
        if (!$importKey) {
            return redirect()->route('admin.products')->with('error', 'Geçersiz ön izleme anahtarı.');
        }

        $data = \Illuminate\Support\Facades\Cache::get($importKey);
        if (!$data) {
            return redirect()->route('admin.products')->with('error', 'Ön izleme süresi dolmuş veya geçersiz.');
        }

        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);
        $brands = \App\Models\Brand::orderBy('name')->get(['id', 'name']);

        $skus = collect($data)->pluck('sku')->filter()->toArray();
        $existingSkus = \App\Models\Product::whereIn('sku', $skus)->pluck('sku')->toArray();

        $mappedData = array_map(function($item) use ($categories, $brands, $existingSkus) {
            $catMatch = $categories->first(fn($c) => mb_strtolower((string)$c->name, 'UTF-8') === mb_strtolower((string)$item['category_name'], 'UTF-8'));
            $item['category_id'] = $catMatch ? $catMatch->id : null;

            $brandMatch = $brands->first(fn($b) => mb_strtolower((string)$b->name, 'UTF-8') === mb_strtolower((string)$item['brand_name'], 'UTF-8'));
            $item['brand_id'] = $brandMatch ? $brandMatch->id : null;
            
            $item['is_duplicate'] = !empty($item['sku']) && in_array($item['sku'], $existingSkus);

            return $item;
        }, $data);

        return view('admin.products.import-preview', [
            'importKey' => $importKey,
            'previewData' => $mappedData,
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'import_key' => 'required|string',
            'products' => 'required|array',
            'products.*.category_id' => 'nullable|exists:categories,id',
            'products.*.brand_id' => 'nullable|exists:brands,id',
        ]);

        $importKey = $request->input('import_key');
        $originalData = \Illuminate\Support\Facades\Cache::get($importKey);
        
        if (!$originalData) {
            return response()->json(['success' => false, 'message' => 'İçe aktarma süresi dolmuş veya geçersiz işlem.'], 400);
        }
        
        $productsInput = $request->input('products'); 

        $importedCount = 0;
        foreach ($originalData as $index => $item) {
            // Check if frontend sent this product (to handle skipping duplicates/unselected rows)
            if (!isset($productsInput[$index])) {
                continue;
            }

            $selectedInput = $productsInput[$index];
            
            $data = [
                'name' => $item['name'],
                'barcode' => $item['barcode'],
                'price' => (float)$item['price'],
                'stock' => (int)$item['stock'],
                'description' => $item['description'],
                'active' => $item['active'],
                'is_popular' => $item['is_popular'],
                'free_shipping' => $item['free_shipping'],
                'category_id' => $selectedInput['category_id'] ?? null,
                'brand_id' => $selectedInput['brand_id'] ?? null,
            ];

            if (!empty($item['sku'])) {
                $product = Product::updateOrCreate(
                    ['sku' => $item['sku']],
                    $data
                );
            } else {
                $data['sku'] = null;
                $product = Product::create($data);
            }

            // Update category_name and brand_name from relationships if IDs exist, or keep original from CSV
            if (!empty($selectedInput['category_id'])) {
                $product->category_name = \App\Models\Category::find($selectedInput['category_id'])?->name;
            } else {
                $product->category_name = $item['category_name'];
            }
            if (!empty($selectedInput['brand_id'])) {
                $product->brand_name = \App\Models\Brand::find($selectedInput['brand_id'])?->name;
            } else {
                $product->brand_name = $item['brand_name'];
            }
            $product->save();
            
            if (!empty($item['images'])) {
                foreach ($item['images'] as $i => $url) {
                    $product->productImages()->create([
                        'url' => $url,
                        'order' => $i + 1
                    ]);
                }
            }
            
            $importedCount++;
        }
        
        \Illuminate\Support\Facades\Cache::forget($importKey);

        return response()->json([
            'success' => true,
            'message' => $importedCount . ' adet ürün başarıyla içe aktarıldı.'
        ]);
    }
}
