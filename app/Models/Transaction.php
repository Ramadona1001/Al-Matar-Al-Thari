<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'amount',
        'original_price',
        'discount_amount',
        'final_amount',
        'loyalty_points_earned',
        'loyalty_points_used',
        'status',
        'payment_method',
        'notes',
        'ip_address',
        'user_agent',
        'user_id',
        'company_id',
        'branch_id',
        'coupon_id',
        'digital_card_id',
        'product_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'loyalty_points_earned' => 'integer',
        'loyalty_points_used' => 'integer',
    ];

    /**
     * Get the user that made the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the transaction.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch that processed the transaction.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the coupon that was used.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the digital card that was used.
     */
    public function digitalCard(): BelongsTo
    {
        return $this->belongsTo(DigitalCard::class);
    }

    /**
     * Get the product that was purchased.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Generate unique transaction ID.
     */
    public static function generateUniqueTransactionId(): string
    {
        do {
            $transactionId = 'TXN-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
        } while (self::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Complete the transaction.
     */
    public function complete(): bool
    {
        // Allow completing even if already completed (for event triggering)
        if ($this->status === 'completed') {
            // Just trigger the event if already completed
            event(new \App\Events\OrderCompleted($this));
            return true;
        }

        if ($this->status !== 'pending') {
            return false;
        }

        // Set original_price if not set (for backward compatibility)
        if (!$this->original_price) {
            $this->original_price = $this->amount;
        }

        $this->update([
            'status' => 'completed',
            'original_price' => $this->original_price ?? $this->amount,
        ]);

        // Dispatch event for automated points calculation
        event(new \App\Events\OrderCompleted($this));

        return true;
    }

    /**
     * Fail the transaction.
     */
    public function fail(string $reason = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'failed',
            'notes' => $reason ?? 'Transaction failed',
        ]);

        return true;
    }

    /**
     * Refund the transaction.
     */
    public function refund(string $reason = null): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        $this->update([
            'status' => 'refunded',
            'notes' => $reason ?? 'Transaction refunded',
        ]);

        // Return loyalty points if they were earned
        if ($this->loyalty_points_earned > 0) {
            LoyaltyPoint::create([
                'user_id' => $this->user_id,
                'company_id' => $this->company_id,
                'points' => -$this->loyalty_points_earned,
                'type' => 'redeemed',
                'source_type' => self::class,
                'source_id' => $this->id,
                'description' => 'Points returned due to refund for transaction ' . $this->transaction_id,
            ]);
        }

        return true;
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded transactions.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope a query to only include transactions for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include transactions for a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include transactions for a specific branch.
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Calculate total sales for a specific company.
     */
    public static function calculateTotalSalesForCompany(int $companyId): float
    {
        return self::forCompany($companyId)
                   ->completed()
                   ->sum('final_amount');
    }

    /**
     * Calculate total discount for a specific company.
     */
    public static function calculateTotalDiscountForCompany(int $companyId): float
    {
        return self::forCompany($companyId)
                   ->completed()
                   ->sum('discount_amount');
    }
}