<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public bool $shouldReversePoints;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, bool $shouldReversePoints = false)
    {
        $this->ticket = $ticket;
        $this->shouldReversePoints = $shouldReversePoints;
    }
}
