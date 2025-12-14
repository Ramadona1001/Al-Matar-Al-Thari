<?php

namespace App\Listeners;

use App\Events\TicketResolved;
use App\Jobs\ReverseAffiliatePointsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleAffiliatePointsOnTicketResolved implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TicketResolved $event): void
    {
        $ticket = $event->ticket;

        // Only proceed if ticket is linked to a transaction
        if (!$ticket->transaction_id) {
            return;
        }

        // If company at fault, reverse affiliate points
        if ($event->shouldReversePoints) {
            ReverseAffiliatePointsJob::dispatch($ticket);
        } else {
            // If customer at fault, approve affiliate points
            // This will be handled by SettleAffiliatePointsJob or manual approval
            // For now, we'll unlock the points to allow settlement
            $this->unlockAffiliatePoints($ticket);
        }
    }

    /**
     * Unlock affiliate points when customer is at fault.
     */
    private function unlockAffiliatePoints($ticket): void
    {
        \App\Models\WalletTransaction::where('type', 'affiliate')
            ->where('source_type', \App\Models\Transaction::class)
            ->where('source_id', $ticket->transaction_id)
            ->where('status', 'locked')
            ->update(['status' => 'pending']);
    }
}
