@extends('layouts.dashboard')

@section('title', __('View Contact Message'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.contact_messages.index') }}">{{ __('Contact Messages') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('View') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-md-8">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-primary-subtle">
                        <i class="fas fa-envelope text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Message Details') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('Contact Message') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Name') }}</label>
                        <div class="fw-semibold text-gray-900 fs-5">{{ $contactMessage->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Email') }}</label>
                        <div>
                            <a href="mailto:{{ $contactMessage->email }}" class="text-primary text-decoration-none fw-semibold">
                                {{ $contactMessage->email }}
                            </a>
                        </div>
                    </div>
                    @if($contactMessage->phone)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">{{ __('Phone') }}</label>
                            <div>
                                <a href="tel:{{ $contactMessage->phone }}" class="text-primary text-decoration-none fw-semibold">
                                    {{ $contactMessage->phone }}
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Status') }}</label>
                        <div>
                            @if($contactMessage->read_at)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ __('Read') }}
                                </span>
                                <small class="text-muted d-block mt-1">
                                    {{ __('Read at') }}: {{ $contactMessage->read_at->format('Y-m-d H:i:s') }}
                                </small>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ __('Unread') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">{{ __('Subject') }}</label>
                        <div class="fw-semibold text-gray-900 fs-5">{{ $contactMessage->subject }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">{{ __('Message') }}</label>
                        <div class="bg-light rounded p-4 border">
                            <p class="mb-0 text-gray-900" style="white-space: pre-wrap;">{{ $contactMessage->message }}</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">{{ __('Received At') }}</label>
                        <div class="text-gray-700">
                            <i class="fas fa-calendar-alt me-1 text-muted"></i>
                            {{ $contactMessage->created_at->format('Y-m-d H:i:s') }}
                        </div>
                        <small class="text-muted">{{ $contactMessage->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <h6 class="fw-bold mb-0">{{ __('Actions') }}</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-grid gap-2">
                    @if(!$contactMessage->read_at)
                        <form action="{{ route('admin.contact_messages.mark-as-read', $contactMessage) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 btn-animated">
                                <i class="fas fa-check me-2"></i>{{ __('Mark as Read') }}
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.contact_messages.mark-as-unread', $contactMessage) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 btn-animated">
                                <i class="fas fa-envelope me-2"></i>{{ __('Mark as Unread') }}
                            </button>
                        </form>
                    @endif
                    <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject }}" class="btn btn-primary w-100 btn-animated">
                        <i class="fas fa-reply me-2"></i>{{ __('Reply') }}
                    </a>
                    <form action="{{ route('admin.contact_messages.destroy', $contactMessage) }}" method="post" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 btn-animated">
                            <i class="fas fa-trash me-2"></i>{{ __('Delete') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.contact_messages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

