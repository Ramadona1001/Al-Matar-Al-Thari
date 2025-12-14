<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'qr_code',
        'barcode',
        'type',
        'value',
        'minimum_purchase',
        'usage_limit_per_user',
        'total_usage_limit',
        'start_date',
        'end_date',
        'status',
        'is_public',
        'offer_id',
        'product_id',
        'company_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Get the offer that owns the coupon.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the product that owns the coupon.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the company that owns the coupon.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user that owns the coupon.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coupon usages for the coupon.
     */
    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get the transactions for the coupon.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the affiliate sales for the coupon.
     */
    public function affiliateSales(): HasMany
    {
        return $this->hasMany(AffiliateSale::class);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now() &&
               $this->getRemainingUsageCount() > 0;
    }

    /**
     * Check if coupon is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if coupon is used.
     */
    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    /**
     * Get remaining usage count.
     */
    public function getRemainingUsageCount(): int
    {
        if ($this->total_usage_limit === null) {
            return PHP_INT_MAX;
        }
        
        $usedCount = $this->couponUsages()->count();
        return max(0, $this->total_usage_limit - $usedCount);
    }

    /**
     * Check if user can use this coupon.
     */
    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->usage_limit_per_user === null) {
            return true;
        }

        $userUsageCount = $this->couponUsages()
            ->where('user_id', $user->id)
            ->count();

        return $userUsageCount < $this->usage_limit_per_user;
    }

    /**
     * Generate unique coupon code.
     */
    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Generate QR code for the coupon.
     */
    public function generateQrCode(): string
    {
        return 'COUPON-' . $this->code . '-' . $this->id;
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include public coupons.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include coupons by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}