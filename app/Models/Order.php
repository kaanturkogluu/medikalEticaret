<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'channel_id', 'coupon_id', 'user_id', 'external_order_id', 'customer_name', 'customer_email', 'customer_phone',
        'total_price', 'order_date', 'currency', 'order_status', 'address_info', 'invoice_info', 'invoice_file', 'raw_marketplace_data', 'synced',
        'payment_method', 'shipping_price', 'discount_amount', 'payment_token', 'shipping_company_id', 'tracking_code',
        'earned_points', 'used_points', 'used_points_discount', 'iyzico_payment_id', 'installment', 'card_family', 'paid_price', 'iyzico_fee', 'is_paid', 'canceled_at', 'cancel_reason'
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
        'invoice_info' => 'array',
        'raw_marketplace_data' => 'array',
        'synced' => 'boolean',
        'order_date' => 'datetime',
        'canceled_at' => 'datetime'
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

    /**
     * Format any phone number into 0(XXX) - XXX - XX-XX format.
     */
    public static function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Clean non-digits
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // Normalize country codes & leading zeros
        if (str_starts_with($digits, '0090') && strlen($digits) === 14) {
            $digits = substr($digits, 4);
        } elseif (str_starts_with($digits, '090') && strlen($digits) === 13) {
            $digits = substr($digits, 3);
        } elseif (str_starts_with($digits, '90') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            $part1 = substr($digits, 0, 3);
            $part2 = substr($digits, 3, 3);
            $part3 = substr($digits, 6, 2);
            $part4 = substr($digits, 8, 2);

            return "{$part1} {$part2} {$part3} {$part4}";
        }

        return trim($phone);
    }

    /**
     * Accessor for formatted customer phone number.
     */
    public function getFormattedCustomerPhoneAttribute(): ?string
    {
        return self::formatPhoneNumber($this->customer_phone);
    }
}
