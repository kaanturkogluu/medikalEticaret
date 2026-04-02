<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelProduct extends Model
{
    protected $fillable = [
        'product_id', 'channel_id', 'external_id', 'price', 'stock', 'sync_status', 'sync_error', 'extra'
    ];

    protected $casts = [
        'extra' => 'array'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
