<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'transaction_type',
        'points',
        'status',
        'source_type',
        'source_id',
        'description',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the admin who approved the transaction.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the source of the transaction.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if transaction is locked.
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    /**
     * Check if transaction is reversed.
     */
    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    /**
     * Approve the transaction.
     */
    public function approve(User $admin): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        // Update wallet balance
        if ($this->type === 'loyalty') {
            $this->wallet->approveLoyaltyPoints($this->points);
        } else {
            $this->wallet->approveAffiliatePoints($this->points);
        }

        return true;
    }

    /**
     * Reject the transaction.
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
}
