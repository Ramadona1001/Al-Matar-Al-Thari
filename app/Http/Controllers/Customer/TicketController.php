<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::where('user_id', auth()->id())
            ->with(['company', 'service', 'attachments'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->paginate(15);

        return view('customer.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $companies = Company::where('status', 'approved')->orderBy('name')->get();
        $services = Service::where('is_active', true)
            ->with('translations')
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.tickets.create', compact('companies', 'services'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:service_not_delivered,payment_issue,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'company_id' => 'nullable|exists:companies,id',
            'service_id' => 'nullable|exists:services,id',
            'transaction_id' => 'nullable|exists:transactions,id',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,mp4|max:10240', // 10MB max
        ]);

        DB::transaction(function () use ($validated, $request) {
            $ticket = Ticket::create([
                'ticket_number' => Ticket::generateTicketNumber(),
                'category' => $validated['category'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'status' => 'open',
                'priority' => 'medium',
                'user_id' => auth()->id(),
                'company_id' => $validated['company_id'] ?? null,
                'service_id' => $validated['service_id'] ?? null,
                'transaction_id' => $validated['transaction_id'] ?? null,
            ]);

            // Fire TicketOpened event (listener will lock affiliate points if transaction linked)
            event(new \App\Events\TicketOpened($ticket));

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('tickets/attachments', 'public');
                    
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        });

        return redirect()->route('customer.tickets.index')
            ->with('success', __('Ticket created successfully.'));
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['company', 'service', 'attachments', 'resolvedBy']);

        return view('customer.tickets.show', compact('ticket'));
    }
}
