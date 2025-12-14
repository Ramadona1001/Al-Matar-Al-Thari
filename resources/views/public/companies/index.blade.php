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
    <section class="section" style="padding: 80px 0; background: #f8f9fa;">
        <div class="container">
            <!-- Companies Grid -->
            <div class="row" id="companiesGrid">
                @forelse($companies as $company)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="{{ route('public.companies.show', $company->id) }}" class="text-decoration-none">
                            <div class="company-card-modern" style="background: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                                {{-- Logo Section --}}
                                <div class="company-logo-wrapper" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 2rem; display: flex; align-items: center; justify-content: center; min-height: 200px;">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" 
                                             alt="{{ $company->localized_name }}" 
                                             style="max-width: 100%; max-height: 150px; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));">
                                    @else
                                        <div style="display: flex; align-items: center; justify-content: center; width: 120px; height: 120px; background: #3D4F60; border-radius: 15px;">
                                            <i class="fas fa-building" style="font-size: 3rem; color: #ffffff;"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Info Section --}}
                                <div class="company-info" style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                                    <h5 style="font-size: 1.25rem; font-weight: 700; color: #3D4F60; margin-bottom: 0.75rem;">{{ $company->localized_name }}</h5>
                                    
                                    @if($company->localized_description)
                                        <p style="color: #6c757d; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem; flex: 1;">{{ Str::limit($company->localized_description, 120) }}</p>
                                    @endif
                                    
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge" style="background: #d4edda; color: #155724; padding: 0.5rem 0.75rem; border-radius: 20px; font-weight: 600;">
                                            <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                                        </span>
                                        @php
                                            $offersCount = $company->offers_count ?? ($company->offers->count() ?? 0);
                                            $productsCount = $company->products_count ?? ($company->products->count() ?? 0);
                                        @endphp
                                        @if($offersCount > 0)
                                            <span class="badge" style="background: #d1ecf1; color: #0c5460; padding: 0.5rem 0.75rem; border-radius: 20px; font-weight: 600;">
                                                <i class="fas fa-tag me-1"></i>{{ $offersCount }} {{ __('Offers') }}
                                            </span>
                                        @endif
                                        @if($productsCount > 0)
                                            <span class="badge" style="background: #fff3cd; color: #856404; padding: 0.5rem 0.75rem; border-radius: 20px; font-weight: 600;">
                                                <i class="fas fa-box me-1"></i>{{ $productsCount }} {{ __('Products') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <span style="color: #3D4F60; font-weight: 600; font-size: 0.95rem;">
                                            {{ __('View Details') }} <i class="fas fa-arrow-left ms-1" style="transition: transform 0.3s ease;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center" style="background: #ffffff; border-radius: 15px; padding: 3rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-building" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                            <p style="color: #3D4F60; font-size: 1.1rem; margin: 0;">{{ __('No companies available at the moment.') }}</p>
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

    <style>
        .company-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
        .company-card-modern:hover .fa-arrow-left {
            transform: translateX(-5px);
        }
        @media (max-width: 768px) {
            .company-logo-wrapper {
                min-height: 150px !important;
                padding: 1.5rem !important;
            }
        }
    </style>

    <!-- Become a Partner CTA -->
    {{-- <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">{{ __('Want to Become a Partner?') }}</h2>
            <p class="cta-description">
                {{ __('Join our network of successful merchants and grow your business') }}
            </p>
            <a href="{{ route('public.contact') }}" class="btn btn-primary-custom btn-lg">{{ __('Contact Us') }}</a>
        </div>
    </section> --}}
@endsection

