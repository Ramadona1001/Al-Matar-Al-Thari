<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'referral_code',
        'referral_link',
        'points_earned',
        'is_used',
        'used_at',
        'referrer_id',
        'referee_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points_earned' => 'integer',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user who made the referral.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred.
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    /**
     * Check if referral is used.
     */
    public function isUsed(): bool
    {
        return $this->is_used;
    }

    /**
     * Check if referral is unused.
     */
    public function isUnused(): bool
    {
        return !$this->is_used;
    }

    /**
     * Use the referral.
     */
    public function useReferral(int $refereeId): bool
    {
        if ($this->is_used) {
            return false;
        }

        $this->update([
            'referee_id' => $refereeId,
            'is_used' => true,
            'used_at' => now(),
        ]);

        return true;
    }

    /**
     * Generate unique referral code.
     */
    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Generate referral link.
     */
    public function generateReferralLink(): string
    {
        return config('app.url') . '/register?ref=' . $this->referral_code;
    }

    /**
     * Award points to the referrer.
     */
    public function awardPoints(): bool
    {
        if (!$this->is_used || $this->points_earned <= 0) {
            return false;
        }

        // Create loyalty points record for the referrer
        LoyaltyPoint::create([
            'user_id' => $this->referrer_id,
            'points' => $this->points_earned,
            'type' => 'bonus',
            'source_type' => self::class,
            'source_id' => $this->id,
            'description' => 'Referral bonus for referring ' . ($this->referee->full_name ?? 'a new user'),
        ]);

        return true;
    }

    /**
     * Create a referral for a user.
     */
    public static function createForUser(int $userId, int $points = 0): self
    {
        $referralCode = self::generateUniqueReferralCode();
        
        return self::create([
            'referrer_id' => $userId,
            'referral_code' => $referralCode,
            'referral_link' => config('app.url') . '/register?ref=' . $referralCode,
            'points_earned' => $points,
            'is_used' => false,
        ]);
    }

    /**
     * Find referral by code.
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('referral_code', $code)->first();
    }

    /**
     * Get referral statistics for a user.
     */
    public static function getStatisticsForUser(int $userId): array
    {
        $totalReferrals = self::where('referrer_id', $userId)->count();
        $usedReferrals = self::where('referrer_id', $userId)->where('is_used', true)->count();
        $totalPoints = self::where('referrer_id', $userId)->where('is_used', true)->sum('points_earned');

        return [
            'total_referrals' => $totalReferrals,
            'used_referrals' => $usedReferrals,
            'pending_referrals' => $totalReferrals - $usedReferrals,
            'total_points_earned' => $totalPoints,
            'conversion_rate' => $totalReferrals > 0 ? round(($usedReferrals / $totalReferrals) * 100, 2) : 0,
        ];
    }

    /**
     * Scope a query to only include used referrals.
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    /**
     * Scope a query to only include unused referrals.
     */
    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    /**
     * Scope a query to only include referrals for a specific referrer.
     */
    public function scopeForReferrer($query, int $referrerId)
    {
        return $query->where('referrer_id', $referrerId);
    }

    /**
     * Scope a query to only include referrals for a specific referee.
     */
    public function scopeForReferee($query, int $refereeId)
    {
        return $query->where('referee_id', $refereeId);
    }
}