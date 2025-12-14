<?php

namespace App\Jobs;

use App\Models\PointsSetting;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SettleAffiliatePointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pointsSettings = PointsSetting::current();
        $settlementDays = $pointsSettings->affiliate_settlement_days ?? 30;
        $cutoffDate = now()->subDays($settlementDays);

        DB::transaction(function () use ($cutoffDate) {
            // Find pending affiliate wallet transactions older than settlement period
            $pendingTransactions = WalletTransaction::where('type', 'affiliate')
                ->where('status', 'pending')
                ->where('transaction_type', 'earned')
                ->where('created_at', '<=', $cutoffDate)
                ->get();

            foreach ($pendingTransactions as $transaction) {
                // Check if transaction is linked to a ticket
                if ($transaction->source_type === \App\Models\Transaction::class && $transaction->source_id) {
                    $relatedTransaction = \App\Models\Transaction::find($transaction->source_id);
                    
                    if ($relatedTransaction) {
                        // Check if there's an open ticket for this transaction
                        $openTicket = Ticket::where('transaction_id', $relatedTransaction->id)
                            ->where('status', 'open')
                            ->exists();

                        if ($openTicket) {
                            // Skip if there's an open ticket
                            continue;
                        }
                    }
                }

                // Check if transaction is locked
                if ($transaction->status === 'locked') {
                    continue;
                }

                // Approve the transaction
                $transaction->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);

                // Update wallet balances
                $wallet = $transaction->wallet;
                $wallet->decrement('affiliate_points_pending', abs($transaction->points));
                $wallet->increment('affiliate_points_balance', abs($transaction->points));

                // Create settled transaction record
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'affiliate',
                    'transaction_type' => 'settled',
                    'points' => abs($transaction->points),
                    'status' => 'approved',
                    'source_type' => WalletTransaction::class,
                    'source_id' => $transaction->id,
                    'description' => "Affiliate points settled after {$settlementDays} days settlement period",
                    'approved_at' => now(),
                ]);
            }
        });
    }
}
