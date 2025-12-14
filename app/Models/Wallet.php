<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loyalty_points_balance',
        'affiliate_points_balance',
        'loyalty_points_pending',
        'affiliate_points_pending',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get total loyalty points (balance + pending).
     */
    public function getTotalLoyaltyPointsAttribute(): int
    {
        return $this->loyalty_points_balance + $this->loyalty_points_pending;
    }

    /**
     * Get total affiliate points (balance + pending).
     */
    public function getTotalAffiliatePointsAttribute(): int
    {
        return $this->affiliate_points_balance + $this->affiliate_points_pending;
    }

    /**
     * Add loyalty points (pending).
     */
    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points_pending', $points);
    }

    /**
     * Add affiliate points (pending).
     */
    public function addAffiliatePoints(int $points): void
    {
        $this->increment('affiliate_points_pending', $points);
    }

    /**
     * Approve pending loyalty points (converts from pending to balance).
     */
    public function approveLoyaltyPoints(int $points): void
    {
        $this->decrement('loyalty_points_pending', $points);
        $this->increment('loyalty_points_balance', $points);
    }

    /**
     * Add loyalty points directly to balance (no pending period).
     */
    public function addLoyaltyPointsDirectly(int $points): void
    {
        $this->increment('loyalty_points_balance', $points);
    }

    /**
     * Approve pending affiliate points.
     */
    public function approveAffiliatePoints(int $points): void
    {
        $this->decrement('affiliate_points_pending', $points);
        $this->increment('affiliate_points_balance', $points);
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemLoyaltyPoints(int $points): bool
    {
        if ($this->loyalty_points_balance < $points) {
            return false;
        }

        $this->decrement('loyalty_points_balance', $points);
        return true;
    }

    /**
     * Redeem affiliate points.
     * Only allows redemption of approved points (not pending or locked).
     */
    public function redeemAffiliatePoints(int $points): bool
    {
        // Check if user has pending or locked affiliate transactions
        $hasPendingOrLocked = WalletTransaction::where('wallet_id', $this->id)
            ->where('type', 'affiliate')
            ->whereIn('status', ['pending', 'locked'])
            ->exists();

        if ($hasPendingOrLocked) {
            // Points cannot be redeemed while there are pending or locked transactions
            return false;
        }

        if ($this->affiliate_points_balance < $points) {
            return false;
        }

        $this->decrement('affiliate_points_balance', $points);
        return true;
    }

    /**
     * Reverse loyalty points.
     */
    public function reverseLoyaltyPoints(int $points): void
    {
        if ($this->loyalty_points_balance >= $points) {
            $this->decrement('loyalty_points_balance', $points);
        } else {
            $remaining = $points - $this->loyalty_points_balance;
            $this->loyalty_points_balance = 0;
            $this->decrement('loyalty_points_pending', $remaining);
        }
    }

    /**
     * Reverse affiliate points.
     */
    public function reverseAffiliatePoints(int $points): void
    {
        if ($this->affiliate_points_balance >= $points) {
            $this->decrement('affiliate_points_balance', $points);
        } else {
            $remaining = $points - $this->affiliate_points_balance;
            $this->affiliate_points_balance = 0;
            $this->decrement('affiliate_points_pending', $remaining);
        }
    }
}
