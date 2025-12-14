<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReversePointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transaction $transaction;
    public ?string $reason;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction, ?string $reason = null)
    {
        $this->transaction = $transaction;
        $this->reason = $reason;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $user = $this->transaction->user;
            $wallet = Wallet::where('user_id', $user->id)->first();

            if (!$wallet) {
                return;
            }

            // Find wallet transactions related to this transaction
            $walletTransactions = WalletTransaction::where('source_type', Transaction::class)
                ->where('source_id', $this->transaction->id)
                ->whereIn('status', ['pending', 'approved'])
                ->get();

            foreach ($walletTransactions as $walletTransaction) {
                // Reverse points
                if ($walletTransaction->type === 'loyalty') {
                    $wallet->reverseLoyaltyPoints($walletTransaction->points);
                } else {
                    $wallet->reverseAffiliatePoints($walletTransaction->points);
                }

                // Create reversal transaction
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => $walletTransaction->type,
                    'transaction_type' => 'reversed',
                    'points' => -$walletTransaction->points,
                    'status' => 'approved',
                    'source_type' => Transaction::class,
                    'source_id' => $this->transaction->id,
                    'description' => 'Points reversed: ' . ($this->reason ?? 'Transaction reversed'),
                ]);

                // Update original transaction status
                $walletTransaction->update(['status' => 'rejected']);

                // Log audit
                AuditLog::log(
                    'points_reversed',
                    $this->transaction,
                    "Points reversed: {$walletTransaction->points} {$walletTransaction->type} points. Reason: " . ($this->reason ?? 'Transaction reversed'),
                    ['points' => $walletTransaction->points, 'status' => $walletTransaction->status],
                    ['points' => -$walletTransaction->points, 'status' => 'rejected']
                );
            }
        });
    }
}
