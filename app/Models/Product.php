<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'variant_key', 'brand_id', 'category_id', 'return_template_id', 'sku', 'barcode', 'name', 'slug',
        'brand_name', 'category_name', 'description', 'video_url', 'price', 'stock', 'active', 'is_popular', 'free_shipping', 'eft_discount',
        'attributes', 'raw_marketplace_data', 'marketplace_status', 'marketplace',
        'external_id', 'platform_listing_id', 'product_content_id', 'supplier_id',
        'views'
    ];

    /**
     * Extract 11-character YouTube Video ID.
     */
    public function getYoutubeVideoIdAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // If user pasted the raw 11-char ID directly
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        // Match video ID from iframe src, watch?v=, youtu.be/, embed/, shorts/, mobile URLs, etc.
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/#\s]{11})/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Convert YouTube URL to YouTube Embed URL if valid.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_video_id;
        return $id ? "https://www.youtube.com/embed/{$id}?rel=0&enablejsapi=1" : null;
    }

    /**
     * Direct YouTube watch link (fallback).
     */
    public function getYoutubeWatchUrlAttribute(): ?string
    {
        $id = $this->youtube_video_id;
        return $id ? "https://www.youtube.com/watch?v={$id}" : (trim($this->video_url) ?: null);
    }

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

            // Otomatik stok kontrolü: Stok 0 veya daha az ise satışı (active) kapat
            if ($product->stock <= 0) {
                $product->active = false;
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
        'free_shipping' => 'boolean',
        'eft_discount' => 'boolean',
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

    public function getEarnedPointsAttribute()
    {
        static $rules = null;
        if ($rules === null) {
            $rules = \App\Models\LoyaltyRule::orderBy('min_amount')->get();
        }

        $price = $this->price;
        foreach ($rules as $rule) {
            $min = (float) $rule->min_amount;
            $maxAmt = (float) $rule->max_amount;
            $max = $maxAmt > 0 ? $maxAmt : 9999999;
            if ($price >= $min && $price <= $max) {
                return $rule->points;
            }
        }
        return 0;
    }
}
