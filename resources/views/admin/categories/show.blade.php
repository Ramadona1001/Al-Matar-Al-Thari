@extends('layouts.dashboard')

@section('title', __('Category Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">{{ __('Categories') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $category->localized_name }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
    </a>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>{{ __('Category Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('ID') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $category->id }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Name (English)') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $category->name['en'] ?? '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Name (Arabic)') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $category->name['ar'] ?? '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Slug') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        <code>{{ $category->slug }}</code>
                    </div>
                </div>

                @if($category->localized_description)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>{{ __('Description') }}:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $category->localized_description }}
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Sort Order') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $category->sort_order }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Status') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($category->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Created At') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $category->created_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>{{ __('Updated At') }}:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $category->updated_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-image me-2"></i>{{ __('Category Image') }}
                </h6>
            </div>
            <div class="card-body text-center">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" 
                         alt="{{ $category->localized_name }}" 
                         class="img-fluid rounded">
                @else
                    <div class="text-muted py-5">
                        <i class="fas fa-image fa-3x mb-3"></i>
                        <p>{{ __('No image uploaded') }}</p>
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
            </div>
        </div>
    </div>
</div>
@endsection

