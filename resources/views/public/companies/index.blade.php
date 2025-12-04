@extends('layouts.new-design')

@section('meta_title', __('Partner Companies'))
@section('meta_description', __('Discover our trusted partner merchants and start earning rewards'))

@section('content')
    <x-page-title 
        :title="__('Partner Companies')" 
        :subtitle="__('Discover our trusted partner merchants and start earning rewards')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Companies'), 'url' => '#']
        ]"
    />

    <!-- Companies Section -->
    <section class="section">
        <div class="container">
            <!-- Companies Grid -->
            <div class="row" id="companiesGrid">
                @forelse($companies as $company)
                    <div class="col-md-4 mb-4">
                        <div class="company-card">
                            @if($company->logo)
                                <div class="company-image">
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}">
                                </div>
                            @else
                                <div class="company-image">
                                    <i class="fas fa-building"></i>
                                </div>
                            @endif
                            <div class="company-info">
                                <h5>{{ $company->localized_name }}</h5>
                                @if($company->category)
                                    <p class="text-muted">{{ $company->category }}</p>
                                @endif
                                @if($company->localized_description)
                                    <p>{{ Str::limit($company->localized_description, 100) }}</p>
                                @endif
                                <div class="mb-2">
                                    <span class="badge bg-success me-2">{{ __('Active') }}</span>
                                    @if($company->offers_count ?? $company->offers->count() ?? 0)
                                        <span class="badge bg-info">{{ $company->offers_count ?? $company->offers->count() }} {{ __('Offers') }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('public.companies.show', $company->id) }}" class="btn btn-sm btn-primary-custom">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <p>{{ __('No companies available at the moment.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(method_exists($companies, 'links'))
                <div class="row mt-4">
                    <div class="col-12">
                        {{ $companies->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Become a Partner CTA -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">{{ __('Want to Become a Partner?') }}</h2>
            <p class="cta-description">
                {{ __('Join our network of successful merchants and grow your business') }}
            </p>
            <a href="{{ route('public.contact') }}" class="btn btn-primary-custom btn-lg">{{ __('Contact Us') }}</a>
        </div>
    </section>
@endsection

