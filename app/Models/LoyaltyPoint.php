<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'points',
        'type',
        'source_type',
        'source_id',
        'description',
        'expiry_date',
        'redeemed_at',
        'user_id',
        'company_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'expiry_date' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the loyalty points.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the loyalty points.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if points are earned.
     */
    public function isEarned(): bool
    {
        return $this->type === 'earned';
    }

    /**
     * Check if points are redeemed.
     */
    public function isRedeemed(): bool
    {
        return $this->type === 'redeemed';
    }

    /**
     * Check if points are expired.
     */
    public function isExpired(): bool
    {
        return $this->type === 'expired' || 
               ($this->expiry_date && $this->expiry_date < now());
    }

    /**
     * Check if points are bonus.
     */
    public function isBonus(): bool
    {
        return $this->type === 'bonus';
    }

    /**
     * Check if points can be redeemed.
     */
    public function canBeRedeemed(): bool
    {
        return $this->type === 'earned' && 
               !$this->isExpired() && 
               $this->redeemed_at === null;
    }

    /**
     * Redeem the points.
     */
    public function redeem(): bool
    {
        if (!$this->canBeRedeemed()) {
            return false;
        }

        $this->update([
            'type' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        return true;
    }

    /**
     * Expire the points.
     */
    public function expire(): bool
    {
        if ($this->type !== 'earned' || $this->redeemed_at !== null) {
            return false;
        }

        $this->update([
            'type' => 'expired',
        ]);

        return true;
    }

    /**
     * Get source model.
     */
    public function getSource()
    {
        if (!$this->source_type || !$this->source_id) {
            return null;
        }

        $sourceClass = $this->source_type;
        if (class_exists($sourceClass)) {
            return $sourceClass::find($this->source_id);
        }

        return null;
    }

    /**
     * Scope a query to only include earned points.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope a query to only include redeemed points.
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    /**
     * Scope a query to only include expired points.
     */
    public function scopeExpired($query)
    {
        return $query->where('type', 'expired')
                     ->orWhere(function ($q) {
                         $q->where('expiry_date', '<', now())
                           ->where('type', 'earned')
                           ->whereNull('redeemed_at');
                     });
    }

    /**
     * Scope a query to only include active (non-expired) points.
     */
    public function scopeActive($query)
    {
        return $query->where('type', 'earned')
                     ->where(function ($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>', now());
                     })
                     ->whereNull('redeemed_at');
    }

    /**
     * Scope a query to only include points for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include points for a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Calculate total points for a user.
     */
    public static function calculateTotalForUser(int $userId): int
    {
        return self::forUser($userId)
                   ->active()
                   ->sum('points');
    }

    /**
     * Redeem points for a user.
     */
    public static function redeemPointsForUser(int $userId, int $points, string $description = null): bool
    {
        $availablePoints = self::calculateTotalForUser($userId);
        
        if ($availablePoints < $points) {
            return false;
        }

        // Create redemption record
        self::create([
            'user_id' => $userId,
            'points' => -$points,
            'type' => 'redeemed',
            'description' => $description ?? 'Points redeemed',
            'redeemed_at' => now(),
        ]);

        return true;
    }
}