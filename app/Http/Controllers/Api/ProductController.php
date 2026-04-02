<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected SyncService $syncService) {}

    public function index(): JsonResponse
    {
        return response()->json(Product::with(['brand', 'category', 'channelProducts'])->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load(['brand', 'category', 'channelProducts']));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $oldStock = $product->stock;
        $oldPrice = $product->price;

        $product->update($data);

        // Sync if stock or price changed
        if (isset($data['stock']) && $data['stock'] != $oldStock) {
            $this->syncService->syncProductStock($product);
        }

        if (isset($data['price']) && $data['price'] != $oldPrice) {
            $this->syncService->syncProductPrice($product);
        }

        return response()->json($product);
    }
}
