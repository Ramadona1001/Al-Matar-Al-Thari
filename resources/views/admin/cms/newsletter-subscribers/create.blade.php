@extends('layouts.dashboard')

@section('title', __('Add Newsletter Subscriber'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.newsletter-subscribers.index') }}">{{ __('Newsletter Subscribers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Add') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-user-plus text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Add Newsletter Subscriber') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.newsletter-subscribers.store') }}">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">{{ __('Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="source" class="form-label fw-semibold">{{ __('Source') }}</label>
                    <select name="source" id="source" class="form-select">
                        <option value="admin" {{ old('source', 'admin') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                        <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>{{ __('Website') }}</option>
                        <option value="api" {{ old('source') == 'api' ? 'selected' : '' }}>{{ __('API') }}</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Add Subscriber') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

