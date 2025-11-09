@extends('layouts.dashboard')

@section('title', __('My Digital Card'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Digital Card') }}</li>
@endsection

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('customer.digital-card.download-qr') }}" class="btn btn-primary">
            <i class="fas fa-download me-2"></i>{{ __('Download QR Code') }}
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
                    @if($digitalCard->type == 'silver')
                        <div class="card-type silver p-4 rounded" style="background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%); color: white;">
                            <i class="fas fa-credit-card fa-4x mb-3"></i>
                            <h3 class="text-uppercase">{{ __('Silver Card') }}</h3>
                        </div>
                    @elseif($digitalCard->type == 'gold')
                        <div class="card-type gold p-4 rounded" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white;">
                            <i class="fas fa-credit-card fa-4x mb-3"></i>
                            <h3 class="text-uppercase">{{ __('Gold Card') }}</h3>
                        </div>
                    @else
                        <div class="card-type platinum p-4 rounded" style="background: linear-gradient(135deg, #E5E4E2 0%, #C0C0C0 100%); color: white;">
                            <i class="fas fa-credit-card fa-4x mb-3"></i>
                            <h3 class="text-uppercase">{{ __('Platinum Card') }}</h3>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>{{ __('Card Number') }}:</strong><br>
                    <h4 class="mt-2">{{ $digitalCard->card_number }}</h4>
                </div>

                <div class="mb-3">
                    <strong>{{ __('Discount Percentage') }}:</strong><br>
                    <h4 class="mt-2 text-primary">{{ $digitalCard->discount_percentage }}%</h4>
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
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>{{ __('Discount') }}:</strong> {{ $benefits['discount_percentage'] }}% {{ __('on all purchases') }}
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-star text-warning me-2"></i>
                        <strong>{{ __('Loyalty Points') }}:</strong> {{ ($benefits['loyalty_points_multiplier'] - 1) * 100 }}% {{ __('bonus points') }}
                    </li>
                </ul>

                <hr>

                <h6 class="mb-3">{{ __('Upgrade Card') }}</h6>
                <form method="POST" action="{{ route('customer.digital-card.upgrade') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="card_type" class="form-label">{{ __('Card Type') }}</label>
                        <select class="form-select" id="card_type" name="card_type" required>
                            <option value="silver" {{ $digitalCard->type == 'silver' ? 'selected' : '' }}>{{ __('Silver') }} (5% discount)</option>
                            <option value="gold" {{ $digitalCard->type == 'gold' ? 'selected' : '' }}>{{ __('Gold') }} (10% discount)</option>
                            <option value="platinum" {{ $digitalCard->type == 'platinum' ? 'selected' : '' }}>{{ __('Platinum') }} (15% discount)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-arrow-up me-2"></i>{{ __('Upgrade Card') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('How to Use') }}</h6>
            </div>
            <div class="card-body">
                <ol>
                    <li class="mb-2">{{ __('Download your QR code or show it on your phone') }}</li>
                    <li class="mb-2">{{ __('Visit a participating store') }}</li>
                    <li class="mb-2">{{ __('Present your QR code at checkout') }}</li>
                    <li class="mb-2">{{ __('Enjoy your discount automatically applied') }}</li>
                    <li>{{ __('Earn loyalty points on every purchase') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

