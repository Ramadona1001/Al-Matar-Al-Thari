@extends('layouts.dashboard')

@section('title', __('My Tickets'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Tickets') }}</li>
@endsection

@section('actions')
    <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('Create Ticket') }}
    </a>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('My Tickets') }}</h6>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('customer.tickets.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">{{ __('Category') }}</label>
                <select name="category" id="category" class="form-select">
                    <option value="">{{ __('All Categories') }}</option>
                    <option value="service_not_delivered" {{ request('category') == 'service_not_delivered' ? 'selected' : '' }}>{{ __('Service Not Delivered') }}</option>
                    <option value="payment_issue" {{ request('category') == 'payment_issue' ? 'selected' : '' }}>{{ __('Payment Issue') }}</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('Filter') }}
                </button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('customer.tickets.index') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-redo me-2"></i>{{ __('Reset') }}
                </a>
            </div>
        </form>

        <!-- Tickets Table -->
        <div class="table-responsive">
            <table class="table table-bordered datatable" data-dt-init="false">
                <thead>
                    <tr>
                        <th>{{ __('Ticket Number') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th width="100">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="fw-semibold text-primary">{{ $ticket->ticket_number }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($ticket->subject, 50) }}</div>
                                @if($ticket->company)
                                    <small class="text-muted">{{ __('Against') }}: {{ $ticket->company->localized_name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($ticket->category == 'service_not_delivered')
                                    <span class="badge bg-danger">{{ __('Service Not Delivered') }}</span>
                                @elseif($ticket->category == 'payment_issue')
                                    <span class="badge bg-warning">{{ __('Payment Issue') }}</span>
                                @else
                                    <span class="badge bg-info">{{ __('Other') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->status == 'open')
                                    <span class="badge bg-primary">{{ __('Open') }}</span>
                                @elseif($ticket->status == 'in_progress')
                                    <span class="badge bg-warning">{{ __('In Progress') }}</span>
                                @elseif($ticket->status == 'resolved')
                                    <span class="badge bg-success">{{ __('Resolved') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Closed') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->priority == 'high')
                                    <span class="badge bg-danger">{{ __('High') }}</span>
                                @elseif($ticket->priority == 'medium')
                                    <span class="badge bg-warning">{{ __('Medium') }}</span>
                                @else
                                    <span class="badge bg-info">{{ __('Low') }}</span>
                                @endif
                            </td>
                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('customer.tickets.show', $ticket) }}" class="btn btn-sm btn-primary" title="{{ __('View') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-ticket-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">{{ __('No tickets found') }}</p>
                                    <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>{{ __('Create Your First Ticket') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection

