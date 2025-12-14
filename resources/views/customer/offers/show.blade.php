@extends('layouts.dashboard')

@section('title', $offer->localized_title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.offers.index') }}">{{ __('Offers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $offer->localized_title }}</li>
@endsection

@section('actions')
    <a href="{{ route('customer.offers.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Offers') }}
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        @if($offer->image)
                            <img src="{{ asset('storage/' . $offer->image) }}" alt="{{ $offer->localized_title }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded mb-3" style="height: 220px;">
                                <i class="fas fa-image fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2">{{ ucfirst(str_replace('_', ' ', $offer->type)) }}</span>
                            @if($offer->is_featured)
                                <span class="badge bg-warning"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                            @endif
                        </div>
                        <h2>{{ $offer->localized_title }}</h2>
                        <p class="text-muted">{{ $offer->localized_description }}</p>
                        <div class="mb-3">
                            <strong class="text-primary" style="font-size: 1.5rem;">
                                @if($offer->type == 'percentage')
                                    {{ $offer->discount_percentage }}%
                                @elseif($offer->type == 'fixed')
                                    {{ number_format($offer->discount_amount, 2) }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $offer->type)) }}
                                @endif
                            </strong>
                            <span class="text-muted ms-2">{{ __('Discount') }}</span>
                        </div>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="180">{{ __('Valid Until') }}:</th>
                                <td>{{ $offer->end_date->format('Y-m-d') }} <small class="text-muted">({{ $offer->end_date->diffForHumans() }})</small></td>
                            </tr>
                            <tr>
                                <th>{{ __('Minimum Purchase') }}:</th>
                                <td>{{ $offer->minimum_purchase ? number_format($offer->minimum_purchase, 2) : __('None') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Company') }}:</th>
                                <td>{{ $offer->company->localized_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Category') }}:</th>
                                <td>{{ $offer->category->localized_name ?? '-' }}</td>
                            </tr>
                        </table>
                        <a href="{{ route('customer.scan.index') }}" class="btn btn-primary">
                            <i class="fas fa-qrcode me-2"></i>{{ __('Scan Coupon') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($offer->coupons->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Available Coupons') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Usage Left') }}</th>
                                <th>{{ __('Valid Until') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offer->coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ ucfirst($coupon->status) }}</td>
                                    <td>{{ $coupon->getRemainingUsageCount() }}</td>
                                    <td>{{ $coupon->end_date->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('About the Company') }}</h6>
            </div>
            <div class="card-body">
                <h5>{{ $offer->company->localized_name ?? __('Unknown') }}</h5>
                <p class="text-muted">{{ $offer->company->localized_description ?? __('No description available.') }}</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-phone me-2 text-muted"></i>{{ $offer->company->phone ?? '-' }}</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-muted"></i>{{ $offer->company->email ?? '-' }}</li>
                    @if($offer->company && $offer->company->website)
                        <li class="mb-2"><i class="fas fa-globe me-2 text-muted"></i><a href="{{ $offer->company->website }}" target="_blank">{{ $offer->company->website }}</a></li>
                    @endif
                    <li><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $offer->company->address ?? '-' }}</li>
                </ul>
            </div>
        </div>

        @if($relatedOffers->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Similar Offers') }}</h6>
            </div>
            <div class="card-body">
                @foreach($relatedOffers as $related)
                    <div class="d-flex align-items-center mb-3">
                        @if($related->image)
                            <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->localized_title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1"><a href="{{ route('customer.offers.show', $related) }}">{{ $related->localized_title }}</a></h6>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $related->end_date->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
