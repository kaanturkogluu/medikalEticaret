<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelCredential extends Model
{
    protected $fillable = ['channel_id', 'api_key', 'api_secret', 'supplier_id', 'extra', 'active'];

    protected $casts = [
        'extra' => 'array',
        'active' => 'boolean'
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
