<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCompany extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tracking_url', 'active'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the tracking link for a specific tracking code.
     */
    public function getTrackingLink(string $code): string
    {
        if (!$this->tracking_url) {
            return '#';
        }

        return str_replace('[TRACKING_CODE]', $code, $this->tracking_url);
    }
}
