<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Jobs\AffiliateRewardJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardAffiliatePointsListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        AffiliateRewardJob::dispatch($event->transaction);
    }
}
