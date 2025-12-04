@extends('layouts.new-design')

@section('meta_title', $company->localized_name ?? __('Company Details'))
@section('meta_description', Str::limit($company->localized_description ?? '', 160))

@section('content')
    <x-page-title 
        :title="$company->localized_name ?? __('Company Details')" 
        :subtitle="__('Company Information')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Companies'), 'url' => route('public.companies.index')],
            ['label' => $company->localized_name ?? __('Company Details'), 'url' => '#']
        ]"
    />

    <!-- Company Details Section -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->localized_name }}" class="img-fluid rounded mb-4">
                    @endif
                    <div class="company-details">
                        <h2>{{ $company->localized_name }}</h2>
                        <div class="mb-3">
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        </div>
                        @if($company->localized_description)
                            <div class="mb-4">
                                <p>{{ $company->localized_description }}</p>
                            </div>
                        @endif
                        <div class="company-info-box mb-4">
                            <h5>{{ __('Company Information') }}</h5>
                            <ul class="list-unstyled">
                                @if($company->email)
                                    <li><i class="fas fa-envelope me-2"></i> <strong>{{ __('Email') }}:</strong> <a href="mailto:{{ $company->email }}">{{ $company->email }}</a></li>
                                @endif
                                @if($company->phone)
                                    <li><i class="fas fa-phone me-2"></i> <strong>{{ __('Phone') }}:</strong> <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a></li>
                                @endif
                                @if($company->address)
                                    <li><i class="fas fa-map-marker-alt me-2"></i> <strong>{{ __('Address') }}:</strong> {{ $company->address }}</li>
                                @endif
                                @if($company->website)
                                    <li><i class="fas fa-globe me-2"></i> <strong>{{ __('Website') }}:</strong> <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="company-sidebar">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>{{ __('Quick Info') }}</h5>
                                <p class="mb-2"><strong>{{ __('Status') }}:</strong> 
                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                </p>
                                @if($company->category)
                                    <p class="mb-2"><strong>{{ __('Category') }}:</strong> {{ $company->category }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($company->branches) && $company->branches->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <h3>{{ __('Branches') }}</h3>
                        <div class="row">
                            @foreach($company->branches as $branch)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>{{ $branch->name }}</h5>
                                            @if($branch->address)
                                                <p><i class="fas fa-map-marker-alt me-2"></i> {{ $branch->address }}</p>
                                            @endif
                                            @if($branch->phone)
                                                <p><i class="fas fa-phone me-2"></i> {{ $branch->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($company->offers) && $company->offers->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <h3>{{ __('Available Offers') }}</h3>
                        <div class="row">
                            @foreach($company->offers as $offer)
                                <div class="col-md-4 mb-4">
                                    <div class="offer-card">
                                        <h5>{{ $offer->localized_title }}</h5>
                                        <p>{{ Str::limit($offer->localized_description, 80) }}</p>
                                        <a href="{{ route('public.offers.show', $offer->slug) }}" class="btn btn-sm btn-primary-custom">{{ __('View Offer') }}</a>
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

