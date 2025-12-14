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

    <!-- Company Header Section -->
    <section class="section" style="padding: 60px 0; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-4 text-center mb-4 mb-lg-0">
                    @if($company->logo)
                        <div style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); display: inline-block;">
                            <img src="{{ asset('storage/' . $company->logo) }}" 
                                 alt="{{ $company->localized_name }}" 
                                 style="max-width: 200px; max-height: 150px; object-fit: contain;">
                        </div>
                    @else
                        <div style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); display: inline-block; width: 200px; height: 150px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-building" style="font-size: 4rem; color: #3D4F60;"></i>
                        </div>
                    @endif
                </div>
                <div class="col-lg-9 col-md-8">
                    <h1 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $company->localized_name }}</h1>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge" style="background: #d4edda; color: #155724; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
                            <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                        </span>
                    </div>
                    @if($company->localized_description)
                        <p style="font-size: 1.1rem; color: #6c757d; line-height: 1.8; margin-bottom: 0;">{{ $company->localized_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Company Details Section -->
    <section class="section" style="padding: 80px 0; background: #ffffff;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="company-info-box" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
                        <h3 style="font-size: 1.75rem; font-weight: 700; color: #3D4F60; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f8f9fa;">
                            <i class="fas fa-info-circle me-2"></i>{{ __('Company Information') }}
                        </h3>
                        <ul class="list-unstyled" style="margin: 0;">
                            @if($company->email)
                                <li style="padding: 1rem 0; border-bottom: 1px solid #f8f9fa;">
                                    <i class="fas fa-envelope me-3" style="color: #3D4F60; width: 24px;"></i>
                                    <strong style="color: #3D4F60;">{{ __('Email') }}:</strong> 
                                    <a href="mailto:{{ $company->email }}" style="color: #6c757d; text-decoration: none; margin-left: 0.5rem;">{{ $company->email }}</a>
                                </li>
                            @endif
                            @if($company->phone)
                                <li style="padding: 1rem 0; border-bottom: 1px solid #f8f9fa;">
                                    <i class="fas fa-phone me-3" style="color: #3D4F60; width: 24px;"></i>
                                    <strong style="color: #3D4F60;">{{ __('Phone') }}:</strong> 
                                    <a href="tel:{{ $company->phone }}" style="color: #6c757d; text-decoration: none; margin-left: 0.5rem;">{{ $company->phone }}</a>
                                </li>
                            @endif
                            @if($company->address)
                                <li style="padding: 1rem 0; border-bottom: 1px solid #f8f9fa;">
                                    <i class="fas fa-map-marker-alt me-3" style="color: #3D4F60; width: 24px;"></i>
                                    <strong style="color: #3D4F60;">{{ __('Address') }}:</strong> 
                                    <span style="color: #6c757d; margin-left: 0.5rem;">{{ $company->address }}</span>
                                </li>
                            @endif
                            @if($company->website)
                                <li style="padding: 1rem 0;">
                                    <i class="fas fa-globe me-3" style="color: #3D4F60; width: 24px;"></i>
                                    <strong style="color: #3D4F60;">{{ __('Website') }}:</strong> 
                                    <a href="{{ $company->website }}" target="_blank" style="color: #6c757d; text-decoration: none; margin-left: 0.5rem;">{{ $company->website }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="company-sidebar">
                        <div style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
                            <h5 style="font-size: 1.25rem; font-weight: 700; color: #3D4F60; margin-bottom: 1.5rem;">
                                <i class="fas fa-chart-line me-2"></i>{{ __('Quick Info') }}
                            </h5>
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: #3D4F60;">{{ __('Status') }}:</strong> 
                                <span class="badge" style="background: #d4edda; color: #155724; padding: 0.5rem 0.75rem; border-radius: 20px; font-weight: 600; margin-left: 0.5rem;">
                                    {{ __('Approved') }}
                                </span>
                            </div>
                            @if($company->category)
                                <div style="margin-bottom: 1rem;">
                                    <strong style="color: #3D4F60;">{{ __('Category') }}:</strong> 
                                    <span style="color: #6c757d; margin-left: 0.5rem;">{{ $company->category }}</span>
                                </div>
                            @endif
                            @php
                                $offersCount = $company->offers->count() ?? 0;
                                $productsCount = $company->products->count() ?? 0;
                            @endphp
                            @if($offersCount > 0 || $productsCount > 0)
                                <div style="padding-top: 1rem; border-top: 1px solid #f8f9fa;">
                                    @if($offersCount > 0)
                                        <div style="margin-bottom: 0.75rem;">
                                            <i class="fas fa-tag me-2" style="color: #3D4F60;"></i>
                                            <strong style="color: #3D4F60;">{{ __('Offers') }}:</strong> 
                                            <span style="color: #6c757d; margin-left: 0.5rem;">{{ $offersCount }}</span>
                                        </div>
                                    @endif
                                    @if($productsCount > 0)
                                        <div>
                                            <i class="fas fa-box me-2" style="color: #3D4F60;"></i>
                                            <strong style="color: #3D4F60;">{{ __('Products') }}:</strong> 
                                            <span style="color: #6c757d; margin-left: 0.5rem;">{{ $productsCount }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($company->products) && $company->products->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 style="font-size: 2rem; font-weight: 700; color: #3D4F60; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #f8f9fa;">
                            <i class="fas fa-box me-2"></i>{{ __('Products') }}
                        </h3>
                        <div class="row">
                            @foreach($company->products as $product)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <a href="{{ route('customer.products.show', $product) }}" class="text-decoration-none">
                                        <div class="product-card-modern" style="background: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                                            @if($product->image)
                                                <div style="width: 100%; height: 200px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                                         alt="{{ $product->localized_name }}" 
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            @else
                                                <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-box" style="font-size: 3rem; color: #6c757d;"></i>
                                                </div>
                                            @endif
                                            <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                                                <h5 style="font-size: 1.1rem; font-weight: 700; color: #3D4F60; margin-bottom: 0.75rem; min-height: 3rem;">{{ $product->localized_name }}</h5>
                                                @if($product->localized_description)
                                                    <p style="color: #6c757d; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1rem; flex: 1;">{{ Str::limit($product->localized_description, 80) }}</p>
                                                @endif
                                                @if($product->price)
                                                    <div style="margin-top: auto;">
                                                        <span style="font-size: 1.25rem; font-weight: 700; color: #3D4F60;">{{ number_format($product->price, 2) }} {{ __('SAR') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($company->branches) && $company->branches->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 style="font-size: 2rem; font-weight: 700; color: #3D4F60; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #f8f9fa;">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ __('Branches') }}
                        </h3>
                        <div class="row">
                            @foreach($company->branches as $branch)
                                <div class="col-md-6 mb-4">
                                    <div style="background: #ffffff; padding: 1.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                                        <h5 style="font-size: 1.25rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $branch->name }}</h5>
                                        @if($branch->address)
                                            <p style="color: #6c757d; margin-bottom: 0.5rem;">
                                                <i class="fas fa-map-marker-alt me-2" style="color: #3D4F60;"></i>{{ $branch->address }}
                                            </p>
                                        @endif
                                        @if($branch->phone)
                                            <p style="color: #6c757d; margin-bottom: 0;">
                                                <i class="fas fa-phone me-2" style="color: #3D4F60;"></i>
                                                <a href="tel:{{ $branch->phone }}" style="color: #6c757d; text-decoration: none;">{{ $branch->phone }}</a>
                                            </p>
                                        @endif
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
                        <h3 style="font-size: 2rem; font-weight: 700; color: #3D4F60; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #f8f9fa;">
                            <i class="fas fa-tag me-2"></i>{{ __('Available Offers') }}
                        </h3>
                        <div class="row">
                            @foreach($company->offers as $offer)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <a href="{{ route('public.offers.show', $offer->slug) }}" class="text-decoration-none">
                                        <div style="background: #ffffff; padding: 1.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                                            @if($offer->image)
                                                <div style="width: 100%; height: 150px; overflow: hidden; background: #f8f9fa; border-radius: 10px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ asset('storage/' . $offer->image) }}" 
                                                         alt="{{ $offer->localized_title }}" 
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            @endif
                                            <h5 style="font-size: 1.25rem; font-weight: 700; color: #3D4F60; margin-bottom: 0.75rem;">{{ $offer->localized_title }}</h5>
                                            @if($offer->localized_description)
                                                <p style="color: #6c757d; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem; flex: 1;">{{ Str::limit($offer->localized_description, 100) }}</p>
                                            @endif
                                            <div style="margin-top: auto;">
                                                <span style="color: #3D4F60; font-weight: 600; font-size: 0.95rem;">
                                                    {{ __('View Offer') }} <i class="fas fa-arrow-left ms-1" style="transition: transform 0.3s ease;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        .product-card-modern:hover,
        a .product-card-modern:hover,
        a > div[style*="box-shadow"]:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
        a > div[style*="box-shadow"]:hover .fa-arrow-left {
            transform: translateX(-5px);
        }
    </style>
        </div>
    </section>
@endsection

