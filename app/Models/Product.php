<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'parent_id', 'variant_key', 'brand_id', 'category_id', 'return_template_id', 'sku', 'barcode', 'name', 'slug',
        'brand_name', 'category_name', 'description', 'price', 'stock', 'active', 'is_popular',
        'attributes', 'raw_marketplace_data', 'marketplace_status', 'marketplace',
        'external_id', 'platform_listing_id', 'product_content_id', 'supplier_id',
        'views'
    ];

    public function returnTemplate(): BelongsTo
    {
        return $this->belongsTo(ReturnTemplate::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (empty($product->slug) || $product->isDirty('name')) {
                $baseSlug = \Illuminate\Support\Str::slug($product->name);
                $skuSlug = \Illuminate\Support\Str::slug($product->sku);
                
                // Combine name and SKU for uniqueness and SEO
                $product->slug = $baseSlug . '-' . $skuSlug;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'active' => 'boolean',
        'is_popular' => 'boolean',
        'attributes' => 'array',
        'raw_marketplace_data' => 'array'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // Shorthand for service
    public function images() { return $this->productImages(); }
    public function attributes() { return $this->productAttributes(); }

    public function channelProducts(): HasMany
    {
        return $this->hasMany(ChannelProduct::class);
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'channel_products')
            ->withPivot(['external_id', 'price', 'stock', 'sync_status', 'sync_error', 'extra'])
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }
}
