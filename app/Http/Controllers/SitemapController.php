<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Page;
use App\Models\Brand;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('active', 1)->where('stock', '>', 0)->get();
        $categories = Category::all();
        $pages = Page::all();
        $brands = Brand::all();

        return response()->view('sitemap', compact('products', 'categories', 'pages', 'brands'))->header('Content-Type', 'text/xml');
    }

    public function akakce()
    {
        $products = Product::with(['category', 'brand', 'images'])
            ->where('active', 1)
            ->where('stock', '>', 0)
            ->get();

        return response()->view('akakce', compact('products'))->header('Content-Type', 'text/xml');
    }
}
