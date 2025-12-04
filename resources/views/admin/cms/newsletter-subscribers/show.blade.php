@extends('layouts.dashboard')

@section('title', __('Subscriber Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.newsletter-subscribers.index') }}">{{ __('Newsletter Subscribers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Details') }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-user text-primary"></i></div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Subscriber Information') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('Subscriber Details') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Email') }}</label>
                        <div class="fw-semibold text-gray-900">{{ $newsletterSubscriber->email }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Name') }}</label>
                        <div class="fw-semibold text-gray-900">{{ $newsletterSubscriber->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Source') }}</label>
                        <div>
                            <span class="badge bg-info-subtle text-info">{{ $newsletterSubscriber->source ?? 'admin' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Status') }}</label>
                        <div>
                            @if($newsletterSubscriber->is_active && !$newsletterSubscriber->unsubscribed_at)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ __('Active') }}
                                </span>
                            @elseif($newsletterSubscriber->unsubscribed_at)
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fas fa-user-times me-1"></i>
                                    {{ __('Unsubscribed') }}
                                </span>
                            @else
                                <span class="badge bg-dark text-secondary rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fas fa-times-circle me-1"></i>
                                    {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">{{ __('Subscribed At') }}</label>
                        <div class="text-gray-700">
                            <i class="fas fa-calendar-alt me-1 text-muted"></i>
                            {{ $newsletterSubscriber->subscribed_at->format('Y-m-d H:i:s') }}
                        </div>
                        <small class="text-muted">{{ $newsletterSubscriber->subscribed_at->diffForHumans() }}</small>
                    </div>
                    @if($newsletterSubscriber->unsubscribed_at)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">{{ __('Unsubscribed At') }}</label>
                            <div class="text-gray-700">
                                <i class="fas fa-calendar-times me-1 text-muted"></i>
                                {{ $newsletterSubscriber->unsubscribed_at->format('Y-m-d H:i:s') }}
                            </div>
                            <small class="text-muted">{{ $newsletterSubscriber->unsubscribed_at->diffForHumans() }}</small>
                        </div>
                    @endif
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
                    <a href="{{ route('admin.newsletter-subscribers.edit', $newsletterSubscriber) }}" class="btn btn-primary btn-animated">
                        <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
                    </a>
                    @if($newsletterSubscriber->is_active && !$newsletterSubscriber->unsubscribed_at)
                        <form action="{{ route('admin.newsletter-subscribers.unsubscribe', $newsletterSubscriber) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 btn-animated">
                                <i class="fas fa-user-times me-2"></i>{{ __('Unsubscribe') }}
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.newsletter-subscribers.resubscribe', $newsletterSubscriber) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 btn-animated">
                                <i class="fas fa-user-check me-2"></i>{{ __('Resubscribe') }}
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.newsletter-subscribers.destroy', $newsletterSubscriber) }}" method="post" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 btn-animated">
                            <i class="fas fa-trash me-2"></i>{{ __('Delete') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

