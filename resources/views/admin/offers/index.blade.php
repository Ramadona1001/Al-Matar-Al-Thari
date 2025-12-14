@extends('layouts.dashboard')

@section('title', __('Offers Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Offers') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
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
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
        <form method="GET" action="{{ route('admin.offers.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="company_id" class="form-label">{{ __('Company') }}</label>
                <select name="company_id" id="company_id" class="form-select">
                    <option value="">{{ __('All Companies') }}</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->localized_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>{{ __('Paused') }}</option>
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
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('Search offers...') }}">
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>{{ __('Reset') }}
                </a>
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
        @if($offers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" data-dt-init="false">
                    <thead>
                        <tr>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Dates') }}</th>
                            <th width="150">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offers as $offer)
                            <tr>
                                <td>
                                    @if($offer->image)
                                        <img src="{{ asset('storage/' . $offer->image) }}" 
                                             alt="{{ $offer->localized_title }}" 
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
                                    <a href="{{ route('admin.companies.show', $offer->company) }}">
                                        {{ $offer->company->localized_name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($offer->type) }}</span>
                                </td>
                                <td>
                                    @if($offer->type == 'percentage')
                                        {{ $offer->discount_percentage }}%
                                    @elseif($offer->type == 'fixed')
                                        {{ number_format($offer->discount_amount, 2) }} {{ __('SAR') }}
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $offer->type)) }}
                                    @endif
                                </td>
                                <td>
                                    @if($offer->status == 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif($offer->status == 'draft')
                                        <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                    @elseif($offer->status == 'inactive')
                                        <span class="badge bg-warning">{{ __('Inactive') }}</span>
                                    @elseif($offer->status == 'expired')
                                        <span class="badge bg-danger">{{ __('Expired') }}</span>
                                    @else
                                        <span class="badge bg-info">{{ __('Paused') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <strong>{{ __('Start') }}:</strong> {{ $offer->start_date->format('Y-m-d') }}<br>
                                        <strong>{{ __('End') }}:</strong> {{ $offer->end_date->format('Y-m-d') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.offers.show', $offer) }}" 
                                           class="btn btn-sm btn-info" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.offers.edit', $offer) }}" 
                                           class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.offers.toggle-status', $offer) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $offer->status === 'active' ? 'warning' : 'success' }}" 
                                                    title="{{ $offer->status === 'active' ? __('Deactivate') : __('Activate') }}">
                                                <i class="fas fa-{{ $offer->status === 'active' ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.offers.destroy', $offer) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this offer?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
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
                {{ $offers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">{{ __('No offers found.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

