@extends('layouts.dashboard')

@section('title', __('Coupon Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.coupons.index') }}">{{ __('Coupons') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $coupon->code }}</li>
@endsection

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('merchant.coupons.edit', $coupon) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
        </a>
        <a href="{{ route('merchant.coupons.qr-code', $coupon) }}" class="btn btn-primary">
            <i class="fas fa-qrcode me-2"></i>{{ __('View QR Code') }}
        </a>
        <a href="{{ route('merchant.coupons.download-qr', $coupon) }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>{{ __('Download QR') }}
        </a>
        <a href="{{ route('merchant.coupons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Coupon Information') }}</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">{{ __('Code') }}:</th>
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
                        <th>{{ __('Minimum Purchase') }}:</th>
                        <td>{{ $coupon->minimum_purchase ? number_format($coupon->minimum_purchase, 2) : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Usage Limit per User') }}:</th>
                        <td>{{ $coupon->usage_limit_per_user ?? __('Unlimited') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Total Usage Limit') }}:</th>
                        <td>{{ $coupon->total_usage_limit ?? __('Unlimited') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Product') }}:</th>
                        <td>{{ $coupon->product->localized_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Status') }}:</th>
                        <td>
                            @if($coupon->status == 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @elseif($coupon->status == 'used')
                                <span class="badge bg-info">{{ __('Used') }}</span>
                            @elseif($coupon->status == 'expired')
                                <span class="badge bg-danger">{{ __('Expired') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Disabled') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Visibility') }}:</th>
                        <td>{{ $coupon->is_public ? __('Public') : __('Private') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Valid From') }}:</th>
                        <td>{{ $coupon->start_date->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Valid Until') }}:</th>
                        <td>{{ $coupon->end_date->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($coupon->couponUsages->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Usage History') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Original Amount') }}</th>
                                <th>{{ __('Discount') }}</th>
                                <th>{{ __('Final Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Used At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupon->couponUsages as $usage)
                                <tr>
                                    <td>{{ $usage->user->full_name ?? '-' }}</td>
                                    <td>{{ number_format($usage->original_amount, 2) }}</td>
                                    <td>{{ number_format($usage->discount_amount, 2) }}</td>
                                    <td>{{ number_format($usage->final_amount, 2) }}</td>
                                    <td>{{ ucfirst($usage->status) }}</td>
                                    <td>{{ $usage->used_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('QR Code') }}</h6>
            </div>
            <div class="card-body text-center">
                @if($coupon->qr_code)
                    <img src="{{ asset('storage/' . str_replace('/storage/', '', $coupon->qr_code)) }}" alt="QR Code" class="img-fluid border p-3" style="max-width: 300px;">
                    <p class="text-muted mt-3">{{ __('Scan this code at the store to use the coupon.') }}</p>
                @else
                    <div class="alert alert-warning">{{ __('QR code is not generated yet.') }}</div>
                @endif
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Statistics') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Total Usage') }}:</strong>
                    <span class="float-end">{{ $coupon->couponUsages()->count() }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Successful Usage') }}:</strong>
                    <span class="float-end">{{ $coupon->couponUsages()->where('status', 'used')->count() }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Failed Usage') }}:</strong>
                    <span class="float-end">{{ $coupon->couponUsages()->where('status', 'failed')->count() }}</span>
                </div>
                <div>
                    <strong>{{ __('Remaining Usage') }}:</strong>
                    <span class="float-end">{{ $coupon->getRemainingUsageCount() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
