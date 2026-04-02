<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
