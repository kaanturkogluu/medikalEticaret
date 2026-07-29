<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CariAccount extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'name',
        'email',
        'phone',
        'tax_number',
        'tax_office',
        'address',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CariTransaction::class)->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
    }

    public function recalculateBalance(): void
    {
        $debits = $this->transactions()->where('type', 'debit')->sum('amount');
        $credits = $this->transactions()->where('type', 'credit')->sum('amount');
        
        $this->update(['balance' => $debits - $credits]);
    }
}
