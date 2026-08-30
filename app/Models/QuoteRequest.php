<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    protected $fillable = [
        'quote_no',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'organization_name',
        'type',
        'customer_note',
        'estimated_total',
        'offered_total',
        'status',
        'admin_notes',
        'created_product_id',
        'custom_payment_link',
    ];

    protected $casts = [
        'estimated_total' => 'decimal:2',
        'offered_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'created_product_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteRequestItem::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Beklemede', 'icon' => 'fa-clock'],
            'reviewed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'İncelendi', 'icon' => 'fa-eye'],
            'offered' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Fiyat Verildi', 'icon' => 'fa-tag'],
            'converted' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Özel Ürün Hazırlandı', 'icon' => 'fa-box-check'],
            'completed' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'label' => 'Tamamlandı / Satın Alındı', 'icon' => 'fa-check-double'],
            'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'label' => 'İptal Edildi', 'icon' => 'fa-times'],
            default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => $this->status, 'icon' => 'fa-info-circle'],
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'bulk_order' => '📦 Toplu Alım (20+ Adet)',
            'donation' => '❤️ Bağış / Yardım Alımı',
            'corporate' => '🏢 Kurumsal / Firma Alımı',
            default => '🏷️ Genel Teklif Talebi',
        };
    }
}
