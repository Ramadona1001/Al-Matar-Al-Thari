<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'pos_device_id',
        'company_id',
        'branch_id',
        'user_id',
        'coupon_id',
        'digital_card_id',
        'amount',
        'discount_amount',
        'final_amount',
        'tax_amount',
        'payment_method',
        'payment_status',
        'pos_transaction_id',
        'items',
        'receipt_data',
        'status',
        'processed_at',
        'synced_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'items' => 'json',
        'receipt_data' => 'json',
        'processed_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_id) {
                $transaction->transaction_id = Transaction::generateUniqueTransactionId();
            }
            if (!$transaction->pos_transaction_id) {
                $transaction->pos_transaction_id = 'POS-' . strtoupper(uniqid());
            }
        });
    }

    public function posDevice(): BelongsTo
    {
        return $this->belongsTo(PosDevice::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function digitalCard(): BelongsTo
    {
        return $this->belongsTo(DigitalCard::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('pos_device_id', $deviceId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function complete(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        return true;
    }

    public function refund(): bool
    {
        if (!in_array($this->status, ['completed', 'pending'])) {
            return false;
        }

        $this->update([
            'status' => 'refunded',
            'synced_at' => now(),
        ]);

        return true;
    }
}