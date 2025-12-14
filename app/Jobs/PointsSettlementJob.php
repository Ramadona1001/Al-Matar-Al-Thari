<?php

namespace App\Jobs;

use App\Models\WalletTransaction;
use App\Models\PointsSetting;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PointsSettlementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?WalletTransaction $walletTransaction;
    public ?int $points;
    public ?string $type; // 'loyalty' or 'affiliate'

    /**
     * Create a new job instance.
     */
    public function __construct(?WalletTransaction $walletTransaction = null, ?int $points = null, ?string $type = null)
    {
        $this->walletTransaction = $walletTransaction;
        $this->points = $points;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pointsSetting = PointsSetting::current();
        $autoApprove = $pointsSetting->auto_approve_redemptions ?? false;

        if ($this->walletTransaction) {
            // Approve specific transaction
            if ($autoApprove || $this->walletTransaction->status === 'pending') {
                $this->approveTransaction($this->walletTransaction);
            }
        } else {
            // Approve all pending transactions (batch settlement)
            $this->settlePendingTransactions($autoApprove);
        }
    }

    /**
     * Approve a specific transaction.
     */
    private function approveTransaction(WalletTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $admin = auth()->user() ?? \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'super-admin');
            })->first();

            $transaction->approve($admin);

            AuditLog::log(
                'points_settled',
                $transaction,
                "Points settled: {$transaction->points} {$transaction->type} points approved",
                ['status' => 'pending'],
                ['status' => 'approved', 'approved_by' => $admin->id]
            );
        });
    }

    /**
     * Settle all pending transactions.
     */
    private function settlePendingTransactions(bool $autoApprove): void
    {
        $query = WalletTransaction::where('status', 'pending');

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->points) {
            $query->where('points', $this->points);
        }

        $transactions = $query->get();

        foreach ($transactions as $transaction) {
            if ($autoApprove) {
                $this->approveTransaction($transaction);
            }
        }
    }
}
