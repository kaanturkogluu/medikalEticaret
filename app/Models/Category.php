<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'active', 'external_id', 'is_navbar'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug) || $category->isDirty('name')) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'active' => 'boolean',
        'is_navbar' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function channelCategories(): HasMany
    {
        return $this->hasMany(ChannelCategory::class);
    }
}
