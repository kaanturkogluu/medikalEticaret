<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'channel_id', 'coupon_id', 'user_id', 'external_order_id', 'customer_name', 'customer_email', 'customer_phone',
        'total_price', 'order_date', 'currency', 'order_status', 'address_info', 'raw_marketplace_data', 'synced',
        'payment_method', 'shipping_price', 'discount_amount', 'payment_token'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    protected $casts = [
        'address_info' => 'array',
        'raw_marketplace_data' => 'array',
        'synced' => 'boolean',
        'order_date' => 'datetime'
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the translated status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $status = strtolower($this->order_status ?? '');
        
        $map = [
            'created'         => 'Onaylandı',
            'awaiting'        => 'Onay Bekliyor',
            'pending_payment' => 'Ödeme Bekliyor',
            'preparing'       => 'Hazırlanıyor',
            'picking'         => 'Toplanıyor',
            'approved'        => 'Onaylandı',
            'scanning'        => 'Okutuluyor',
            'shipped'         => 'Kargoya Verildi',
            'delivered'       => 'Teslim Edildi',
            'cancelled'       => 'İptal Edildi',
            'undeliveredandreturned' => 'İade Edildi',
            'returned'        => 'İade Edildi',
            'unpaid'          => 'Ödenmedi',
            'readytoship'      => 'Kargoya Hazır',
        ];

        return $map[$status] ?? ($this->order_status ?? 'İşleniyor');
    }
}
