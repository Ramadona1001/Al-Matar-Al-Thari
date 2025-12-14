<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use App\Models\AffiliateSale;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReverseAffiliatePointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ticket $ticket;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->ticket->transaction_id) {
            return;
        }

        DB::transaction(function () {
            // Find affiliate sales related to this transaction
            $affiliateSales = AffiliateSale::where('transaction_id', $this->ticket->transaction_id)
                ->where('status', 'approved')
                ->get();

            foreach ($affiliateSales as $affiliateSale) {
                // Find wallet transactions related to this affiliate sale
                $walletTransactions = WalletTransaction::where('type', 'affiliate')
                    ->where('source_type', \App\Models\Transaction::class)
                    ->where('source_id', $this->ticket->transaction_id)
                    ->whereIn('status', ['pending', 'approved', 'locked'])
                    ->get();

                foreach ($walletTransactions as $walletTransaction) {
                    // Skip if already reversed
                    if ($walletTransaction->status === 'reversed') {
                        continue;
                    }

                    $points = abs($walletTransaction->points);
                    $wallet = $walletTransaction->wallet;
                    $originalStatus = $walletTransaction->status; // Save original status before update

                    // Update transaction status to reversed
                    $walletTransaction->update([
                        'status' => 'reversed',
                    ]);

                    // Deduct points from wallet based on original status
                    if ($originalStatus === 'approved') {
                        // Deduct from approved balance
                        $wallet->decrement('affiliate_points_balance', $points);
                    } else {
                        // Deduct from pending balance
                        $wallet->decrement('affiliate_points_pending', $points);
                    }

                    // Create reversal transaction record
                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'affiliate',
                        'transaction_type' => 'reversed',
                        'points' => -$points,
                        'status' => 'approved',
                        'source_type' => Ticket::class,
                        'source_id' => $this->ticket->id,
                        'description' => "Affiliate points reversed due to ticket resolution. Ticket: {$this->ticket->ticket_number}",
                        'approved_by' => $this->ticket->resolved_by,
                        'approved_at' => now(),
                    ]);

                    // Log action
                    AuditLog::log(
                        'affiliate_points_reversed',
                        $this->ticket,
                        "Affiliate points reversed due to ticket resolution. Points: {$points}, Ticket: {$this->ticket->ticket_number}",
                        $this->ticket->resolvedBy,
                        [
                            'wallet_transaction_id' => $walletTransaction->id,
                            'affiliate_sale_id' => $affiliateSale->id,
                            'points' => $points,
                            'reason' => 'Company at fault - ticket resolved',
                        ]
                    );
                }

                // Update affiliate sale status
                $affiliateSale->update([
                    'status' => 'rejected',
                ]);
            }
        });
    }
}
