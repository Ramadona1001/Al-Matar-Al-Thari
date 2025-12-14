<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\PointsSetting;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CalculateLoyaltyPointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transaction $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Skip if user or card is frozen
        if ($this->transaction->user->is_frozen || 
            ($this->transaction->user->digitalCard && $this->transaction->user->digitalCard->is_frozen)) {
            return;
        }

        // Get points settings
        $pointsSetting = PointsSetting::current();
        $earnRate = $pointsSetting->earn_rate ?? 10; // Default: 10 SAR = 1 point

        // Calculate points from original price (not final amount after discount)
        $originalPrice = $this->transaction->original_price ?? $this->transaction->amount;
        $points = (int) floor($originalPrice / $earnRate);

        if ($points <= 0) {
            return;
        }

        DB::transaction(function () use ($points) {
            // Get or create wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $this->transaction->user_id],
                [
                    'loyalty_points_balance' => 0,
                    'affiliate_points_balance' => 0,
                    'loyalty_points_pending' => 0,
                    'affiliate_points_pending' => 0,
                ]
            );

            // Add points directly to balance (loyalty points don't need settlement period)
            $wallet->approveLoyaltyPoints($points);

            // Create wallet transaction as approved (loyalty points are immediately available)
            $walletTransaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'loyalty',
                'transaction_type' => 'earned',
                'points' => $points,
                'status' => 'approved',
                'approved_at' => now(),
                'source_type' => Transaction::class,
                'source_id' => $this->transaction->id,
                'description' => 'Loyalty points earned from transaction ' . $this->transaction->transaction_id,
            ]);

            // Update transaction
            $this->transaction->update([
                'loyalty_points_earned' => $points,
            ]);

            // Log audit
            AuditLog::log(
                'points_earned',
                $this->transaction,
                "Loyalty points earned: {$points} points from transaction {$this->transaction->transaction_id}",
                null,
                ['points' => $points, 'original_price' => $this->transaction->original_price ?? $this->transaction->amount]
            );
        });
    }
}
