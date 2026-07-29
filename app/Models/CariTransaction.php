<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CariTransaction extends Model
{
    protected $fillable = [
        'cari_account_id',
        'order_id',
        'type', // 'debit' = Borç / Satış, 'credit' = Alacak / Tahsilat
        'amount',
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function cariAccount(): BelongsTo
    {
        return $this->belongsTo(CariAccount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected static function booted(): void
    {
        static::saved(function (CariTransaction $transaction) {
            $transaction->cariAccount?->recalculateBalance();
        });

        static::deleted(function (CariTransaction $transaction) {
            $transaction->cariAccount?->recalculateBalance();
        });
    }
}
