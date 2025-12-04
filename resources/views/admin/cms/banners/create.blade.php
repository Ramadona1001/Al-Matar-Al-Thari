@extends('layouts.dashboard')

@section('title', __('Create Banner'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">{{ __('Banners') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
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
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Create Banner') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="title" class="form-label fw-semibold">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label for="locale" class="form-label fw-semibold">{{ __('Language') }} <span class="text-danger">*</span></label>
                    <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror" required>
                        <option value="">{{ __('Select Language') }}</option>
                        @foreach($locales as $loc)
                            <option value="{{ $loc }}" {{ old('locale') == $loc ? 'selected' : '' }}>{{ strtoupper($loc) }} - {{ __($loc) }}</option>
                        @endforeach
                    </select>
                    @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-12">
                    <label for="subtitle" class="form-label fw-semibold">{{ __('Subtitle') }}</label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" class="form-control">
                </div>
                
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                </div>
                
                <div class="col-md-6">
                    <label for="image_path" class="form-label fw-semibold">{{ __('Image') }}</label>
                    <input type="file" name="image_path" id="image_path" class="form-control @error('image_path') is-invalid @enderror" accept="image/*">
                    @error('image_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label for="mobile_image_path" class="form-label fw-semibold">{{ __('Mobile Image') }}</label>
                    <input type="file" name="mobile_image_path" id="mobile_image_path" class="form-control" accept="image/*">
                </div>
                
                <div class="col-md-6">
                    <label for="button_text" class="form-label fw-semibold">{{ __('Button Text') }}</label>
                    <input type="text" name="button_text" id="button_text" value="{{ old('button_text') }}" class="form-control">
                </div>
                
                <div class="col-md-6">
                    <label for="button_link" class="form-label fw-semibold">{{ __('Button Link') }}</label>
                    <input type="url" name="button_link" id="button_link" value="{{ old('button_link') }}" class="form-control">
                </div>
                
                <div class="col-md-4">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" class="form-control" min="0">
                </div>
                
                <div class="col-md-4">
                    <label for="button_style" class="form-label fw-semibold">{{ __('Button Style') }}</label>
                    <select name="button_style" id="button_style" class="form-select">
                        <option value="primary" {{ old('button_style') == 'primary' ? 'selected' : '' }}>{{ __('Primary') }}</option>
                        <option value="secondary" {{ old('button_style') == 'secondary' ? 'selected' : '' }}>{{ __('Secondary') }}</option>
                        <option value="outline" {{ old('button_style') == 'outline' ? 'selected' : '' }}>{{ __('Outline') }}</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Create Banner') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

