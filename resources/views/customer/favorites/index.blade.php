@extends('layouts.dashboard')

@section('title', __('favorite_companies'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('favorite_companies') }}</li>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('my_favorite_companies') }}</h6>
    </div>
    <div class="card-body">
        <!-- Search Filter -->
        <form method="GET" action="{{ route('customer.favorites.index') }}" class="row g-3 mb-4">
            <div class="col-md-10">
                <label for="search" class="form-label">{{ __('search_companies') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('search_by_company_name') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('search') }}
                </button>
            </div>
        </form>

        <!-- Companies Grid -->
        <div class="row g-4">
            @forelse($paginated as $company)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($company->logo)
                                    <img src="{{ asset('storage/' . $company->logo) }}" 
                                         alt="{{ $company->localized_name }}" 
                                         class="rounded-circle" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-building text-primary fa-2x"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold">{{ $company->localized_name }}</h5>
                                    @if($company->industry)
                                        <small class="text-muted">{{ $company->industry }}</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-primary">{{ $company->transaction_count ?? 0 }}</div>
                                        <small class="text-muted">{{ __('transactions') }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-success">${{ number_format($company->total_spent ?? 0, 2) }}</div>
                                        <small class="text-muted">{{ __('total_spent') }}</small>
                                    </div>
                                </div>
                            </div>

                            @if($company->description)
                                <p class="text-muted small mb-3">
                                    {{ Str::limit(is_array($company->description) ? ($company->description[app()->getLocale()] ?? $company->description['en'] ?? '') : ($company->description ?? ''), 100) }}
                                </p>
                            @endif

                            <div class="d-flex gap-2">
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="fas fa-globe me-1"></i>{{ __('website') }}
                                    </a>
                                @endif
                                @if($company->phone)
                                    <a href="tel:{{ $company->phone }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-heart fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">{{ __('no_favorite_companies_found') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $paginated->links() }}
        </div>
    </div>
</div>
@endsection

