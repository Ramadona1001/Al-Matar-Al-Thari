@extends('layouts.dashboard')

@section('title', __('Create Service'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">{{ __('Services') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-concierge-bell text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Create Service') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="languageTabs" role="tablist">
                @foreach($locales as $index => $locale)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                id="{{ $locale }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#{{ $locale }}" 
                                type="button" 
                                role="tab"
                                aria-controls="{{ $locale }}"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <span class="me-1">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                            {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mb-4" id="languageTabsContent">
                @foreach($locales as $index => $locale)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                         id="{{ $locale }}" 
                         role="tabpanel"
                         aria-labelledby="{{ $locale }}-tab">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="title_{{ $locale }}" class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="title[{{ $locale }}]" 
                                       id="title_{{ $locale }}" 
                                       value="{{ old("title.{$locale}") }}" 
                                       class="form-control @error("title.{$locale}") is-invalid @enderror" 
                                       required>
                                @error("title.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="short_description_{{ $locale }}" class="form-label fw-semibold">{{ __('Short Description') }}</label>
                                <textarea name="short_description[{{ $locale }}]" 
                                          id="short_description_{{ $locale }}" 
                                          rows="3" 
                                          class="form-control @error("short_description.{$locale}") is-invalid @enderror">{{ old("short_description.{$locale}") }}</textarea>
                                @error("short_description.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="description_{{ $locale }}" class="form-label fw-semibold">{{ __('Description') }}</label>
                                <textarea name="description[{{ $locale }}]" 
                                          id="description_{{ $locale }}" 
                                          rows="6" 
                                          class="form-control @error("description.{$locale}") is-invalid @enderror">{{ old("description.{$locale}") }}</textarea>
                                @error("description.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('SEO Settings') }}</h6>
                            </div>
                            
                            <div class="col-12">
                                <label for="meta_title_{{ $locale }}" class="form-label fw-semibold">{{ __('Meta Title') }}</label>
                                <input type="text" 
                                       name="meta_title[{{ $locale }}]" 
                                       id="meta_title_{{ $locale }}" 
                                       value="{{ old("meta_title.{$locale}") }}" 
                                       class="form-control @error("meta_title.{$locale}") is-invalid @enderror">
                                @error("meta_title.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="meta_description_{{ $locale }}" class="form-label fw-semibold">{{ __('Meta Description') }}</label>
                                <textarea name="meta_description[{{ $locale }}]" 
                                          id="meta_description_{{ $locale }}" 
                                          rows="2" 
                                          class="form-control @error("meta_description.{$locale}") is-invalid @enderror">{{ old("meta_description.{$locale}") }}</textarea>
                                @error("meta_description.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="meta_keywords_{{ $locale }}" class="form-label fw-semibold">{{ __('Meta Keywords') }}</label>
                                <input type="text" 
                                       name="meta_keywords[{{ $locale }}]" 
                                       id="meta_keywords_{{ $locale }}" 
                                       value="{{ old("meta_keywords.{$locale}") }}" 
                                       class="form-control @error("meta_keywords.{$locale}") is-invalid @enderror"
                                       placeholder="{{ __('keyword1, keyword2, keyword3') }}">
                                @error("meta_keywords.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- General Settings (Outside Tabs) -->
            <div class="row g-4 mt-4 pt-4 border-top">
                <div class="col-12">
                    <h6 class="fw-bold text-gray-900 mb-3">{{ __('General Settings') }}</h6>
                </div>
                
                <div class="col-md-6">
                    <label for="slug" class="form-label fw-semibold">{{ __('Slug') }}</label>
                    <input type="text" 
                           name="slug" 
                           id="slug" 
                           value="{{ old('slug') }}" 
                           class="form-control @error('slug') is-invalid @enderror" 
                           placeholder="{{ __('Auto-generated from title if empty') }}">
                    <small class="text-muted">{{ __('Leave empty to auto-generate from title') }}</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="icon" class="form-label fw-semibold">{{ __('Icon') }}</label>
                    <input type="text" 
                           name="icon" 
                           id="icon" 
                           value="{{ old('icon') }}" 
                           class="form-control @error('icon') is-invalid @enderror" 
                           placeholder="fas fa-star">
                    <small class="text-muted">{{ __('Font Awesome icon class (e.g., fas fa-star)') }}</small>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="image_path" class="form-label fw-semibold">{{ __('Image') }}</label>
                    <input type="file" 
                           name="image_path" 
                           id="image_path" 
                           class="form-control @error('image_path') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    <small class="text-muted">{{ __('Max size: 5MB. Accepted formats: JPG, PNG, GIF, WEBP') }}</small>
                    @error('image_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="og_image" class="form-label fw-semibold">{{ __('OG Image (Social Media)') }}</label>
                    <input type="file" 
                           name="og_image" 
                           id="og_image" 
                           class="form-control @error('og_image') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    <small class="text-muted">{{ __('Image for social media sharing (1200x630px recommended)') }}</small>
                    @error('og_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', 0) }}" 
                           class="form-control @error('order') is-invalid @enderror" 
                           min="0">
                    <small class="text-muted">{{ __('Lower numbers appear first') }}</small>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input @error('is_active') is-invalid @enderror" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input @error('is_featured') is-invalid @enderror" 
                               id="is_featured" 
                               name="is_featured" 
                               value="1" 
                               {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_featured">{{ __('Featured') }}</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-save me-2"></i>{{ __('Create Service') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
