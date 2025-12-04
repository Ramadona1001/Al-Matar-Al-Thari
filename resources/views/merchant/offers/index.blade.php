@extends('layouts.dashboard')

@section('title', __('Offers Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Offers') }}</li>
@endsection

@section('actions')
    <a href="{{ route('merchant.offers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('Create Offer') }}
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Offers') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
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
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('Expired') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expired'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Featured') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['featured'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
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
        <form method="GET" action="{{ route('merchant.offers.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
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
            <div class="col-md-4">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('Search offers...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Offers Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Offers List') }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0" data-dt-init="false">
                <thead>
                    <tr>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th width="150">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td>
                                @if($offer->image)
                                    <img src="{{ asset('storage/' . $offer->image) }}" alt="{{ $offer->localized_title }}" 
                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $offer->localized_title }}</strong>
                                @if($offer->is_featured)
                                    <span class="badge bg-warning ms-2"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($offer->type) }}</span>
                            </td>
                            <td>
                                @if($offer->type == 'percentage')
                                    {{ $offer->discount_percentage }}%
                                @elseif($offer->type == 'fixed')
                                    {{ number_format($offer->discount_amount, 2) }}
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $offer->type)) }}
                                @endif
                            </td>
                            <td>{{ $offer->category->localized_name ?? '-' }}</td>
                            <td>
                                @if($offer->status == 'active')
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @elseif($offer->status == 'inactive')
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $offer->start_date->format('Y-m-d') }}</td>
                            <td>{{ $offer->end_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('merchant.offers.show', $offer) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('merchant.offers.edit', $offer) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('merchant.offers.toggle-featured', $offer) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $offer->is_featured ? 'warning' : 'secondary' }}" 
                                                title="{{ $offer->is_featured ? __('Remove from Featured') : __('Mark as Featured') }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('merchant.offers.destroy', $offer) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                                onclick="return confirm('{{ __('Are you sure?') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">{{ __('No offers found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $offers->links() }}
        </div>
    </div>
</div>
@endsection

