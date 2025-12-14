@extends('layouts.dashboard')

@section('title', __('My Wallet'))

@section('content')
<section class="section-intro fade-in-up mb-5">
    <div class="row g-4 align-items-center">
        <div class="col-xl-8">
            <div class="text-white">
                <span class="badge rounded-pill bg-white text-primary bg-opacity-15 mb-3">{{ __('wallet') }}</span>
                <h2 class="display-6 fw-semibold mb-3">{{ __('My Wallet') }}</h2>
                <p class="text-white-50 mb-0">{{ __('Manage your loyalty cards and discover new programs') }}</p>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="glass-card p-4 h-100 text-white fade-in-up delay-1">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="text-white-50 text-uppercase small fw-semibold mb-1">{{ __('wallet_summary') }}</p>
                        <h4 class="fw-semibold mb-0">{{ __('your_cards') }}</h4>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25 text-white">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                    <li class="d-flex justify-content-between"><span>{{ __('active_cards') }}</span><span class="fw-semibold text-white">{{ $walletCards->count() }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('available_cards') }}</span><span class="fw-semibold text-white">{{ $discoverCards->count() }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Your Cards Section -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-primary-subtle">
                            <i class="fas fa-credit-card text-primary"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('your_loyalty_cards') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('Your Cards') }}</h5>
                        </div>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">{{ __('Active') }}</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if($walletCards->isEmpty())
                    <div class="empty-state-modern text-center py-5">
                        <div class="empty-icon-wrapper mb-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="text-muted mb-0 fw-semibold">{{ __('Your wallet is empty. Start saving for rewards by following cards below.') }}</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($walletCards as $card)
                            <div class="col-sm-6 col-lg-4">
                                <div class="card border-0 shadow-sm h-100 card-hover">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="fw-semibold mb-0 text-gray-900">{{ $card->title }}</h6>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                                                <i class="fas fa-star me-1"></i>{{ __('Points') }}: <strong>{{ $card->pivot->points_balance ?? 0 }}</strong>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-building me-1 text-primary"></i>
                                            {{ optional($card->company)->localized_name ?? optional($card->company)->name ?? __('No Company') }}
                                        </p>
                                        <div class="d-flex gap-2">
                                            <form method="POST" action="{{ route('customer.cards.unfollow', ['loyaltyCard' => $card->id, 'locale' => app()->getLocale()]) }}" class="flex-fill">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 btn-animated">
                                                    <i class="fas fa-times me-1"></i>{{ __('Unfollow') }}
                                                </button>
                                            </form>
                                            <a href="{{ route('public.card', ['slug' => $card->slug, 'locale' => app()->getLocale()]) }}" class="btn btn-primary btn-sm flex-fill btn-animated">
                                                <i class="fas fa-eye me-1"></i>{{ __('View') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Discover Cards Section -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-info-subtle">
                            <i class="fas fa-compass text-info"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('explore') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('Discover Cards') }}</h5>
                        </div>
                    </div>
                    <span class="text-muted small">{{ __('Browse available loyalty programs') }}</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if($discoverCards->isEmpty())
                    <div class="empty-state-modern text-center py-5">
                        <div class="empty-icon-wrapper mb-3">
                            <i class="fas fa-search"></i>
                        </div>
                        <p class="text-muted mb-0 fw-semibold">{{ __('No cards available right now.') }}</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($discoverCards as $card)
                            <div class="col-sm-6 col-lg-4">
                                <div class="card border-0 shadow-sm h-100 card-hover">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="fw-semibold mb-0 text-gray-900">{{ $card->title }}</h6>
                                            @php
                                                $alreadyFollowed = $walletCards->contains('id', $card->id);
                                            @endphp
                                            @if($alreadyFollowed)
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                                                    <i class="fas fa-check me-1"></i>{{ __('Following') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-building me-1 text-primary"></i>
                                            {{ optional($card->company)->localized_name ?? optional($card->company)->name ?? __('No Company') }}
                                        </p>
                                        <div class="d-flex gap-2">
                                            @if(!$alreadyFollowed)
                                                <form method="POST" action="{{ route('customer.cards.follow', ['loyaltyCard' => $card->id, 'locale' => app()->getLocale()]) }}" class="flex-fill">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-animated">
                                                        <i class="fas fa-plus me-1"></i>{{ __('Follow') }}
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('public.card', ['slug' => $card->slug, 'locale' => app()->getLocale()]) }}" class="btn {{ $alreadyFollowed ? 'btn-primary' : 'btn-outline-primary' }} btn-sm {{ $alreadyFollowed ? 'w-100' : 'flex-fill' }} btn-animated">
                                                <i class="fas fa-eye me-1"></i>{{ __('View') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
