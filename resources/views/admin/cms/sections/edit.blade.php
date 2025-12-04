@extends('layouts.dashboard')

@section('title', __('Edit Section'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">{{ __('Sections') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
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
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-puzzle-piece text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Section') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        @if($section->image_path)
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('Current Image') }}</label>
                <div>
                    <img src="{{ asset('storage/' . $section->image_path) }}" alt="Section Image" class="img-thumbnail" style="max-height: 200px; max-width: 100%;">
                </div>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.sections.update', $section) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- General Settings (Before Tabs) -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $section->name) }}" 
                           class="form-control @error('name') is-invalid @enderror" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="type" class="form-label fw-semibold">{{ __('Type') }} <span class="text-danger">*</span></label>
                    <select name="type" 
                            id="type" 
                            class="form-select @error('type') is-invalid @enderror" 
                            required>
                        <option value="">{{ __('Select Type') }}</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ old('type', $section->type) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="page" class="form-label fw-semibold">{{ __('Page') }}</label>
                    <select name="page" 
                            id="page" 
                            class="form-select @error('page') is-invalid @enderror">
                        <option value="">{{ __('Select Page') }}</option>
                        @foreach($pages ?? [] as $page)
                            <option value="{{ $page }}" {{ old('page', $section->page) == $page ? 'selected' : '' }}>{{ ucfirst($page) }}</option>
                        @endforeach
                    </select>
                    @error('page')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
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
                    @php
                        $translation = $section->translate($locale);
                        $titleValue = old("title.{$locale}", $translation ? $translation->title : '');
                        $subtitleValue = old("subtitle.{$locale}", $translation ? $translation->subtitle : '');
                        $contentValue = old("content.{$locale}", $translation ? $translation->content : '');
                    @endphp
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                         id="{{ $locale }}" 
                         role="tabpanel"
                         aria-labelledby="{{ $locale }}-tab">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="title_{{ $locale }}" class="form-label fw-semibold">{{ __('Title') }}</label>
                                <input type="text" 
                                       name="title[{{ $locale }}]" 
                                       id="title_{{ $locale }}" 
                                       value="{{ $titleValue }}" 
                                       class="form-control @error("title.{$locale}") is-invalid @enderror">
                                @error("title.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="subtitle_{{ $locale }}" class="form-label fw-semibold">{{ __('Subtitle') }}</label>
                                <input type="text" 
                                       name="subtitle[{{ $locale }}]" 
                                       id="subtitle_{{ $locale }}" 
                                       value="{{ $subtitleValue }}" 
                                       class="form-control @error("subtitle.{$locale}") is-invalid @enderror">
                                @error("subtitle.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="content_{{ $locale }}" class="form-label fw-semibold">{{ __('Content') }}</label>
                                <textarea name="content[{{ $locale }}]" 
                                          id="content_{{ $locale }}" 
                                          rows="8" 
                                          class="form-control @error("content.{$locale}") is-invalid @enderror">{{ $contentValue }}</textarea>
                                @error("content.{$locale}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Additional Settings (Outside Tabs) -->
            <div class="row g-4 mt-4 pt-4 border-top">
                <div class="col-12">
                    <h6 class="fw-bold text-gray-900 mb-3">{{ __('Additional Settings') }}</h6>
                </div>
                
                <div class="col-md-6">
                    <label for="image_path" class="form-label fw-semibold">{{ __('New Image') }}</label>
                    <input type="file" 
                           name="image_path" 
                           id="image_path" 
                           class="form-control @error('image_path') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    <small class="text-muted">{{ __('Leave empty to keep current image. Max size: 5MB') }}</small>
                    @error('image_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', $section->order ?? 0) }}" 
                           class="form-control @error('order') is-invalid @enderror" 
                           min="0">
                    <small class="text-muted">{{ __('Lower numbers appear first') }}</small>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="columns_per_row" class="form-label fw-semibold">{{ __('Columns Per Row') }}</label>
                    <select name="columns_per_row" 
                            id="columns_per_row" 
                            class="form-select @error('columns_per_row') is-invalid @enderror">
                        <option value="1" {{ old('columns_per_row', $section->columns_per_row ?? 1) == 1 ? 'selected' : '' }}>{{ __('1 Column (Full Width)') }}</option>
                        <option value="2" {{ old('columns_per_row', $section->columns_per_row ?? 1) == 2 ? 'selected' : '' }}>{{ __('2 Columns') }}</option>
                        <option value="3" {{ old('columns_per_row', $section->columns_per_row ?? 1) == 3 ? 'selected' : '' }}>{{ __('3 Columns') }}</option>
                        <option value="4" {{ old('columns_per_row', $section->columns_per_row ?? 1) == 4 ? 'selected' : '' }}>{{ __('4 Columns') }}</option>
                    </select>
                    <small class="text-muted">{{ __('Number of sections to display in one row') }}</small>
                    @error('columns_per_row')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input @error('is_visible') is-invalid @enderror" 
                               id="is_visible" 
                               name="is_visible" 
                               value="1" 
                               {{ old('is_visible', $section->is_visible) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_visible">{{ __('Visible') }}</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-save me-2"></i>{{ __('Update Section') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
