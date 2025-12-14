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
<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Digital Card Information') }}</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="card-type p-4 rounded" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                        <i class="fas fa-credit-card fa-4x mb-3"></i>
                        <h3 class="text-uppercase">{{ __('Digital Card') }}</h3>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>{{ __('Card Number') }}:</strong><br>
                    <h4 class="mt-2">{{ $digitalCard->card_number }}</h4>
                </div>

                <div class="mb-3">
                    <strong>{{ __('Status') }}:</strong><br>
                    @if($digitalCard->isActive())
                        <span class="badge bg-success mt-2">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-danger mt-2">{{ __('Inactive') }}</span>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>{{ __('Expiry Date') }}:</strong><br>
                    <span class="mt-2">{{ $digitalCard->expiry_date->format('Y-m-d') }}</span>
                </div>

                @if($digitalCard->qr_code)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . str_replace('/storage/', '', $digitalCard->qr_code)) }}" 
                             alt="QR Code" class="img-fluid" style="max-width: 200px;">
                        <p class="text-muted mt-2">{{ __('Scan this QR code at the store') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

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
@endsection

