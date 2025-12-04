@extends('layouts.dashboard')

@section('title', __('Available Coupons'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('My Coupons') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters') }}</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('customer.coupons.index') }}">
                    <div class="mb-3">
                        <label for="search" class="form-label">{{ __('Search by Code') }}</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Enter coupon code') }}">
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">{{ __('Company') }}</label>
                        <select class="form-select" id="company" name="company">
                            <option value="">{{ __('All Companies') }}</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
                                    {{ $company->localized_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">{{ __('Coupon Type') }}</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>{{ __('Apply Filters') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="row g-3">
            @forelse($coupons as $coupon)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $coupon->type)) }}</span>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $coupon->end_date->diffForHumans() }}</small>
                            </div>
                            <h4>{{ $coupon->code }}</h4>
                            <p class="text-muted mb-2">{{ $coupon->offer->localized_title ?? __('General Discount') }}</p>
                            <ul class="list-unstyled small text-muted flex-grow-1">
                                <li><i class="fas fa-building me-2"></i>{{ $coupon->company->name ?? '-' }}</li>
                                <li><i class="fas fa-tag me-2"></i>
                                    @if($coupon->type == 'percentage')
                                        {{ $coupon->value }}% {{ __('off') }}
                                    @elseif($coupon->type == 'fixed')
                                        {{ number_format($coupon->value, 2) }} {{ __('discount') }}
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $coupon->type)) }}
                                    @endif
                                </li>
                                <li><i class="fas fa-calendar-alt me-2"></i>{{ $coupon->start_date->format('Y-m-d') }} &mdash; {{ $coupon->end_date->format('Y-m-d') }}</li>
                                @if($coupon->minimum_purchase)
                                    <li><i class="fas fa-shopping-cart me-2"></i>{{ __('Min purchase') }}: {{ number_format($coupon->minimum_purchase, 2) }}</li>
                                @endif
                            </ul>
                            <a href="{{ route('customer.coupons.show', $coupon) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>{{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        {{ __('No coupons available right now. Check back later!') }}
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
