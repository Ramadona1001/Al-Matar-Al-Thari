<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Company;
use App\Models\DigitalCard;
use App\Jobs\ReversePointsJob;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'company', 'service', 'resolvedBy'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(15);

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'company', 'service', 'attachments', 'resolvedBy']);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Resolve the ticket.
     */
    public function resolve(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string',
            'should_reverse_points' => 'boolean',
            'action' => 'nullable|in:freeze_customer,freeze_company,freeze_card,none',
        ]);

        DB::transaction(function () use ($ticket, $validated) {
            $admin = auth()->user();

            // Resolve ticket
            $ticket->resolve($admin, $validated['resolution_notes']);

            // Handle actions
            if ($validated['action'] === 'freeze_customer') {
                $this->freezeCustomer($ticket->user, 'Ticket resolution: ' . $validated['resolution_notes'], $admin);
            } elseif ($validated['action'] === 'freeze_company' && $ticket->company) {
                $this->freezeCompany($ticket->company, 'Ticket resolution: ' . $validated['resolution_notes'], $admin);
            } elseif ($validated['action'] === 'freeze_card' && $ticket->user->digitalCard) {
                $this->freezeCard($ticket->user->digitalCard, 'Ticket resolution: ' . $validated['resolution_notes'], $admin);
            }

            // Fire TicketResolved event (listener will handle affiliate points)
            event(new \App\Events\TicketResolved(
                $ticket,
                $validated['should_reverse_points'] ?? false
            ));

            // Log audit
            AuditLogService::logTicketResolved($ticket, $validated['resolution_notes'], $admin);
        });

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', __('Ticket resolved successfully.'));
    }

    /**
     * Freeze customer account.
     */
    private function freezeCustomer(User $user, string $reason, User $admin): void
    {
        $user->update([
            'is_frozen' => true,
            'frozen_reason' => $reason,
            'frozen_by' => $admin->id,
            'frozen_at' => now(),
        ]);

        AuditLogService::logAccountFrozen($user, $reason, $admin);
    }

    /**
     * Freeze company account.
     */
    private function freezeCompany(Company $company, string $reason, User $admin): void
    {
        $company->update([
            'is_frozen' => true,
            'frozen_reason' => $reason,
            'frozen_by' => $admin->id,
            'frozen_at' => now(),
        ]);

        AuditLogService::logAccountFrozen($company, $reason, $admin);
    }

    /**
     * Freeze digital card.
     */
    private function freezeCard(DigitalCard $card, string $reason, User $admin): void
    {
        $card->update([
            'is_frozen' => true,
            'frozen_reason' => $reason,
            'frozen_by' => $admin->id,
            'frozen_at' => now(),
            'status' => 'blocked',
        ]);

        AuditLogService::logCardFrozen($card, $reason, $admin);
    }

    /**
     * Reverse points for ticket.
     */
    private function reversePointsForTicket(Ticket $ticket): void
    {
        // Find transactions related to this ticket's company/service
        $transactions = \App\Models\Transaction::where('user_id', $ticket->user_id)
            ->where(function($q) use ($ticket) {
                if ($ticket->company_id) {
                    $q->where('company_id', $ticket->company_id);
                }
            })
            ->where('status', 'completed')
            ->get();

        foreach ($transactions as $transaction) {
            ReversePointsJob::dispatch($transaction, 'Ticket resolved: ' . $ticket->ticket_number);
        }
    }
}
