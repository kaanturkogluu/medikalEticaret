<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'old_price' => $this->old_price ? (float) $this->old_price : null,
            'stock' => $this->stock,
            'description' => $this->description,
            'brand' => $this->whenLoaded('brand', fn() => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'images' => $this->whenLoaded('productImages', fn() => $this->productImages->map(fn($img) => [
                'id' => $img->id,
                'image_url' => url('storage/' . ltrim($img->image_path, '/')),
                'is_primary' => $img->is_primary,
            ])),
            'created_at' => $this->created_at,
        ];
    }
}
