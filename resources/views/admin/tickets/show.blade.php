@extends('layouts.dashboard')

@section('title', __('Ticket Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ __('Tickets') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_number }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Ticket Details') }}</h6>
                <span class="badge 
                    @if($ticket->status == 'open') bg-primary
                    @elseif($ticket->status == 'in_progress') bg-warning
                    @elseif($ticket->status == 'resolved') bg-success
                    @else bg-secondary
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h4 class="mb-2">{{ $ticket->subject }}</h4>
                    <div class="d-flex gap-3 mb-3">
                        <span class="badge 
                            @if($ticket->category == 'service_not_delivered') bg-danger
                            @elseif($ticket->category == 'payment_issue') bg-warning
                            @else bg-info
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                        </span>
                        <span class="badge 
                            @if($ticket->priority == 'high') bg-danger
                            @elseif($ticket->priority == 'medium') bg-warning
                            @else bg-info
                            @endif">
                            {{ ucfirst($ticket->priority) }} {{ __('Priority') }}
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-calendar me-1"></i>{{ $ticket->created_at->format('Y-m-d H:i') }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">{{ __('User Information') }}</h6>
                    <div class="d-flex align-items-center gap-2">
                        <div>
                            <div class="fw-semibold">{{ $ticket->user->full_name }}</div>
                            <small class="text-muted">{{ $ticket->user->email }}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">{{ __('Description') }}</h6>
                    <p class="text-muted">{{ $ticket->description }}</p>
                </div>

                @if($ticket->company)
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">{{ __('Company') }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            @if($ticket->company->logo)
                                <img src="{{ asset('storage/' . $ticket->company->logo) }}" 
                                     alt="{{ $ticket->company->localized_name }}" 
                                     class="rounded-circle" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                            <span>{{ $ticket->company->localized_name }}</span>
                        </div>
                    </div>
                @endif

                @if($ticket->service)
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">{{ __('Service') }}</h6>
                        <p class="text-muted">{{ $ticket->service->localized_name }}</p>
                    </div>
                @endif

                @if($ticket->attachments->count() > 0)
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">{{ __('Attachments') }}</h6>
                        <div class="row g-2">
                            @foreach($ticket->attachments as $attachment)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body p-2 text-center">
                                            @if(str_starts_with($attachment->file_type, 'image/'))
                                                <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                                                     alt="{{ $attachment->file_name }}" 
                                                     class="img-thumbnail" 
                                                     style="max-height: 100px; object-fit: cover;">
                                            @else
                                                <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                                <div class="small text-muted">{{ Str::limit($attachment->file_name, 20) }}</div>
                                            @endif
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary mt-2">
                                                <i class="fas fa-download me-1"></i>{{ __('Download') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($ticket->isResolved() && $ticket->resolution_notes)
                    <div class="alert alert-success">
                        <h6 class="fw-semibold mb-2">{{ __('Resolution') }}</h6>
                        <p class="mb-0">{{ $ticket->resolution_notes }}</p>
                        @if($ticket->resolvedBy)
                            <small class="text-muted">
                                {{ __('Resolved by') }}: {{ $ticket->resolvedBy->full_name }} 
                                {{ __('on') }} {{ $ticket->resolved_at->format('Y-m-d H:i') }}
                            </small>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if(!$ticket->isResolved())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Resolve Ticket') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tickets.resolve', $ticket) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="resolution_notes" class="form-label">{{ __('Resolution Notes') }} <span class="text-danger">*</span></label>
                            <textarea name="resolution_notes" id="resolution_notes" rows="4" 
                                      class="form-control @error('resolution_notes') is-invalid @enderror" required>{{ old('resolution_notes') }}</textarea>
                            @error('resolution_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="should_reverse_points" id="should_reverse_points" value="1">
                                <label class="form-check-label" for="should_reverse_points">
                                    {{ __('Reverse Points') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">{{ __('Reverse points earned from related transactions') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="action" class="form-label">{{ __('Action') }}</label>
                            <select name="action" id="action" class="form-select">
                                <option value="none">{{ __('No Action') }}</option>
                                <option value="freeze_customer">{{ __('Freeze Customer Account') }}</option>
                                @if($ticket->company)
                                    <option value="freeze_company">{{ __('Freeze Company Account') }}</option>
                                @endif
                                @if($ticket->user->digitalCard)
                                    <option value="freeze_card">{{ __('Freeze Digital Card') }}</option>
                                @endif
                            </select>
                            <small class="form-text text-muted">{{ __('Take action based on ticket resolution') }}</small>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i>{{ __('Resolve Ticket') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Ticket Information') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Ticket Number') }}:</strong>
                    <div class="text-primary fw-semibold">{{ $ticket->ticket_number }}</div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Status') }}:</strong>
                    <div>
                        <span class="badge 
                            @if($ticket->status == 'open') bg-primary
                            @elseif($ticket->status == 'in_progress') bg-warning
                            @elseif($ticket->status == 'resolved') bg-success
                            @else bg-secondary
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Priority') }}:</strong>
                    <div>
                        <span class="badge 
                            @if($ticket->priority == 'high') bg-danger
                            @elseif($ticket->priority == 'medium') bg-warning
                            @else bg-info
                            @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Created At') }}:</strong>
                    <div class="text-muted">{{ $ticket->created_at->format('Y-m-d H:i') }}</div>
                </div>
                @if($ticket->resolved_at)
                    <div class="mb-3">
                        <strong>{{ __('Resolved At') }}:</strong>
                        <div class="text-muted">{{ $ticket->resolved_at->format('Y-m-d H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Actions') }}</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Tickets') }}
                </a>
                @if($ticket->user)
                    <a href="{{ route('admin.users.edit', $ticket->user) }}" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-user me-2"></i>{{ __('View User') }}
                    </a>
                @endif
                @if($ticket->company)
                    <a href="{{ route('admin.companies.show', $ticket->company) }}" class="btn btn-info w-100">
                        <i class="fas fa-building me-2"></i>{{ __('View Company') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

