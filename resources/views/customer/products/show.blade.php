@extends('layouts.dashboard')

@section('title', $product->localized_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.products.index') }}">{{ __('Products') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $product->localized_name }}</li>
@endsection

@section('actions')
    <a href="{{ route('customer.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Products') }}
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->localized_name }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded mb-3" style="height: 300px;">
                                <i class="fas fa-image fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center mb-2">
                            @if($product->is_featured)
                                <span class="badge bg-warning me-2"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                            @endif
                            @if($product->category)
                                <span class="badge bg-info me-2">{{ $product->category->localized_name }}</span>
                            @endif
                        </div>
                        <h2>{{ $product->localized_name }}</h2>
                        <p class="text-muted">{{ $product->localized_description }}</p>
                        
                        <div class="mb-3">
                            <strong class="text-primary" style="font-size: 2rem;">
                                {{ number_format($product->price, 2) }} {{ __('SAR') }}
                            </strong>
                            @if($product->compare_price)
                                <small class="text-muted text-decoration-line-through ms-2" style="font-size: 1.2rem;">
                                    {{ number_format($product->compare_price, 2) }}
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            @if($product->isInStock())
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i>{{ __('In Stock') }}
                                </span>
                                @if($product->track_stock)
                                    <small class="text-muted ms-2">({{ $product->stock_quantity }} {{ __('available') }})</small>
                                @endif
                            @else
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times-circle me-1"></i>{{ __('Out of Stock') }}
                                </span>
                            @endif
                        </div>

                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="180">{{ __('SKU') }}:</th>
                                <td>{{ $product->sku ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Company') }}:</th>
                                <td>{{ $product->company->localized_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Category') }}:</th>
                                <td>{{ $product->category->localized_name ?? '-' }}</td>
                            </tr>
                        </table>

                        @if($product->isInStock() && auth()->user()->digitalCard && auth()->user()->digitalCard->isActive())
                            <form method="POST" action="{{ route('customer.products.purchase', $product) }}" class="mt-3">
                                @csrf
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               value="1" min="1" max="{{ $product->stock_quantity ?? 999 }}" required>
                                    </div>
                                    <div class="col-md-8">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>{{ __('Purchase with Digital Card') }}
                                        </button>
                                    </div>
                                </div>
                                @if($product->track_stock)
                                    <small class="text-muted">{{ __('Maximum quantity available: :qty', ['qty' => $product->stock_quantity]) }}</small>
                                @endif
                            </form>
                        @elseif(!$product->isInStock())
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ __('This product is currently out of stock.') }}
                            </div>
                        @else
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>{{ __('Please activate your digital card to make a purchase.') }}
                                <a href="{{ route('customer.digital-card.index') }}" class="alert-link">{{ __('Go to Digital Card') }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($product->offers->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Related Offers') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($product->offers as $offer)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6>{{ $offer->localized_title }}</h6>
                                    <p class="small text-muted">{{ Str::limit($offer->localized_description, 80) }}</p>
                                    <a href="{{ route('customer.offers.show', $offer) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('View Offer') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('About the Company') }}</h6>
            </div>
            <div class="card-body">
                <h5>{{ $product->company->localized_name ?? __('Unknown') }}</h5>
                <p class="text-muted">{{ $product->company->localized_description ?? __('No description available.') }}</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-phone me-2 text-muted"></i>{{ $product->company->phone ?? '-' }}</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-muted"></i>{{ $product->company->email ?? '-' }}</li>
                    @if($product->company && $product->company->website)
                        <li class="mb-2"><i class="fas fa-globe me-2 text-muted"></i><a href="{{ $product->company->website }}" target="_blank">{{ $product->company->website }}</a></li>
                    @endif
                    <li><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $product->company->address ?? '-' }}</li>
                </ul>
            </div>
        </div>

        @if($relatedProducts->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Similar Products') }}</h6>
            </div>
            <div class="card-body">
                @foreach($relatedProducts as $related)
                    <div class="d-flex align-items-center mb-3">
                        @if($related->image)
                            <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->localized_name }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1"><a href="{{ route('customer.products.show', $related) }}">{{ $related->localized_name }}</a></h6>
                            <small class="text-primary">{{ number_format($related->price, 2) }} {{ __('SAR') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

