@extends('layouts.public')

@section('title', __('Send Points'))

@section('content')
<div class="container py-5" style="max-width: 720px;">
    <div class="text-center mb-4">
        <h1 class="h3">{{ __('Send Points') }}</h1>
        <p class="text-muted mb-0">{{ __('Help a friend reach rewards faster by sending your points.') }}</p>
    </div>

    @if(!auth()->check())
        <div class="alert alert-info">{{ __('You need to sign in to send points.') }}</div>
        <div class="text-center mb-4">
            <a class="btn btn-primary" href="{{ route('login', app()->getLocale()) }}">{{ __('Sign in') }}</a>
        </div>
    @endif

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
            <div class="mb-3">
                <div class="fw-semibold">{{ __('Request from') }}</div>
                <div class="text-muted">{{ $link->customer->name }} • {{ __('Link') }}: {{ $link->uuid }}</div>
                @if($link->loyaltyCard)
                    <div class="small text-muted">{{ __('Requested Card') }}: {{ $link->loyaltyCard->title }}</div>
                @endif
            </div>

            <form method="post" action="{{ route('public.requests.send', [app()->getLocale(), $link->uuid]) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ __('Select Loyalty Card') }}</label>
                    <select class="form-select" name="card_id" {{ $link->card_id ? 'disabled' : '' }}>
                        @if($link->card_id)
                            <option value="{{ $link->card_id }}" selected>
                                {{ $link->loyaltyCard->title }}
                            </option>
                        @else
                            @foreach($suggestedCards as $card)
                                <option value="{{ $card->id }}">{{ $card->title }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($link->card_id)
                        <input type="hidden" name="card_id" value="{{ $link->card_id }}" />
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Points to Send') }}</label>
                    <input type="number" class="form-control" name="points" min="1" placeholder="{{ __('Enter points') }}" required />
                    <div class="form-text">{{ __('FIFO transfer applies; your oldest points are sent first.') }}</div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary" type="submit" {{ !auth()->check() ? 'disabled' : '' }}>
                        {{ __('Send Points') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

