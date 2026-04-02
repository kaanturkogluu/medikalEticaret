<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'brand_id', 'category_id', 'sku', 'barcode', 'name', 'brand_name', 'category_name', 
        'description', 'price', 'stock', 'active', 'attributes', 'raw_marketplace_data', 
        'marketplace_status', 'marketplace', 'external_id', 'platform_listing_id', 
        'product_content_id', 'supplier_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'attributes' => 'array',
        'raw_marketplace_data' => 'array'
    ];

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
}
