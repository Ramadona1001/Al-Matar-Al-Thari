<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'payment_gateway_id',
        'user_id',
        'company_id',
        'affiliate_id',
        'type',
        'amount',
        'currency',
        'fee_amount',
        'net_amount',
        'status',
        'gateway_transaction_id',
        'gateway_response',
        'payment_method',
        'payer_info',
        'recipient_info',
        'description',
        'metadata',
        'processed_at',
        'failed_at',
        'refunded_at',
        'refund_amount',
        'refund_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'payer_info' => 'array',
        'recipient_info' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markAsCompleted($gatewayTransactionId = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'completed',
            'gateway_transaction_id' => $gatewayTransactionId,
            'gateway_response' => $gatewayResponse,
            'processed_at' => now()
        ]);
    }

    public function markAsFailed($reason = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => $gatewayResponse,
            'failed_at' => now()
        ]);

        if ($reason) {
            $this->update(['description' => $reason]);
        }
    }

    public function markAsRefunded($amount = null, $reason = null)
    {
        $this->update([
            'status' => 'refunded',
            'refund_amount' => $amount ?? $this->amount,
            'refund_reason' => $reason,
            'refunded_at' => now()
        ]);
    }

    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            'disputed' => 'Disputed'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function getStatusBadge()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'secondary',
            'disputed' => 'warning'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function scopeForAffiliate($query, $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
    }
}