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
            
            <!-- Language Tabs -->
            <ul class="nav nav-tabs mb-4" id="langTabs" role="tablist">
                @foreach($locales as $index => $locale)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                id="lang-{{ $locale }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#lang-{{ $locale }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="lang-{{ $locale }}" 
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <i class="fas fa-language me-1"></i>{{ strtoupper($locale) }} - {{ __($locale) }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content mb-4" id="langTabsContent">
                @foreach($locales as $index => $locale)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                         id="lang-{{ $locale }}" 
                         role="tabpanel" 
                         aria-labelledby="lang-{{ $locale }}-tab">
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="title_{{ $locale }}" class="form-label fw-semibold">{{ __('Title') }}</label>
                                <input type="text" 
                                       name="title[{{ $locale }}]" 
                                       id="title_{{ $locale }}" 
                                       value="{{ old("title.{$locale}") }}" 
                                       class="form-control @error("title.{$locale}") is-invalid @enderror"
                                       placeholder="{{ __('Enter title in :locale', ['locale' => strtoupper($locale)]) }}">
                                @error("title.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="subtitle_{{ $locale }}" class="form-label fw-semibold">{{ __('Subtitle') }}</label>
                                <input type="text" 
                                       name="subtitle[{{ $locale }}]" 
                                       id="subtitle_{{ $locale }}" 
                                       value="{{ old("subtitle.{$locale}") }}" 
                                       class="form-control @error("subtitle.{$locale}") is-invalid @enderror"
                                       placeholder="{{ __('Enter subtitle in :locale', ['locale' => strtoupper($locale)]) }}">
                                @error("subtitle.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="description_{{ $locale }}" class="form-label fw-semibold">{{ __('Description') }}</label>
                                <textarea name="description[{{ $locale }}]" 
                                          id="description_{{ $locale }}" 
                                          rows="4" 
                                          class="form-control @error("description.{$locale}") is-invalid @enderror"
                                          placeholder="{{ __('Enter description in :locale', ['locale' => strtoupper($locale)]) }}">{{ old("description.{$locale}") }}</textarea>
                                @error("description.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="button_text_{{ $locale }}" class="form-label fw-semibold">{{ __('Button Text') }}</label>
                                <input type="text" 
                                       name="button_text[{{ $locale }}]" 
                                       id="button_text_{{ $locale }}" 
                                       value="{{ old("button_text.{$locale}") }}" 
                                       class="form-control @error("button_text.{$locale}") is-invalid @enderror"
                                       placeholder="{{ __('Enter button text in :locale', ['locale' => strtoupper($locale)]) }}">
                                @error("button_text.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Common Fields -->
            <div class="border-top pt-4 mb-4">
                <h6 class="fw-semibold mb-3">{{ __('Common Settings') }}</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="image_path" class="form-label fw-semibold">{{ __('Image') }}</label>
                        <input type="file" 
                               name="image_path" 
                               id="image_path" 
                               class="form-control @error('image_path') is-invalid @enderror" 
                               accept="image/*">
                        @error('image_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="mobile_image_path" class="form-label fw-semibold">{{ __('Mobile Image') }}</label>
                        <input type="file" 
                               name="mobile_image_path" 
                               id="mobile_image_path" 
                               class="form-control @error('mobile_image_path') is-invalid @enderror" 
                               accept="image/*">
                        @error('mobile_image_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="button_link" class="form-label fw-semibold">{{ __('Button Link') }}</label>
                        <input type="url" 
                               name="button_link" 
                               id="button_link" 
                               value="{{ old('button_link') }}" 
                               class="form-control @error('button_link') is-invalid @enderror"
                               placeholder="{{ __('https://example.com') }}">
                        @error('button_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="button_style" class="form-label fw-semibold">{{ __('Button Style') }}</label>
                        <select name="button_style" 
                                id="button_style" 
                                class="form-select @error('button_style') is-invalid @enderror">
                            <option value="primary" {{ old('button_style') == 'primary' ? 'selected' : '' }}>{{ __('Primary') }}</option>
                            <option value="secondary" {{ old('button_style') == 'secondary' ? 'selected' : '' }}>{{ __('Secondary') }}</option>
                            <option value="outline" {{ old('button_style') == 'outline' ? 'selected' : '' }}>{{ __('Outline') }}</option>
                        </select>
                        @error('button_style')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               value="{{ old('order', 0) }}" 
                               class="form-control @error('order') is-invalid @enderror" 
                               min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="checkbox" 
                                   class="form-check-input @error('is_active') is-invalid @enderror" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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
