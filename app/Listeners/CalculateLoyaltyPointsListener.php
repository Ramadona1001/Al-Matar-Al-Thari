<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Jobs\CalculateLoyaltyPointsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculateLoyaltyPointsListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        CalculateLoyaltyPointsJob::dispatch($event->transaction);
    }
}
