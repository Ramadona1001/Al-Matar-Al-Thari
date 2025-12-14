<?php

namespace App\Listeners;

use App\Events\TicketOpened;
use App\Models\WalletTransaction;
use App\Models\AffiliateSale;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class LockAffiliatePointsOnTicketOpened implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TicketOpened $event): void
    {
        $ticket = $event->ticket;

        // Only proceed if ticket is linked to a transaction
        if (!$ticket->transaction_id) {
            return;
        }

        DB::transaction(function () use ($ticket) {
            // Find affiliate sales related to this transaction
            $affiliateSales = AffiliateSale::where('transaction_id', $ticket->transaction_id)
                ->where('status', 'approved')
                ->get();

            foreach ($affiliateSales as $affiliateSale) {
                // Find wallet transactions related to this affiliate sale
                $walletTransactions = WalletTransaction::where('type', 'affiliate')
                    ->where('source_type', \App\Models\Transaction::class)
                    ->where('source_id', $ticket->transaction_id)
                    ->where('status', 'pending') // Only lock pending transactions
                    ->get();

                foreach ($walletTransactions as $walletTransaction) {
                    // Update status to locked
                    $walletTransaction->update([
                        'status' => 'locked',
                    ]);

                    // Log action
                    AuditLog::log(
                        'affiliate_points_locked',
                        $ticket,
                        "Affiliate points locked due to ticket {$ticket->ticket_number}",
                        auth()->user(),
                        [
                            'wallet_transaction_id' => $walletTransaction->id,
                            'affiliate_sale_id' => $affiliateSale->id,
                            'points' => $walletTransaction->points,
                        ]
                    );
                }
            }
        });
    }
}
