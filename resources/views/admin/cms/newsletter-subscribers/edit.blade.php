@extends('layouts.dashboard')

@section('title', __('Edit Newsletter Subscriber'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.newsletter-subscribers.index') }}">{{ __('Newsletter Subscribers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-user-edit text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Newsletter Subscriber') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.newsletter-subscribers.update', $newsletterSubscriber) }}">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">{{ __('Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $newsletterSubscriber->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $newsletterSubscriber->name) }}" class="form-control">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $newsletterSubscriber->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Subscriber') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

