@extends('layouts.new-design')

@section('meta_title', $offer->localized_title ?? __('Offer Details'))
@section('meta_description', Str::limit($offer->localized_description ?? '', 160))

@section('content')
    <x-page-title 
        :title="$offer->localized_title ?? __('Offer Details')" 
        :subtitle="__('Special Offer Details')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Offers'), 'url' => route('public.offers.index')],
            ['label' => $offer->localized_title ?? __('Offer Details'), 'url' => '#']
        ]"
    />

    <!-- Offer Details Section -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    @if($offer->image)
                        <img src="{{ asset('storage/' . $offer->image) }}" alt="{{ $offer->localized_title }}" class="img-fluid rounded mb-4">
                    @endif
                    <div class="offer-details">
                        <h2>{{ $offer->localized_title }}</h2>
                        <div class="mb-3">
                            @if($offer->discount_percentage)
                                <span class="badge bg-success fs-6">{{ number_format($offer->discount_percentage, 0) }}% {{ __('OFF') }}</span>
                            @elseif($offer->discount_amount)
                                <span class="badge bg-info fs-6">{{ __('Cashback') }}: {{ number_format($offer->discount_amount, 2) }}</span>
                            @endif
                            @if($offer->is_featured)
                                <span class="badge bg-warning">{{ __('Featured') }}</span>
                            @endif
                        </div>
                        <div class="mb-4">
                            <p>{{ $offer->localized_description }}</p>
                        </div>
                        <div class="offer-info-box mb-4">
                            <h5>{{ __('Offer Details') }}</h5>
                            <ul class="list-unstyled">
                                @if($offer->start_date)
                                    <li><i class="fas fa-calendar-check me-2"></i> <strong>{{ __('Start Date') }}:</strong> {{ $offer->start_date->format('M d, Y') }}</li>
                                @endif
                                @if($offer->end_date)
                                    <li><i class="fas fa-calendar-times me-2"></i> <strong>{{ __('End Date') }}:</strong> {{ $offer->end_date->format('M d, Y') }}</li>
                                @endif
                                @if($offer->minimum_purchase)
                                    <li><i class="fas fa-dollar-sign me-2"></i> <strong>{{ __('Minimum Purchase') }}:</strong> {{ number_format($offer->minimum_purchase, 2) }}</li>
                                @endif
                                @if($offer->company)
                                    <li><i class="fas fa-store me-2"></i> <strong>{{ __('Company') }}:</strong> <a href="{{ route('public.companies.show', $offer->company->id) }}">{{ $offer->company->localized_name }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="offer-sidebar">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>{{ __('Quick Info') }}</h5>
                                <p class="mb-2"><strong>{{ __('Status') }}:</strong> 
                                    <span class="badge bg-{{ $offer->isActive() ? 'success' : 'secondary' }}">
                                        {{ $offer->isActive() ? __('Active') : __('Inactive') }}
                                    </span>
                                </p>
                                @if($offer->category)
                                    <p class="mb-2"><strong>{{ __('Category') }}:</strong> {{ $offer->category->localized_name ?? $offer->category->name }}</p>
                                @endif
                                @if($offer->branch)
                                    <p class="mb-2"><strong>{{ __('Branch') }}:</strong> {{ $offer->branch->name }}</p>
                                @endif
                            </div>
                        </div>
                        @auth
                            <div class="card">
                                <div class="card-body">
                                    <a href="{{ route('customer.offers.show', $offer->id) }}" class="btn btn-primary-custom w-100">{{ __('Get This Offer') }}</a>
                                </div>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-body">
                                    <a href="{{ route('register', ['type' => 'customer']) }}" class="btn btn-primary-custom w-100">{{ __('Sign Up to Get Offer') }}</a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            @if(isset($related) && $related->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <h3>{{ __('Related Offers') }}</h3>
                        <div class="row">
                            @foreach($related as $relatedOffer)
                                <div class="col-md-4 mb-4">
                                    <div class="offer-card">
                                        <h5>{{ $relatedOffer->localized_title }}</h5>
                                        <p>{{ Str::limit($relatedOffer->localized_description, 80) }}</p>
                                        <a href="{{ route('public.offers.show', $relatedOffer->slug) }}" class="btn btn-sm btn-primary-custom">{{ __('View') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

