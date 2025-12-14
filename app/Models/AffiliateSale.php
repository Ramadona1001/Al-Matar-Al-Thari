<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateSale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_amount',
        'commission_amount',
        'commission_rate',
        'status',
        'approved_at',
        'paid_at',
        'ip_address',
        'user_agent',
        'affiliate_id',
        'user_id',
        'company_id',
        'offer_id',
        'coupon_id',
        'transaction_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sale_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the affiliate that owns the sale.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the user that made the purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the sale.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the offer that was purchased.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the transaction that generated this affiliate sale.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the coupon that was used.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Check if sale is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if sale is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if sale is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if sale is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Approve the sale.
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

        // Update affiliate total earnings
        $this->affiliate->increment('total_earned', $this->commission_amount);

        return true;
    }

    /**
     * Reject the sale.
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
     * Mark the sale as paid.
     */
    public function markAsPaid(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return true;
    }

    /**
     * Get commission status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get commission status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'paid' => 'success',
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Scope a query to only include pending sales.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved sales.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include paid sales.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include sales for a specific affiliate.
     */
    public function scopeForAffiliate($query, int $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    /**
     * Scope a query to only include sales for a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include sales for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate total commission for a specific affiliate.
     */
    public static function calculateTotalCommissionForAffiliate(int $affiliateId): float
    {
        return self::forAffiliate($affiliateId)
                   ->whereIn('status', ['approved', 'paid'])
                   ->sum('commission_amount');
    }

    /**
     * Calculate total commission for a specific company.
     */
    public static function calculateTotalCommissionForCompany(int $companyId): float
    {
        return self::forCompany($companyId)
                   ->whereIn('status', ['approved', 'paid'])
                   ->sum('commission_amount');
    }
}