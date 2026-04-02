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
            ->where('active', true);

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

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
        
        $categories = Category::whereHas('products')->withCount('products')->get();
        $brands = Brand::whereHas('products')->withCount('products')->get();

        return view('home', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'productImages', 'productAttributes']);
        
        $relatedProducts = Product::with(['brand', 'productImages'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(10)
            ->get();

        $categories = Category::whereHas('products')->take(10)->get();

        return view('product_detail', compact('product', 'relatedProducts', 'categories'));
    }

    public function favorites()
    {
        return view('favorites');
    }
}
