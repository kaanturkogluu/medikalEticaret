<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'productImages'])
            ->where('active', true)
            ->where('stock', '>', 0);

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }
        
        $products = $query->paginate(24)->onEachSide(1)->withQueryString();
        // Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Brand Filter
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Price Filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->paginate(24)->onEachSide(1);
        
        $categories = Category::whereHas('products', function($q) {
            $q->where('active', true)->where('stock', '>', 0);
        })->withCount(['products' => function($q) {
            $q->where('active', true)->where('stock', '>', 0);
        }])->get();

        $brands = Brand::whereHas('products', function($q) {
            $q->where('active', true)->where('stock', '>', 0);
        })->withCount(['products' => function($q) {
            $q->where('active', true)->where('stock', '>', 0);
        }])->get();

        $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();

        $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();

        // Featured Brands
        $featuredBrands = Brand::where('is_featured', true)->where('active', true)->orderBy('name')->get();

        // Popular Products Settings
        $popularActive = \App\Models\Setting::getValue('popular_section_active', true);
        $popularMax = \App\Models\Setting::getValue('popular_section_max', 10);

        // Popular Products Query
        $popularProducts = collect();
        if ($popularActive) {
            $popularProducts = Product::with(['brand', 'productImages'])
                ->where('active', true)
                ->where(function($q) {
                    $q->where('is_popular', true)
                      ->orWhere('stock', '>', 0);
                })
                ->orderBy('is_popular', 'desc')
                ->orderByRaw('stock > 0 desc')
                ->orderBy('views', 'desc')
                ->take($popularMax)
                ->get();
        }

        // Recently Viewed Products
        $recentlyViewedIds = json_decode(request()->cookie('recently_viewed', '[]'), true);
        if (!is_array($recentlyViewedIds)) $recentlyViewedIds = [];
        
        $recentlyViewedProducts = collect();
        if (count($recentlyViewedIds) > 0) {
            $recentlyViewedProducts = Product::with(['brand', 'productImages'])
                ->whereIn('id', $recentlyViewedIds)
                ->where('active', true)
                ->where('stock', '>', 0)
                ->get()
                ->sortBy(function($p) use ($recentlyViewedIds) {
                    return array_search($p->id, $recentlyViewedIds);
                });
        }

        return view('home', compact('products', 'categories', 'brands', 'banners', 'popularProducts', 'featuredBrands', 'recentlyViewedProducts'));
    }

    public function show(Product $product, Request $request)
    {
        // IP Bazlı Görüntülenme Arttırma (Günde 1 kez)
        $ip = $request->ip();
        $cacheKey = "product_viewed_{$product->id}_{$ip}";
        
        if (!\Cache::has($cacheKey)) {
            $product->increment('views');
            \Cache::put($cacheKey, true, now()->addDay());
        }

        // Son Görüntülenenler (Cookie Bazlı)
        $recentlyViewed = request()->cookie('recently_viewed', '[]');
        $ids = json_decode($recentlyViewed, true);
        if (!is_array($ids)) $ids = [];
        
        // Mevcut ürünü listenin başına ekle, kopyaları kaldır
        if (($key = array_search($product->id, $ids)) !== false) {
            unset($ids[$key]);
        }
        array_unshift($ids, $product->id);
        
        // Limit: 10 ürün
        $ids = array_slice($ids, 0, 10);
        \Cookie::queue('recently_viewed', json_encode($ids), 60 * 24 * 30); // 30 Gün

        $product->load(['brand', 'category', 'productImages', 'productAttributes']);
        
        $relatedProducts = Product::with(['brand', 'productImages'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->orderByRaw('stock > 0 DESC')
            ->take(10)
            ->get();

        $categories = Category::whereHas('products', function($q) {
            $q->where('active', true)->where('stock', '>', 0);
        })->take(10)->get();

        return view('product_detail', compact('product', 'relatedProducts', 'categories'));
    }

    public function favorites()
    {
        return view('favorites');
    }

    public function page($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
