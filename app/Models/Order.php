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
        'payment_method', 'shipping_price', 'discount_amount', 'payment_token', 'shipping_company_id', 'tracking_code'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
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
        $status = strtolower(trim($this->order_status ?? ''));
        
        $map = [
            // Bekleyen / Yeni Siparişler
            'created'         => 'Yeni Sipariş',
            'awaiting'        => 'Yeni Sipariş',
            'approved'        => 'Yeni Sipariş',
            'pending_payment' => 'Ödeme Bekliyor',
            'unpaid'          => 'Ödenmedi',
            
            // Hazırlık ve Kargo Öncesi
            'preparing'       => 'Hazırlanıyor',
            'picking'         => 'Hazırlanıyor',
            'scanning'        => 'Hazırlanıyor',
            'readytoship'     => 'Hazırlanıyor',
            'kargo bekleniyor' => 'Hazırlanıyor',
            'kargoya hazır'   => 'Hazırlanıyor',
            
            // Kargo ve Teslimat
            'shipped'         => 'Kargoya Verildi',
            'delivered'       => 'Teslim Edildi',
            
            // İptal ve İade
            'cancelled'       => 'İptal Edildi',
            'iptal edildi'    => 'İptal Edildi',
            'kargo yapilmasi beklenmiyor' => 'İptal Edildi', // Veya 'Gönderim Yapılmayacak'
            'undeliveredandreturned' => 'İade Edildi',
            'returned'        => 'İade Edildi',
        ];

        return $map[$status] ?? ucfirst($status);
    }
}
