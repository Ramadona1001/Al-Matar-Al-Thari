@extends('layouts.dashboard')

@section('title', __('Coupons Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Coupons') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Coupons') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Active') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Used') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['used'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('Expired') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expired'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters') }}</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="company_id" class="form-label">{{ __('Company') }}</label>
                <select name="company_id" id="company_id" class="form-select">
                    <option value="">{{ __('All Companies') }}</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->localized_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>{{ __('Used') }}</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="offer_id" class="form-label">{{ __('Offer') }}</label>
                <select name="offer_id" id="offer_id" class="form-select">
                    <option value="">{{ __('All Offers') }}</option>
                    @foreach($offers as $offer)
                        <option value="{{ $offer->id }}" {{ request('offer_id') == $offer->id ? 'selected' : '' }}>
                            {{ $offer->localized_title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('Search coupons...') }}">
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>{{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Coupons Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Coupons List') }}</h6>
    </div>
    <div class="card-body">
        @if($coupons->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" data-dt-init="false">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Offer') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Dates') }}</th>
                            <th width="150">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td>
                                    <code>{{ $coupon->code }}</code>
                                    @if($coupon->is_public)
                                        <span class="badge bg-info ms-2">{{ __('Public') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.companies.show', $coupon->company) }}">
                                        {{ $coupon->company->localized_name }}
                                    </a>
                                </td>
                                <td>
                                    @if($coupon->offer)
                                        <a href="{{ route('admin.offers.show', $coupon->offer) }}">
                                            {{ $coupon->offer->localized_title }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($coupon->type) }}</span>
                                </td>
                                <td>
                                    @if($coupon->type == 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        {{ number_format($coupon->value, 2) }} {{ __('SAR') }}
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->status == 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif($coupon->status == 'used')
                                        <span class="badge bg-info">{{ __('Used') }}</span>
                                    @elseif($coupon->status == 'expired')
                                        <span class="badge bg-danger">{{ __('Expired') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Disabled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <strong>{{ __('Start') }}:</strong> {{ $coupon->start_date->format('Y-m-d') }}<br>
                                        <strong>{{ __('End') }}:</strong> {{ $coupon->end_date->format('Y-m-d') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                           class="btn btn-sm btn-info" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                           class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $coupon->status === 'active' ? 'warning' : 'success' }}" 
                                                    title="{{ $coupon->status === 'active' ? __('Disable') : __('Activate') }}">
                                                <i class="fas fa-{{ $coupon->status === 'active' ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this coupon?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-ticket-alt fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">{{ __('No coupons found.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

