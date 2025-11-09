@extends('layouts.dashboard')

@section('title', __('Browse Offers'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Offers') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters') }}</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('customer.offers.index') }}">
                    <div class="mb-3">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search offers...') }}">
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">{{ __('Category') }}</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->localized_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">{{ __('Offer Type') }}</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="featured" name="featured" {{ request()->boolean('featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="featured">
                            {{ __('Featured Offers') }}
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>{{ __('Apply Filters') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="row g-3">
            @forelse($offers as $offer)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm">
                        @if($offer->image)
                            <img src="{{ asset('storage/' . $offer->image) }}" class="card-img-top" alt="{{ $offer->localized_title }}" style="height: 160px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offer->type)) }}</span>
                                @if($offer->is_featured)
                                    <span class="badge bg-warning"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                                @endif
                            </div>
                            <h5 class="card-title">{{ $offer->localized_title }}</h5>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($offer->localized_description, 90) }}</p>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-primary">
                                        @if($offer->type == 'percentage')
                                            {{ $offer->discount_percentage }}%
                                        @elseif($offer->type == 'fixed')
                                            {{ number_format($offer->discount_amount, 2) }}
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $offer->type)) }}
                                        @endif
                                    </strong>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $offer->end_date->diffForHumans() }}</small>
                                </div>
                                <a href="{{ route('customer.offers.show', $offer) }}" class="btn btn-outline-primary w-100">
                                    {{ __('View Details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        {{ __('No offers available at the moment. Please check back later!') }}
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $offers->links() }}
        </div>
    </div>
</div>
@endsection
