@extends('layouts.dashboard')

@section('title', __('Edit Banner'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">{{ __('Banners') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle">
                <i class="fas fa-image text-primary"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Banner') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        @if($banner->image_path)
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('Current Image') }}</label>
                <div>
                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="img-thumbnail" style="max-height: 200px;">
                </div>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="title" class="form-label fw-semibold">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}" class="form-control @error('title') is-invalid @enderror">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label for="locale" class="form-label fw-semibold">{{ __('Language') }} <span class="text-danger">*</span></label>
                    <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror" required>
                        @foreach($locales as $loc)
                            <option value="{{ $loc }}" {{ old('locale', $banner->locale) == $loc ? 'selected' : '' }}>{{ strtoupper($loc) }} - {{ __($loc) }}</option>
                        @endforeach
                    </select>
                    @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-12">
                    <label for="subtitle" class="form-label fw-semibold">{{ __('Subtitle') }}</label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="form-control">
                </div>
                
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $banner->description) }}</textarea>
                </div>
                
                <div class="col-md-6">
                    <label for="image_path" class="form-label fw-semibold">{{ __('New Image') }}</label>
                    <input type="file" name="image_path" id="image_path" class="form-control @error('image_path') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
                    @error('image_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label for="mobile_image_path" class="form-label fw-semibold">{{ __('New Mobile Image') }}</label>
                    <input type="file" name="mobile_image_path" id="mobile_image_path" class="form-control" accept="image/*">
                    <small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
                </div>
                
                <div class="col-md-6">
                    <label for="button_text" class="form-label fw-semibold">{{ __('Button Text') }}</label>
                    <input type="text" name="button_text" id="button_text" value="{{ old('button_text', $banner->button_text) }}" class="form-control">
                </div>
                
                <div class="col-md-6">
                    <label for="button_link" class="form-label fw-semibold">{{ __('Button Link') }}</label>
                    <input type="url" name="button_link" id="button_link" value="{{ old('button_link', $banner->button_link) }}" class="form-control">
                </div>
                
                <div class="col-md-4">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $banner->order) }}" class="form-control" min="0">
                </div>
                
                <div class="col-md-4">
                    <label for="button_style" class="form-label fw-semibold">{{ __('Button Style') }}</label>
                    <select name="button_style" id="button_style" class="form-select">
                        <option value="primary" {{ old('button_style', $banner->button_style) == 'primary' ? 'selected' : '' }}>{{ __('Primary') }}</option>
                        <option value="secondary" {{ old('button_style', $banner->button_style) == 'secondary' ? 'selected' : '' }}>{{ __('Secondary') }}</option>
                        <option value="outline" {{ old('button_style', $banner->button_style) == 'outline' ? 'selected' : '' }}>{{ __('Outline') }}</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Banner') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

