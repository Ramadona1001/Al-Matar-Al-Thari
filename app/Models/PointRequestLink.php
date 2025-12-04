<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointRequestLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_id',
        'card_id',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function loyaltyCard(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCard::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function isActive(): bool
    {
        if (!$this->active) return false;
        return is_null($this->expires_at) || $this->expires_at->isFuture();
    }
}

