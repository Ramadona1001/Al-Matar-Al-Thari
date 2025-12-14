@extends('layouts.dashboard')

@section('title', __('Product Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.products.index') }}">{{ __('Products') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $product->localized_name }}</li>
@endsection

@section('actions')
    <a href="{{ route('merchant.products.edit', $product) }}" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
    </a>
    <a href="{{ route('merchant.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>{{ __('Product Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('ID') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $product->id }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Name (English)') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $product->name['en'] ?? '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Name (Arabic)') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $product->name['ar'] ?? '-' }}
                    </div>
                </div>

                @if($product->localized_description)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>{{ __('Description') }}:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $product->localized_description }}
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('SKU') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($product->sku)
                            <code>{{ $product->sku }}</code>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Price') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        <strong>{{ number_format($product->price, 2) }} {{ __('SAR') }}</strong>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <br><small class="text-muted text-decoration-line-through">{{ number_format($product->compare_price, 2) }} {{ __('SAR') }}</small>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Stock Status') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($product->track_stock)
                            <span class="badge {{ $product->isInStock() ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->stock_quantity }} {{ __('units') }}
                            </span>
                        @else
                            <span class="badge {{ $product->in_stock ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->in_stock ? __('Available') : __('Unavailable') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Category') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($product->category)
                            {{ $product->category->localized_name }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Status') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($product->status == 'active')
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @elseif($product->status == 'draft')
                            <span class="badge bg-secondary">{{ __('Draft') }}</span>
                        @elseif($product->status == 'inactive')
                            <span class="badge bg-warning">{{ __('Inactive') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                        @endif
                        @if($product->is_featured)
                            <span class="badge bg-warning ms-2"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Created At') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $product->created_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Updated At') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $product->updated_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-image me-2"></i>{{ __('Product Images') }}
                </h6>
            </div>
            <div class="card-body text-center">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->localized_name }}" 
                         class="img-fluid rounded mb-3">
                @else
                    <div class="text-muted py-5">
                        <i class="fas fa-image fa-3x mb-3"></i>
                        <p>{{ __('No main image uploaded') }}</p>
                    </div>
                @endif

                @if($product->images && count($product->images) > 0)
                    <hr>
                    <h6 class="mb-3">{{ __('Additional Images') }}</h6>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @foreach($product->images as $img)
                            <img src="{{ asset('storage/' . $img) }}" 
                                 alt="{{ $product->localized_name }}" 
                                 class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>{{ __('Statistics') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Total Offers') }}:</strong>
                    <span class="badge bg-info float-end">{{ $stats['total_offers'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Active Offers') }}:</strong>
                    <span class="badge bg-success float-end">{{ $stats['active_offers'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Total Coupons') }}:</strong>
                    <span class="badge bg-info float-end">{{ $stats['total_coupons'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Used Coupons') }}:</strong>
                    <span class="badge bg-success float-end">{{ $stats['used_coupons'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

