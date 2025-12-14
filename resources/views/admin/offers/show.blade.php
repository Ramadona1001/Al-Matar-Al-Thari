@extends('layouts.dashboard')

@section('title', __('Offer Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.offers.index') }}">{{ __('Offers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $offer->localized_title }}</li>
@endsection

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('merchant.offers.edit', $offer) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
        </a>
        <form method="POST" action="{{ route('merchant.offers.toggle-featured', $offer) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-{{ $offer->is_featured ? 'warning' : 'secondary' }}" title="{{ $offer->is_featured ? __('Remove from Featured') : __('Mark as Featured') }}">
                <i class="fas fa-star me-2"></i>{{ $offer->is_featured ? __('Unfeature') : __('Feature') }}
            </button>
        </form>
        <a href="{{ route('merchant.offers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Offer Information') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($offer->image)
                            <img src="{{ asset('storage/' . $offer->image) }}" alt="{{ $offer->localized_title }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h3>{{ $offer->localized_title }}</h3>
                        <p class="text-muted">{{ $offer->localized_description }}</p>
                        <div class="mb-3">
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offer->type)) }}</span>
                            @if($offer->is_featured)
                                <span class="badge bg-warning"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                            @endif
                        </div>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="200">{{ __('Discount') }}:</th>
                                <td>
                                    @if($offer->type == 'percentage')
                                        {{ $offer->discount_percentage }}%
                                    @elseif($offer->type == 'fixed')
                                        {{ number_format($offer->discount_amount, 2) }}
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $offer->type)) }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Minimum Purchase') }}:</th>
                                <td>{{ $offer->minimum_purchase ? number_format($offer->minimum_purchase, 2) : '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Category') }}:</th>
                                <td>{{ $offer->category->localized_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Branch') }}:</th>
                                <td>{{ $offer->branch->name ?? __('All Branches') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}:</th>
                                <td>
                                    @if($offer->status == 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif($offer->status == 'inactive')
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('Draft') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Valid Period') }}:</th>
                                <td>{{ $offer->start_date->format('Y-m-d') }} &mdash; {{ $offer->end_date->format('Y-m-d') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($offer->coupons->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Coupons') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Usage') }}</th>
                                <th>{{ __('Expires') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offer->coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ ucfirst($coupon->status) }}</td>
                                    <td>{{ $coupon->couponUsages()->count() }} / {{ $coupon->total_usage_limit ?? '∞' }}</td>
                                    <td>{{ $coupon->end_date->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('merchant.coupons.show', $coupon) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Performance') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Total Coupons') }}:</strong>
                    <span class="float-end">{{ $stats['total_coupons'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Used Coupons') }}:</strong>
                    <span class="float-end">{{ $stats['used_coupons'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Total Transactions') }}:</strong>
                    <span class="float-end">{{ $stats['total_transactions'] }}</span>
                </div>
                <div>
                    <strong>{{ __('Total Revenue') }}:</strong>
                    <span class="float-end">{{ number_format($stats['total_revenue'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Usage Tips') }}</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Ensure offer dates are current') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Monitor coupon usage for performance') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Use featured offers for higher visibility') }}</li>
                    <li><i class="fas fa-check text-success me-2"></i>{{ __('Adjust discount values based on performance') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
