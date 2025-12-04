@extends('layouts.dashboard')

@section('title', __('Create Blog Post'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">{{ __('Blogs') }}</a></li>
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
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-blog text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Create Blog Post') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data">
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
                                <label for="excerpt_{{ $locale }}" class="form-label fw-semibold">{{ __('Excerpt') }}</label>
                                <textarea name="excerpt[{{ $locale }}]" 
                                          id="excerpt_{{ $locale }}" 
                                          rows="3" 
                                          class="form-control @error("excerpt.{$locale}") is-invalid @enderror">{{ old("excerpt.{$locale}") }}</textarea>
                                <small class="text-muted">{{ __('Brief summary of the blog post') }}</small>
                                @error("excerpt.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="content_{{ $locale }}" class="form-label fw-semibold">{{ __('Content') }} <span class="text-danger">*</span></label>
                                <textarea name="content[{{ $locale }}]" 
                                          id="content_{{ $locale }}" 
                                          rows="12" 
                                          class="form-control @error("content.{$locale}") is-invalid @enderror" 
                                          required>{{ old("content.{$locale}") }}</textarea>
                                @error("content.{$locale}")
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
                    <label for="author_id" class="form-label fw-semibold">{{ __('Author') }}</label>
                    <select name="author_id" 
                            id="author_id" 
                            class="form-select @error('author_id') is-invalid @enderror">
                        <option value="">{{ __('Select Author') }}</option>
                        @foreach($authors ?? [] as $author)
                            <option value="{{ $author->id }}" {{ old('author_id', auth()->id()) == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>
                    @error('author_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="featured_image" class="form-label fw-semibold">{{ __('Featured Image') }}</label>
                    <input type="file" 
                           name="featured_image" 
                           id="featured_image" 
                           class="form-control @error('featured_image') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    <small class="text-muted">{{ __('Max size: 5MB. Recommended: 1200x630px') }}</small>
                    @error('featured_image')
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
                    <label for="published_at" class="form-label fw-semibold">{{ __('Published At') }}</label>
                    <input type="datetime-local" 
                           name="published_at" 
                           id="published_at" 
                           value="{{ old('published_at') }}" 
                           class="form-control @error('published_at') is-invalid @enderror">
                    <small class="text-muted">{{ __('Schedule publication date and time') }}</small>
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input @error('is_published') is-invalid @enderror" 
                               id="is_published" 
                               name="is_published" 
                               value="1" 
                               {{ old('is_published') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_published">{{ __('Published') }}</label>
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
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-save me-2"></i>{{ __('Create Blog Post') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
