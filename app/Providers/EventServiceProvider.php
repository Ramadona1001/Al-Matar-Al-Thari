<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Listeners\SendCustomEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendCustomEmailVerificationNotification::class,
        ],
        \App\Events\OrderCompleted::class => [
            \App\Listeners\CalculateLoyaltyPointsListener::class,
            \App\Listeners\AwardAffiliatePointsListener::class,
        ],
        \App\Events\PaymentConfirmed::class => [
            \App\Listeners\CalculateLoyaltyPointsListener::class,
            \App\Listeners\AwardAffiliatePointsListener::class,
        ],
        \App\Events\TicketOpened::class => [
            \App\Listeners\LockAffiliatePointsOnTicketOpened::class,
        ],
        \App\Events\TicketResolved::class => [
            \App\Listeners\HandleAffiliatePointsOnTicketResolved::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
