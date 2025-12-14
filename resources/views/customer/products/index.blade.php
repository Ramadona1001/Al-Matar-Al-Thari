@extends('layouts.dashboard')

@section('title', __('Products'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Products') }}</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters') }}</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('customer.products.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="{{ __('Search products...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">{{ __('Category') }}</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->localized_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="company" class="form-label">{{ __('Company') }}</label>
                        <select name="company" id="company" class="form-select">
                            <option value="">{{ __('All Companies') }}</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
                                    {{ $company->localized_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                   {{ request('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">
                                {{ __('Featured Only') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>{{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->localized_name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                @if($product->is_featured)
                                    <span class="badge bg-warning mb-2"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                                @endif
                                @if($product->category)
                                    <span class="badge bg-info mb-2">{{ $product->category->localized_name }}</span>
                                @endif
                            </div>
                            <h5 class="card-title">{{ $product->localized_name }}</h5>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($product->localized_description, 100) }}</p>
                            <div class="mb-3">
                                <strong class="text-primary" style="font-size: 1.25rem;">{{ number_format($product->price, 2) }} {{ __('SAR') }}</strong>
                                @if($product->compare_price)
                                    <small class="text-muted text-decoration-line-through ms-2">{{ number_format($product->compare_price, 2) }}</small>
                                @endif
                            </div>
                            <div class="mb-2">
                                @if($product->isInStock())
                                    <span class="badge bg-success">{{ __('In Stock') }}</span>
                                    @if($product->track_stock)
                                        <small class="text-muted">({{ $product->stock_quantity }} {{ __('available') }})</small>
                                    @endif
                                @else
                                    <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('customer.products.show', $product) }}" class="btn btn-primary mt-auto">
                                <i class="fas fa-eye me-2"></i>{{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>{{ __('No products found.') }}
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

