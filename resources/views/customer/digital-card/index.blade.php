@extends('layouts.dashboard')

@section('title', __('My Digital Card'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Digital Card') }}</li>
@endsection

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('customer.digital-card.download-qr') }}" class="btn btn-primary">
            <i class="fas fa-qrcode me-2"></i>{{ __('Download QR Code') }}
        </a>
        <a href="{{ route('customer.digital-card.download') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>{{ __('Download Card as PNG') }}
        </a>
    </div>
@endsection

@section('content')
@php
    try {
        $site = \App\Models\SiteSetting::getSettings();
        $siteLogo = !empty($site->logo_path) ? asset('storage/' . $site->logo_path) : null;
        $brandName = is_array($site->brand_name ?? null)
            ? ($site->brand_name[app()->getLocale()] ?? reset($site->brand_name ?? []))
            : ($site->brand_name ?? config('app.name'));
    } catch (\Exception $e) {
        $siteLogo = null;
        $brandName = config('app.name');
    }
    $user = auth()->user();
@endphp

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <!-- Business Card Style Digital Card -->
        <div class="card shadow-lg border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
            <!-- Digital Card Display -->
            <div class="business-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 40px; min-height: 400px; position: relative;">
                <!-- Logo at Top -->
                <div class="text-center mb-4">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $brandName }}" style="max-height: 60px; max-width: 200px; object-fit: contain;">
                    @else
                        <h3 style="color: #3D4F60; font-weight: 700; margin: 0;">{{ $brandName }}</h3>
                    @endif
                </div>

                <!-- Card Content -->
                <div class="row align-items-center h-100" style="min-height: 300px;">
                    <!-- QR Code - Left Side -->
                    <div class="col-md-5 text-center">
                        @if($digitalCard->qr_code)
                            <div class="qr-code-container" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: inline-block;">
                                <img src="{{ asset('storage/' . str_replace('/storage/', '', $digitalCard->qr_code)) }}" 
                                     alt="QR Code" 
                                     style="width: 180px; height: 180px; display: block;">
                            </div>
                            <p class="text-muted mt-3 mb-0" style="font-size: 0.9rem;">{{ __('Scan this QR code at the store') }}</p>
                        @else
                            <div class="qr-code-placeholder" style="background: #f0f0f0; width: 180px; height: 180px; border-radius: 15px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-qrcode" style="font-size: 80px; color: #ccc;"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Customer Information - Right Side -->
                    <div class="col-md-7">
                        <div class="customer-info" style="color: #3D4F60;">
                            <div class="mb-4">
                                <h2 style="color: #3D4F60; font-weight: 700; font-size: 2rem; margin-bottom: 10px;">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </h2>
                                <p style="color: #6c757d; font-size: 1rem; margin: 0;">{{ __('Loyalty Member') }}</p>
                            </div>

                            <div class="card-details" style="background: rgba(61, 79, 96, 0.05); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                                <div class="mb-3">
                                    <div style="color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                                        {{ __('Card Number') }}
                                    </div>
                                    <div style="color: #3D4F60; font-size: 1.5rem; font-weight: 700; font-family: 'Courier New', monospace;">
                                        {{ $digitalCard->card_number }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div style="color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                                            {{ __('Status') }}
                                        </div>
                                        <div>
                                            @if($digitalCard->isActive())
                                                <span class="badge" style="background: #28a745; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge" style="background: #dc3545; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div style="color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                                            {{ __('Expiry Date') }}
                                        </div>
                                        <div style="color: #3D4F60; font-size: 1rem; font-weight: 600;">
                                            {{ $digitalCard->expiry_date->format('Y-m-d') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($user->email)
                                <div class="contact-info">
                                    <div style="color: #6c757d; font-size: 0.9rem;">
                                        <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                                    </div>
                                    @if($user->phone)
                                        <div style="color: #6c757d; font-size: 0.9rem; margin-top: 8px;">
                                            <i class="fas fa-phone me-2"></i>{{ $user->phone }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Decorative Element -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #3b82f6 0%, #2563eb 50%, #1e40af 100%);"></div>
            </div>
        </div>

        <!-- Card Benefits Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Card Benefits') }}</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $benefits = $digitalCard->getBenefits();
                        @endphp
                        
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-star text-warning me-2"></i>
                                <strong>{{ __('Loyalty Points') }}:</strong> {{ $benefits['description'] }}
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                {{ $benefits['points_system'] }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('How to Use') }}</h6>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li class="mb-2">{{ __('Download your digital card as PNG or QR code') }}</li>
                            <li class="mb-2">{{ __('Visit a participating store') }}</li>
                            <li class="mb-2">{{ __('Present your card or QR code at checkout') }}</li>
                            <li>{{ __('Earn loyalty points on every purchase based on admin settings') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.business-card {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

@media (max-width: 768px) {
    .business-card {
        padding: 30px 20px !important;
    }
    
    .business-card .row > div {
        margin-bottom: 30px;
    }
}

@media print {
    .business-card {
        page-break-inside: avoid;
    }
}
</style>
@endsection

