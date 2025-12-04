<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedeemCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'points',
        'company_id',
        'card_id',
        'expires_at',
        'used_at',
        'used_by_user_id',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function loyaltyCard(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCard::class);
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeUnused($query)
    {
        return $query->whereNull('used_at');
    }

    public function isValid(): bool
    {
        if (!$this->active) return false;
        if ($this->used_at !== null) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    public function markUsed(int $userId): void
    {
        $this->used_by_user_id = $userId;
        $this->used_at = now();
        $this->active = false; // single use
        $this->save();
    }
}

