@extends('layouts.dashboard')

@section('title', __('Coupon QR Code'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.coupons.index') }}">{{ __('Coupons') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('QR Code') }}</li>
@endsection

@section('actions')
    <a href="{{ route('merchant.coupons.download-qr', $coupon) }}" class="btn btn-primary">
        <i class="fas fa-download me-2"></i>{{ __('Download QR Code') }}
    </a>
    <a href="{{ route('merchant.coupons.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Coupon QR Code') }}</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <h4>{{ $coupon->code }}</h4>
                    <p class="text-muted">{{ __('Scan this QR code to use the coupon') }}</p>
                </div>

                @if($coupon->qr_code)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . str_replace('/storage/', '', $coupon->qr_code)) }}" 
                             alt="QR Code" class="img-fluid border p-3" style="max-width: 400px;">
                    </div>
                @else
                    <div class="alert alert-warning">
                        {{ __('QR code is being generated. Please refresh the page.') }}
                    </div>
                @endif

                <div class="card border-info mb-4">
                    <div class="card-body">
                        <h6 class="card-title">{{ __('Coupon Details') }}</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">{{ __('Code') }}:</th>
                                <td><strong>{{ $coupon->code }}</strong></td>
                            </tr>
                            <tr>
                                <th>{{ __('Type') }}:</th>
                                <td>{{ ucfirst(str_replace('_', ' ', $coupon->type)) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Value') }}:</th>
                                <td>
                                    @if($coupon->type == 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        {{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Valid Until') }}:</th>
                                <td>{{ $coupon->end_date->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}:</th>
                                <td>
                                    @if($coupon->status == 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($coupon->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('merchant.coupons.download-qr', $coupon) }}" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>{{ __('Download QR Code') }}
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print me-2"></i>{{ __('Print') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .navbar, .sidebar, .btn, .breadcrumb {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection

