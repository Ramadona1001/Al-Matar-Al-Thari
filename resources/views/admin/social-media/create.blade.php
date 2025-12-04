@extends('layouts.dashboard')

@section('title', __('Add Social Media Link'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.social-media.index') }}">{{ __('Social Media') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Add') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle">
                <i class="fab fa-facebook text-primary"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Site Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Add Social Media Link') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.social-media.store') }}">
            @csrf
            
            <div class="row g-4">
                <div class="col-12">
                    <label for="platform" class="form-label fw-semibold">{{ __('Platform') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('platform') is-invalid @enderror" id="platform" name="platform" required>
                        <option value="">{{ __('Select Platform') }}</option>
                        @foreach($availablePlatforms as $platformKey => $platform)
                            @if(!isset($socialLinks[$platformKey]))
                                <option value="{{ $platformKey }}" {{ old('platform') == $platformKey ? 'selected' : '' }}>
                                    {{ $platform['name'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('platform')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(count($socialLinks) >= count($availablePlatforms))
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle me-2"></i>{{ __('All available platforms have been added. You can edit them from the main page.') }}
                        </div>
                    @endif
                </div>
                
                <div class="col-12">
                    <label for="url" class="form-label fw-semibold">{{ __('URL') }} <span class="text-danger">*</span></label>
                    <input type="url" 
                           class="form-control @error('url') is-invalid @enderror" 
                           id="url" 
                           name="url" 
                           value="{{ old('url') }}" 
                           placeholder="https://..." 
                           required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ __('Enter the full URL to your social media profile (e.g., https://facebook.com/yourpage)') }}</small>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.social-media.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Add Social Media Link') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

