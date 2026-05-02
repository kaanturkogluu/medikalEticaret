<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'is_used',
        'user_id',
        'order_id',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'value' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    /**
     * Check if coupon is valid for use.
     */
    public function isValid(): bool
    {
        return !$this->is_used;
    }

    /**
     * Calculate discount for given cart items.
     */
    public function calculateDiscountForItems(array $items): float
    {
        $eligibleAmount = 0;
        $categoryIds = $this->categories->pluck('id')->toArray();

        foreach ($items as $item) {
            $isEligible = true;
            if (!empty($categoryIds)) {
                $product = Product::find($item['product_id']);
                if (!$product || !in_array($product->category_id, $categoryIds)) {
                    $isEligible = false;
                }
            }

            if ($isEligible) {
                $eligibleAmount += $item['price'] * $item['quantity'];
            }
        }

        if ($this->type === 'percent') {
            return ($eligibleAmount * $this->value) / 100;
        }

        return min($eligibleAmount, $this->value);
    }

    /**
     * Calculate discount for a given amount (Legacy/Simple).
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percent') {
            return ($amount * $this->value) / 100;
        }

        return min($amount, $this->value);
    }
}
