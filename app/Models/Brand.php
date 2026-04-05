<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'active', 'is_featured'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($brand) {
            if (empty($brand->slug) || $brand->isDirty('name')) {
                $brand->slug = \Illuminate\Support\Str::slug($brand->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function channelBrands(): HasMany
    {
        return $this->hasMany(ChannelBrand::class);
    }
}
