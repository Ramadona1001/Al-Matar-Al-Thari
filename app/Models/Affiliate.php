<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
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
        'commission_rate',
        'commission_type',
        'total_earned',
        'total_referrals',
        'status',
        'approved_at',
        'user_id',
        'company_id',
        'offer_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_referrals' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the affiliate account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the affiliate account.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the offer that the affiliate is promoting.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the affiliate sales for the affiliate.
     */
    public function affiliateSales(): HasMany
    {
        return $this->hasMany(AffiliateSale::class);
    }

    /**
     * Check if affiliate is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if affiliate is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if affiliate is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if affiliate is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
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
     * Calculate commission for a sale amount.
     */
    public function calculateCommission(float $saleAmount): float
    {
        if (!$this->isApproved()) {
            return 0;
        }

        if ($this->commission_type === 'percentage') {
            return ($saleAmount * $this->commission_rate) / 100;
        }

        return $this->commission_rate; // Fixed amount
    }

    /**
     * Record a successful referral sale.
     */
    public function recordSale(float $saleAmount, User $referredUser, array $additionalData = []): AffiliateSale
    {
        $commissionAmount = $this->calculateCommission($saleAmount);

        $sale = $this->affiliateSales()->create([
            'sale_amount' => $saleAmount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $this->commission_rate,
            'status' => 'pending',
            'user_id' => $referredUser->id,
            'company_id' => $this->company_id,
            'offer_id' => $this->offer_id ?? null,
            'ip_address' => $additionalData['ip_address'] ?? request()->ip(),
            'user_agent' => $additionalData['user_agent'] ?? request()->userAgent(),
        ]);

        // Update affiliate statistics
        $this->increment('total_referrals');

        return $sale;
    }

    /**
     * Approve the affiliate account.
     */
    public function approve(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Reject the affiliate account.
     */
    public function reject(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
        ]);

        return true;
    }

    /**
     * Suspend the affiliate account.
     */
    public function suspend(): bool
    {
        if (!in_array($this->status, ['approved', 'active'])) {
            return false;
        }

        $this->update([
            'status' => 'suspended',
        ]);

        return true;
    }

    /**
     * Reactivate the affiliate account.
     */
    public function reactivate(): bool
    {
        if ($this->status !== 'suspended') {
            return false;
        }

        $this->update([
            'status' => 'approved',
        ]);

        return true;
    }

    /**
     * Scope a query to only include approved affiliates.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending affiliates.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include active affiliates.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include affiliates for a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include affiliates for a specific offer.
     */
    public function scopeForOffer($query, int $offerId)
    {
        return $query->where('offer_id', $offerId);
    }
}