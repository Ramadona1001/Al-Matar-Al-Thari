@extends('layouts.dashboard')

@section('title', __('Social Media Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Social Media') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.social-media.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Add Social Media') }}
    </a>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<!-- Social Media Links Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-info-subtle">
                <i class="fab fa-facebook text-info"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Site Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Social Media Links') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="row g-4">
            @foreach($availablePlatforms as $platformKey => $platform)
                @php
                    $existingUrl = $socialLinks[$platformKey] ?? null;
                @endphp
                <div class="col-lg-6 col-md-6">
                    <div class="card border h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="social-icon-wrapper" style="background-color: {{ $platform['color'] }}20; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="{{ $platform['icon'] }}" style="color: {{ $platform['color'] }}; font-size: 24px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-gray-900">{{ $platform['name'] }}</h6>
                                    @if($existingUrl)
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                                        </small>
                                    @else
                                        <small class="text-muted">
                                            <i class="fas fa-times-circle me-1"></i>{{ __('Not Set') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            
                            <form action="{{ route('admin.social-media.update', $platformKey) }}" method="POST" class="social-media-form">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="url_{{ $platformKey }}" class="form-label fw-semibold">{{ __('URL') }}</label>
                                    <div class="input-group">
                                        <input type="url" 
                                               class="form-control @error("url") is-invalid @enderror" 
                                               id="url_{{ $platformKey }}" 
                                               name="url" 
                                               value="{{ old("url", $existingUrl) }}" 
                                               placeholder="https://...">
                                        @if($existingUrl)
                                            <a href="{{ $existingUrl }}" target="_blank" class="btn btn-outline-primary" title="{{ __('Open Link') }}">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                    @error('url')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                        <i class="fas fa-save me-1"></i>{{ __('Save') }}
                                    </button>
                                    @if($existingUrl)
                                        <form action="{{ route('admin.social-media.destroy', $platformKey) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this social media link?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
