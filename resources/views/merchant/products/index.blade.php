@extends('layouts.dashboard')

@section('title', __('Products Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Products') }}</li>
@endsection

@section('actions')
    <a href="{{ route('merchant.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('Create Product') }}
    </a>
@endsection

@section('content')
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

<div class="row mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Products') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Active') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('In Stock') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_stock'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Out of Stock') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['out_of_stock'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters') }}</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('merchant.products.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="category_id" class="form-label">{{ __('Category') }}</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->localized_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="stock_status" class="form-label">{{ __('Stock Status') }}</label>
                <select name="stock_status" id="stock_status" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('Search products...') }}">
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('merchant.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>{{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Products List') }}</h6>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" data-dt-init="false">
                    <thead>
                        <tr>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('SKU') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th width="150">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->localized_name }}" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product->localized_name }}</strong>
                                    @if($product->is_featured)
                                        <span class="badge bg-warning ms-2"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->sku)
                                        <code>{{ $product->sku }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($product->price, 2) }} {{ __('SAR') }}</strong>
                                    @if($product->compare_price && $product->compare_price > $product->price)
                                        <br><small class="text-muted text-decoration-line-through">{{ number_format($product->compare_price, 2) }} {{ __('SAR') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($product->track_stock)
                                        <span class="badge {{ $product->isInStock() ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->stock_quantity }} {{ __('units') }}
                                        </span>
                                    @else
                                        <span class="badge {{ $product->in_stock ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->in_stock ? __('Available') : __('Unavailable') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->category)
                                        {{ $product->category->localized_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->status == 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif($product->status == 'draft')
                                        <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                    @elseif($product->status == 'inactive')
                                        <span class="badge bg-warning">{{ __('Inactive') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('merchant.products.show', $product) }}" 
                                           class="btn btn-sm btn-info" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('merchant.products.edit', $product) }}" 
                                           class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('merchant.products.toggle-featured', $product) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" 
                                                    title="{{ $product->is_featured ? __('Unfeature') : __('Feature') }}">
                                                <i class="fas fa-{{ $product->is_featured ? 'star' : 'star' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('merchant.products.destroy', $product) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="{{ __('Delete') }}"
                                                    {{ $product->offers()->count() > 0 || $product->coupons()->count() > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">{{ __('No products found.') }}</p>
                <a href="{{ route('merchant.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('Create First Product') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

