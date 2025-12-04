@extends('layouts.app')

@section('title', $card->title)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h1 class="h3 mb-2">{{ $card->title }}</h1>
                    <p class="text-muted mb-3">{{ optional($card->company)->name }}</p>
                    @if($card->image_path)
                        <img src="{{ asset($card->image_path) }}" alt="{{ $card->title }}" class="img-fluid mb-3" />
                    @endif
                    <p>{{ $card->description }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    @auth
                        @if(auth()->user()->hasRole('customer'))
                            <form method="POST" action="{{ route('customer.cards.follow', ['loyaltyCard' => $card->id, 'locale' => app()->getLocale()]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">{{ __('Add to Wallet') }}</button>
                            </form>
                        @else
                            <p class="text-muted">{{ __('Login as a customer to add this card to your wallet.') }}</p>
                        @endif
                    @else
                        <p class="mb-2">{{ __('Start saving for rewards!') }}</p>
                        <a class="btn btn-primary w-100" href="{{ route('login', ['locale' => app()->getLocale()]) }}">{{ __('Login to Add') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

