@extends('layouts.app')

@section('title', __('My Wallet'))

@section('content')
<div class="container py-4">
    <h1 class="mb-3">{{ __('My Wallet') }}</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-4 card-hover">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('Your Cards') }}</span>
            <span class="badge bg-primary bg-opacity-10 text-primary">{{ __('Active') }}</span>
        </div>
        <div class="card-body">
            @if($walletCards->isEmpty())
                <p class="text-muted mb-0">{{ __('Your wallet is empty. Start saving for rewards by following cards below.') }}</p>
            @else
                <div class="row g-3">
                    @foreach($walletCards as $card)
                        <div class="col-sm-6 col-lg-4">
                            <div class="card h-100 card-hover">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="mb-1">{{ $card->title }}</h5>
                                        <span class="badge bg-light text-dark">{{ __('Points') }}: <strong>{{ $card->pivot->points_balance }}</strong></span>
                                    </div>
                                    <p class="mb-2 text-muted">{{ optional($card->company)->name }}</p>
                                </div>
                                <div class="card-footer bg-white d-flex gap-2">
                                    <form method="POST" action="{{ route('customer.cards.unfollow', ['loyaltyCard' => $card->id, 'locale' => app()->getLocale()]) }}">
                                        @csrf
                                        @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-animated">{{ __('Unfollow') }}</button>
                                    </form>
                                    <a href="{{ route('public.card', ['slug' => $card->slug, 'locale' => app()->getLocale()]) }}" class="btn btn-outline-primary btn-sm btn-animated">{{ __('View') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card card-hover">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('Discover Cards') }}</span>
            <span class="text-muted small">{{ __('Browse available loyalty programs') }}</span>
        </div>
        <div class="card-body">
            @if($discoverCards->isEmpty())
                <p class="text-muted mb-0">{{ __('No cards available right now.') }}</p>
            @else
                <div class="row g-3">
                    @foreach($discoverCards as $card)
                        <div class="col-sm-6 col-lg-4">
                            <div class="card h-100 card-hover">
                                <div class="card-body">
                                    <h5 class="mb-1">{{ $card->title }}</h5>
                                    <p class="mb-2 text-muted">{{ optional($card->company)->name }}</p>
                                    @php
                                        $alreadyFollowed = $walletCards->contains('id', $card->id);
                                    @endphp
                                    @if(!$alreadyFollowed)
                                        <form method="POST" action="{{ route('customer.cards.follow', ['loyaltyCard' => $card->id, 'locale' => app()->getLocale()]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm btn-animated">{{ __('Follow') }}</button>
                                        </form>
                                    @else
                                        <span class="badge bg-success">{{ __('Following') }}</span>
                                    @endif
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ route('public.card', ['slug' => $card->slug, 'locale' => app()->getLocale()]) }}" class="btn btn-outline-primary btn-sm btn-animated">{{ __('View') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
