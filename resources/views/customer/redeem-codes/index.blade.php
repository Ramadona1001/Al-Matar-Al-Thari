@extends('layouts.dashboard')

@section('title', __('Redeem Points Code'))

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <h1 class="h4 mb-3">{{ __('Redeem Points Code') }}</h1>
    <p class="text-muted">{{ __('Enter a valid code to add points to your wallet.') }}</p>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('customer.redeem-codes.store', app()->getLocale()) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ __('4-digit code or token') }}</label>
                    <input type="text" class="form-control" name="code" maxlength="32" placeholder="1234" required />
                    <div class="form-text">{{ __('Codes are single-use and may expire based on configuration.') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Select Loyalty Card') }}</label>
                    <select class="form-select" name="card_id">
                        <option value="">{{ __('Use default from code') }}</option>
                        @foreach($cards as $card)
                            <option value="{{ $card->id }}">{{ $card->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">{{ __('Redeem') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-muted mt-3 small">
        {{ __('Default code expiry (minutes):') }} {{ $defaultExpiryMinutes }}
    </div>
</div>
@endsection

