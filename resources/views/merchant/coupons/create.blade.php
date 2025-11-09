@extends('layouts.dashboard')

@section('title', __('Create Coupon'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.coupons.index') }}">{{ __('Coupons') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Create New Coupon') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('merchant.coupons.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">{{ __('Coupon Code') }}</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code') }}" placeholder="{{ __('Leave empty to auto-generate') }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="offer_id" class="form-label">{{ __('Related Offer') }}</label>
                                <select class="form-select @error('offer_id') is-invalid @enderror" id="offer_id" name="offer_id">
                                    <option value="">{{ __('Select Offer (optional)') }}</option>
                                    @foreach($offers as $offer)
                                        <option value="{{ $offer->id }}" {{ old('offer_id') == $offer->id ? 'selected' : '' }}>
                                            {{ $offer->localized_title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('offer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">{{ __('Coupon Type') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>{{ __('Percentage Discount') }}</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Discount') }}</option>
                                    <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>{{ __('Free Shipping') }}</option>
                                    <option value="buy_x_get_y" {{ old('type') == 'buy_x_get_y' ? 'selected' : '' }}>{{ __('Buy X Get Y') }}</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="value" class="form-label">{{ __('Value') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('value') is-invalid @enderror"
                                       id="value" name="value" value="{{ old('value') }}" step="0.01" min="0" required>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_purchase" class="form-label">{{ __('Minimum Purchase') }}</label>
                                <input type="number" class="form-control @error('minimum_purchase') is-invalid @enderror"
                                       id="minimum_purchase" name="minimum_purchase" value="{{ old('minimum_purchase') }}" step="0.01" min="0">
                                @error('minimum_purchase')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="usage_limit_per_user" class="form-label">{{ __('Usage Limit per User') }}</label>
                                <input type="number" class="form-control @error('usage_limit_per_user') is-invalid @enderror"
                                       id="usage_limit_per_user" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', 1) }}" min="1">
                                @error('usage_limit_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="total_usage_limit" class="form-label">{{ __('Total Usage Limit') }}</label>
                                <input type="number" class="form-control @error('total_usage_limit') is-invalid @enderror"
                                       id="total_usage_limit" name="total_usage_limit" value="{{ old('total_usage_limit') }}" min="1">
                                @error('total_usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1"
                                       {{ old('is_public') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">
                                    {{ __('Make this coupon public') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('merchant.coupons.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('Create Coupon') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
