<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'logo', 'active'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function channelBrands(): HasMany
    {
        return $this->hasMany(ChannelBrand::class);
    }
}
