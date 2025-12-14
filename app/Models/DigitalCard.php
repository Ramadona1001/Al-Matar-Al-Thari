<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DigitalCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_number',
        'qr_code',
        'type',
        'discount_percentage',
        'loyalty_points',
        'expiry_date',
        'status',
        'user_id',
        'is_frozen',
        'frozen_reason',
        'frozen_by',
        'frozen_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'loyalty_points' => 'integer',
        'expiry_date' => 'date',
        'is_frozen' => 'boolean',
        'frozen_at' => 'datetime',
    ];

    /**
     * Get the user that owns the digital card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the digital card.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Check if card is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date >= now();
    }

    /**
     * Check if card is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date < now() || $this->status === 'expired';
    }

    /**
     * Check if card is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Check if card is frozen.
     */
    public function isFrozen(): bool
    {
        return $this->is_frozen === true;
    }

    /**
     * Get the admin who froze the card.
     */
    public function frozenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'frozen_by');
    }

    /**
     * Generate unique card number.
     */
    public static function generateUniqueCardNumber(string $type = 'standard'): string
    {
        $prefix = 'DC'; // Digital Card prefix
        
        do {
            $number = $prefix . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        } while (self::where('card_number', $number)->exists());

        return $number;
    }

    /**
     * Generate QR code for the card.
     */
    public function generateQrCode(): string
    {
        return 'CARD-' . $this->card_number . '-' . $this->id;
    }

    /**
     * Get card benefits based on type.
     * Note: Cards no longer have discounts, only points system as configured by admin.
     */
    public function getBenefits(): array
    {
        return [
            'description' => __('Earn loyalty points on every purchase'),
            'points_system' => __('Points are calculated based on admin settings'),
        ];
    }

    /**
     * Calculate loyalty points for a given amount.
     */
    public function calculateLoyaltyPoints(float $amount): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        $benefits = $this->getBenefits();
        $basePoints = intval($amount / 10); // 1 point per 10 currency units
        return intval($basePoints * $benefits['loyalty_points_multiplier']);
    }

    /**
     * Scope a query to only include active cards.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expiry_date', '>=', now());
    }

    /**
     * Scope a query to only include cards by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include expired cards.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                     ->orWhere('status', 'expired');
    }
}