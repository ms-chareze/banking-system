<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
        'minimum_balance',
        'low_balance_notified',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'minimum_balance' => 'decimal:2',
            'low_balance_notified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}