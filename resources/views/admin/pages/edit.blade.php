@extends('layouts.dashboard')

@section('title', __('Edit Page'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">{{ __('Pages') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle">
                <i class="fas fa-file-alt text-primary"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Page') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        @if($page->featured_image)
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('Current Featured Image') }}</label>
                <div>
                    <img src="{{ asset('storage/' . $page->featured_image) }}" 
                         alt="{{ $page->title }}" 
                         class="img-thumbnail" 
                         style="max-height: 200px;">
                </div>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <!-- Basic Information -->
                <div class="col-12">
                    <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('Basic Information') }}</h6>
                </div>
                
                <div class="col-md-6">
                    <label for="slug" class="form-label fw-semibold">{{ __('Slug') }} <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" 
                           class="form-control @error('slug') is-invalid @enderror" 
                           placeholder="about-us" required>
                    <small class="text-muted">{{ __('URL-friendly identifier (e.g., about-us)') }}</small>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label for="locale" class="form-label fw-semibold">{{ __('Language') }} <span class="text-danger">*</span></label>
                    <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror" required>
                        @foreach($locales as $loc)
                            <option value="{{ $loc }}" {{ old('locale', $page->locale) == $loc ? 'selected' : '' }}>
                                {{ strtoupper($loc) }} - {{ config("localization.locale_names.{$loc}", $loc) }}
                            </option>
                        @endforeach
                    </select>
                    @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <!-- Multi-Language Content Tabs -->
                <div class="col-12">
                    <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2 mt-4">{{ __('Content (Multi-Language)') }}</h6>
                    
                    <ul class="nav nav-tabs mb-3" id="contentTabs" role="tablist">
                        @foreach($locales as $index => $locale)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                        id="tab-{{ $locale }}" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#content-{{ $locale }}" 
                                        type="button" 
                                        role="tab">
                                    <span class="me-2">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                                    {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="tab-content" id="contentTabsContent">
                        @foreach($locales as $index => $locale)
                            @php
                                $titleValue = old("title.{$locale}", is_array($page->title) ? ($page->title[$locale] ?? '') : '');
                                $contentValue = old("content.{$locale}", is_array($page->content) ? ($page->content[$locale] ?? '') : '');
                                $excerptValue = old("excerpt.{$locale}", is_array($page->excerpt) ? ($page->excerpt[$locale] ?? '') : '');
                                $metaTitleValue = old("meta_title.{$locale}", is_array($page->meta_title) ? ($page->meta_title[$locale] ?? '') : '');
                                $metaDescValue = old("meta_description.{$locale}", is_array($page->meta_description) ? ($page->meta_description[$locale] ?? '') : '');
                                $metaKeywordsValue = old("meta_keywords.{$locale}", is_array($page->meta_keywords) ? ($page->meta_keywords[$locale] ?? '') : '');
                            @endphp
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                 id="content-{{ $locale }}" 
                                 role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="title_{{ $locale }}" class="form-label fw-semibold">
                                            {{ __('Title') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               name="title[{{ $locale }}]" 
                                               id="title_{{ $locale }}" 
                                               value="{{ $titleValue }}" 
                                               class="form-control @error("title.{$locale}") is-invalid @enderror" 
                                               required>
                                        @error("title.{$locale}")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="excerpt_{{ $locale }}" class="form-label fw-semibold">
                                            {{ __('Excerpt') }}
                                        </label>
                                        <textarea name="excerpt[{{ $locale }}]" 
                                                  id="excerpt_{{ $locale }}" 
                                                  rows="2" 
                                                  class="form-control">{{ $excerptValue }}</textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="content_{{ $locale }}" class="form-label fw-semibold">
                                            {{ __('Content') }}
                                        </label>
                                        <textarea name="content[{{ $locale }}]" 
                                                  id="content_{{ $locale }}" 
                                                  rows="8" 
                                                  class="form-control">{{ $contentValue }}</textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <h6 class="fw-semibold text-gray-700 mb-3 mt-4">{{ __('SEO Settings') }}</h6>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="meta_title_{{ $locale }}" class="form-label fw-semibold">
                                            {{ __('Meta Title') }}
                                        </label>
                                        <input type="text" 
                                               name="meta_title[{{ $locale }}]" 
                                               id="meta_title_{{ $locale }}" 
                                               value="{{ $metaTitleValue }}" 
                                               class="form-control">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="meta_description_{{ $locale }}" class="form-label fw-semibold">
                                            {{ __('Meta Description') }}
                                        </label>
                                        <textarea name="meta_description[{{ $locale }}]" 
                                                  id="meta_description_{{ $locale }}" 
                                                  rows="2" 
                                                  class="form-control">{{ $metaDescValue }}</textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="meta_keywords_{{ $locale }}" class="form-label fw-semibold">
                                            {{ __('Meta Keywords') }}
                                        </label>
                                        <input type="text" 
                                               name="meta_keywords[{{ $locale }}]" 
                                               id="meta_keywords_{{ $locale }}" 
                                               value="{{ $metaKeywordsValue }}" 
                                               class="form-control"
                                               placeholder="{{ __('keyword1, keyword2, keyword3') }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Additional Settings -->
                <div class="col-12">
                    <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2 mt-4">{{ __('Additional Settings') }}</h6>
                </div>
                
                <div class="col-md-6">
                    <label for="featured_image" class="form-label fw-semibold">{{ __('New Featured Image') }}</label>
                    <input type="file" name="featured_image" id="featured_image" 
                           class="form-control @error('featured_image') is-invalid @enderror" 
                           accept="image/*">
                    <small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
                    @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label for="template" class="form-label fw-semibold">{{ __('Template') }}</label>
                    <select name="template" id="template" class="form-select">
                        <option value="default" {{ old('template', $page->template ?? 'default') == 'default' ? 'selected' : '' }}>{{ __('Default') }}</option>
                        <option value="full-width" {{ old('template', $page->template) == 'full-width' ? 'selected' : '' }}>{{ __('Full Width') }}</option>
                        <option value="sidebar" {{ old('template', $page->template) == 'sidebar' ? 'selected' : '' }}>{{ __('With Sidebar') }}</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $page->order ?? 0) }}" 
                           class="form-control" min="0">
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_published" 
                               name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_published">{{ __('Published') }}</label>
                    </div>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="show_in_menu" 
                               name="show_in_menu" value="1" {{ old('show_in_menu', $page->show_in_menu) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="show_in_menu">{{ __('Show in Menu') }}</label>
                    </div>
                </div>
                
                <!-- Menu Label (Multi-Language) -->
                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('Menu Label') }}</label>
                    <div class="row g-3">
                        @foreach($locales as $locale)
                            @php
                                $menuLabelValue = old("menu_label.{$locale}", is_array($page->menu_label) ? ($page->menu_label[$locale] ?? '') : '');
                            @endphp
                            <div class="col-md-6">
                                <label for="menu_label_{{ $locale }}" class="form-label small">
                                    {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                </label>
                                <input type="text" 
                                       name="menu_label[{{ $locale }}]" 
                                       id="menu_label_{{ $locale }}" 
                                       value="{{ $menuLabelValue }}" 
                                       class="form-control">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Page') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
