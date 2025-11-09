@extends('layouts.dashboard')

@section('title', __('Coupons Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Coupons') }}</li>
@endsection

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('merchant.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('Create Coupon') }}
        </a>
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
            <i class="fas fa-layer-group me-2"></i>{{ __('Bulk Generate') }}
        </button>
    </div>
@endsection

@section('content')
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
        <form method="GET" action="{{ route('merchant.coupons.index') }}" class="row g-3">
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
            <div class="col-md-4">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('Search by code...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
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
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Value') }}</th>
                        <th>{{ __('Offer') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Usage') }}</th>
                        <th>{{ __('Valid Until') }}</th>
                        <th width="200">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong></td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $coupon->type)) }}</span>
                            </td>
                            <td>
                                @if($coupon->type == 'percentage')
                                    {{ $coupon->value }}%
                                @else
                                    {{ number_format($coupon->value, 2) }}
                                @endif
                            </td>
                            <td>{{ $coupon->offer->localized_title ?? '-' }}</td>
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
                                {{ $coupon->couponUsages()->count() }} / 
                                {{ $coupon->total_usage_limit ?? '∞' }}
                            </td>
                            <td>{{ $coupon->end_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('merchant.coupons.show', $coupon) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('merchant.coupons.qr-code', $coupon) }}" class="btn btn-sm btn-primary" title="{{ __('QR Code') }}">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                    <a href="{{ route('merchant.coupons.download-qr', $coupon) }}" class="btn btn-sm btn-success" title="{{ __('Download QR') }}">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('merchant.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('merchant.coupons.destroy', $coupon) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                                onclick="return confirm('{{ __('Are you sure?') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('No coupons found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $coupons->links() }}
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div class="modal fade" id="bulkGenerateModal" tabindex="-1" aria-labelledby="bulkGenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('merchant.coupons.bulk-generate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkGenerateModalLabel">{{ __('Bulk Generate Coupons') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="10" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="percentage">{{ __('Percentage') }}</option>
                            <option value="fixed">{{ __('Fixed Amount') }}</option>
                            <option value="free_shipping">{{ __('Free Shipping') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="value" class="form-label">{{ __('Value') }} <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="value" name="value" 
                               step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">{{ __('End Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="disabled">{{ __('Disabled') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Generate') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

