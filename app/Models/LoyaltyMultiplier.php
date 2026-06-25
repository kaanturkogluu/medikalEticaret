<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyMultiplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_days',
        'order_count',
        'multiplier'
    ];
}
