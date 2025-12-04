<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    use HasFactory;

    protected $table = 'coupon_usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usage_code',
        'discount_amount',
        'original_amount',
        'final_amount',
        'status',
        'ip_address',
        'user_agent',
        'used_at',
        'coupon_id',
        'user_id',
        'company_id',
        'branch_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    /**
     * Get the coupon that was used.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the user who used the coupon.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the coupon usage.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch where the coupon was used.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Check if usage is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'used';
    }

    /**
     * Check if usage is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if usage is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Generate unique usage code.
     */
    public static function generateUniqueUsageCode(): string
    {
        do {
            $code = 'USAGE-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
        } while (self::where('usage_code', $code)->exists());

        return $code;
    }

    /**
     * Record successful coupon usage.
     */
    public static function recordUsage(Coupon $coupon, User $user, float $originalAmount, float $discountAmount, float $finalAmount, int $branchId = null): self
    {
        return self::create([
            'usage_code' => self::generateUniqueUsageCode(),
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'company_id' => $coupon->company_id,
            'branch_id' => $branchId,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'status' => 'used',
            'used_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Record failed coupon usage.
     */
    public static function recordFailedUsage(Coupon $coupon, User $user, string $reason = null): self
    {
        return self::create([
            'usage_code' => self::generateUniqueUsageCode(),
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'company_id' => $coupon->company_id,
            'status' => 'failed',
            'used_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'used' => 'Used',
            'failed' => 'Failed',
            'expired' => 'Expired',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'used' => 'success',
            'failed' => 'danger',
            'expired' => 'warning',
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Scope a query to only include successful usages.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * Scope a query to only include failed usages.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include usages for a specific coupon.
     */
    public function scopeForCoupon($query, int $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    /**
     * Scope a query to only include usages for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include usages for a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include usages for a specific branch.
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Calculate total discount for a specific coupon.
     */
    public static function calculateTotalDiscountForCoupon(int $couponId): float
    {
        return self::forCoupon($couponId)
                   ->successful()
                   ->sum('discount_amount');
    }

    /**
     * Calculate total usage count for a specific coupon.
     */
    public static function calculateUsageCountForCoupon(int $couponId): int
    {
        return self::forCoupon($couponId)
                   ->successful()
                   ->count();
    }

    /**
     * Calculate total usage count for a specific user.
     */
    public static function calculateUsageCountForUser(int $userId): int
    {
        return self::forUser($userId)
                   ->successful()
                   ->count();
    }

    /**
     * Get usage statistics for a coupon.
     */
    public static function getUsageStatisticsForCoupon(int $couponId): array
    {
        $totalUsages = self::forCoupon($couponId)->count();
        $successfulUsages = self::forCoupon($couponId)->successful()->count();
        $failedUsages = self::forCoupon($couponId)->failed()->count();
        $totalDiscount = self::calculateTotalDiscountForCoupon($couponId);

        return [
            'total_usages' => $totalUsages,
            'successful_usages' => $successfulUsages,
            'failed_usages' => $failedUsages,
            'success_rate' => $totalUsages > 0 ? round(($successfulUsages / $totalUsages) * 100, 2) : 0,
            'total_discount' => $totalDiscount,
        ];
    }
}