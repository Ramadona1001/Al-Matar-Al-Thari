@extends('layouts.dashboard')

@section('title', __('Profile Settings'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
@endsection

@section('content')
@if(session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ __('Profile updated successfully.') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

@if(session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ __('Password updated successfully.') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-primary-subtle">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Account Settings') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('Profile Information') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Update Password -->
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-warning-subtle">
                        <i class="fas fa-lock text-warning"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Security') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('Update Password') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Delete Account -->
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 border-danger">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-danger-subtle">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Danger Zone') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('Delete Account') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
