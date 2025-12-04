<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $table = 'loyalty_transactions';

    protected $fillable = [
        'user_id',
        'company_id',
        'card_id',
        'type',
        'points',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function loyaltyCard(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCard::class);
    }

    // Scopes
    public function scopeEarn($query)
    {
        return $query->where('type', 'earn');
    }

    public function scopeRedeem($query)
    {
        return $query->where('type', 'redeem');
    }

    public function scopeTransfer($query)
    {
        return $query->where('type', 'transfer');
    }

    public function scopeRevert($query)
    {
        return $query->where('type', 'revert');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCard($query, int $cardId)
    {
        return $query->where('card_id', $cardId);
    }
}

