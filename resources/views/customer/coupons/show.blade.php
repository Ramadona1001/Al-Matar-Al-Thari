@extends('layouts.dashboard')

@section('title', __('Coupon Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.coupons.index') }}">{{ __('Coupons') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $coupon->code }}</li>
@endsection

@section('actions')
    <a href="{{ route('customer.scan.index') }}" class="btn btn-primary">
        <i class="fas fa-qrcode me-2"></i>{{ __('Scan Coupon') }}
    </a>
    <a href="{{ route('customer.coupons.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h2 class="mb-3">{{ $coupon->code }}</h2>
                <div class="mb-3">
                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $coupon->type)) }}</span>
                    <small class="text-muted ms-2"><i class="fas fa-clock me-1"></i>{{ $coupon->end_date->diffForHumans() }}</small>
                </div>
                <table class="table table-borderless">
                    <tr>
                        <th width="200">{{ __('Discount') }}:</th>
                        <td>
                            @if($coupon->type == 'percentage')
                                {{ $coupon->value }}% {{ __('off your purchase') }}
                            @elseif($coupon->type == 'fixed')
                                {{ number_format($coupon->value, 2) }} {{ __('off your purchase') }}
                            @else
                                {{ ucfirst(str_replace('_', ' ', $coupon->type)) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Valid Period') }}:</th>
                        <td>{{ $coupon->start_date->format('Y-m-d H:i') }} &mdash; {{ $coupon->end_date->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Minimum Purchase') }}:</th>
                        <td>{{ $coupon->minimum_purchase ? number_format($coupon->minimum_purchase, 2) : __('None') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Usage Limit') }}:</th>
                        <td>{{ $coupon->usage_limit_per_user ?? __('Unlimited per user') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Total Remaining') }}:</th>
                        <td>{{ $coupon->getRemainingUsageCount() }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Company') }}:</th>
                        <td>{{ $coupon->company->name ?? '-' }}</td>
                    </tr>
                    @if($coupon->offer)
                    <tr>
                        <th>{{ __('Offer') }}:</th>
                        <td>{{ $coupon->offer->localized_title }}</td>
                    </tr>
                    @endif
                </table>
                <p class="text-muted">{{ __('Present this coupon and your digital card at checkout to redeem the discount.') }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('How to Redeem') }}</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li class="mb-2">{{ __('Visit a participating store of the company.') }}</li>
                    <li class="mb-2">{{ __('Pick the products or services you want.') }}</li>
                    <li class="mb-2">{{ __('Show this coupon and your digital card at checkout.') }}</li>
                    <li>{{ __('Scan the coupon QR code using the Scan page to apply the discount.') }}</li>
                </ol>
            </div>
        </div>

        @if($similarCoupons->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Other Coupons from this Company') }}</h6>
            </div>
            <div class="card-body">
                @foreach($similarCoupons as $similar)
                    <div class="border-bottom pb-2 mb-2">
                        <h6 class="mb-1"><a href="{{ route('customer.coupons.show', $similar) }}">{{ $similar->code }}</a></h6>
                        <small class="text-muted">{{ $similar->end_date->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
