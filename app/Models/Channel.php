<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Channel extends Model
{
    protected $fillable = ['name', 'slug', 'settings', 'active'];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean'
    ];

    public function credential(): HasOne
    {
        return $this->hasOne(ChannelCredential::class);
    }

    public function channelProducts(): HasMany
    {
        return $this->hasMany(ChannelProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
