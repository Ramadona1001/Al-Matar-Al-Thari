@extends('layouts.dashboard')

@section('title', __('Site Settings'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Site Settings') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<form method="POST" action="{{ route('admin.site.settings.update') }}" enctype="multipart/form-data" id="siteSettingsForm">
    @csrf
    
    <!-- Tabs Navigation -->
    <div class="card modern-card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-primary-subtle">
                    <i class="fas fa-cog text-primary"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Site Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Site Settings') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>{{ __('General') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab">
                        <i class="fas fa-palette me-2"></i>{{ __('Branding') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="theme-colors-tab" data-bs-toggle="tab" data-bs-target="#theme-colors" type="button" role="tab">
                        <i class="fas fa-paint-brush me-2"></i>{{ __('Theme Colors') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                        <i class="fas fa-search me-2"></i>{{ __('SEO') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                        <i class="fas fa-address-book me-2"></i>{{ __('Contact') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab">
                        <i class="fas fa-window-maximize me-2"></i>{{ __('Footer') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom" type="button" role="tab">
                        <i class="fas fa-code me-2"></i>{{ __('Custom Code') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabsContent">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('Basic Information') }}</h6>
                        </div>
                        
                        <!-- Multi-Language Brand Name -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('Brand Name') }} <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                @foreach($locales as $locale)
                                    <div class="col-md-6">
                                        <label for="brand_name_{{ $locale }}" class="form-label small">
                                            <span class="me-1">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                                            {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                        </label>
                                        @php
                                            $brandNameValue = old("brand_name.{$locale}", is_array($settings->brand_name) ? ($settings->brand_name[$locale] ?? '') : ($settings->brand_name ?? ''));
                                        @endphp
                                        <input type="text" 
                                               name="brand_name[{{ $locale }}]" 
                                               id="brand_name_{{ $locale }}" 
                                               value="{{ $brandNameValue }}" 
                                               class="form-control @error("brand_name.{$locale}") is-invalid @enderror"
                                               required>
                                        @error("brand_name.{$locale}")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Colors -->
                        <div class="col-md-6">
                            <label for="primary_color" class="form-label fw-semibold">{{ __('Primary Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="primary_color" 
                                       name="primary_color" 
                                       value="{{ old('primary_color', $settings->primary_color ?? '#0d6efd') }}"
                                       title="{{ __('Choose primary color') }}">
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ old('primary_color', $settings->primary_color ?? '#0d6efd') }}"
                                       onchange="document.getElementById('primary_color').value = this.value">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="secondary_color" class="form-label fw-semibold">{{ __('Secondary Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="secondary_color" 
                                       name="secondary_color" 
                                       value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}"
                                       title="{{ __('Choose secondary color') }}">
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}"
                                       onchange="document.getElementById('secondary_color').value = this.value">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branding Tab -->
                <div class="tab-pane fade" id="branding" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('Logo & Icons') }}</h6>
                        </div>
                        
                        <!-- Logo -->
                        <div class="col-md-6">
                            <label for="logo" class="form-label fw-semibold">{{ __('Main Logo') }}</label>
                            <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if(!empty($settings->logo_path))
                                <div class="mt-3">
                                    <label class="form-label small">{{ __('Current Logo') }}</label>
                                    <div>
                                        <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Footer Logo -->
                        <div class="col-md-6">
                            <label for="footer_logo" class="form-label fw-semibold">{{ __('Footer Logo') }}</label>
                            <input type="file" name="footer_logo" id="footer_logo" class="form-control @error('footer_logo') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            @error('footer_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if(!empty($settings->footer_logo_path))
                                <div class="mt-3">
                                    <label class="form-label small">{{ __('Current Footer Logo') }}</label>
                                    <div>
                                        <img src="{{ asset('storage/'.$settings->footer_logo_path) }}" alt="Footer Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Favicon -->
                        <div class="col-md-6">
                            <label for="favicon" class="form-label fw-semibold">{{ __('Favicon') }}</label>
                            <input type="file" name="favicon" id="favicon" class="form-control @error('favicon') is-invalid @enderror" accept="image/png,image/x-icon,image/ico">
                            <small class="text-muted">{{ __('Recommended: 32x32px or 16x16px PNG/ICO') }}</small>
                            @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if(!empty($settings->favicon_path))
                                <div class="mt-3">
                                    <label class="form-label small">{{ __('Current Favicon') }}</label>
                                    <div>
                                        <img src="{{ asset('storage/'.$settings->favicon_path) }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Preloader Icon -->
                        <div class="col-md-6">
                            <label for="preloader_icon" class="form-label fw-semibold">{{ __('Preloader Icon') }}</label>
                            <input type="file" name="preloader_icon" id="preloader_icon" class="form-control @error('preloader_icon') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml,image/gif">
                            <small class="text-muted">{{ __('Icon shown while page is loading (GIF/PNG/SVG)') }}</small>
                            @error('preloader_icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if(!empty($settings->preloader_icon_path))
                                <div class="mt-3">
                                    <label class="form-label small">{{ __('Current Preloader Icon') }}</label>
                                    <div>
                                        <img src="{{ asset('storage/'.$settings->preloader_icon_path) }}" alt="Preloader" class="img-thumbnail" style="max-height: 64px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Theme Colors Tab -->
                <div class="tab-pane fade" id="theme-colors" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>{{ __('Theme Colors') }}</strong> - {{ __('Colors inspired by your logo. These colors will be used throughout the website.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Theme Colors Section -->
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">
                                <i class="fas fa-palette me-2"></i>{{ __('Main Theme Colors') }}
                            </h6>
                        </div>

                        <!-- Primary Color -->
                        <div class="col-md-4">
                            <label for="theme_primary_color" class="form-label fw-semibold">
                                {{ __('Primary Color') }} <small class="text-muted">({{ __('Dark Green') }})</small>
                            </label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="theme_primary_color" 
                                       name="theme_primary_color" 
                                       value="{{ old('theme_primary_color', $settings->theme_primary_color ?? '#1B4332') }}"
                                       title="{{ __('Choose primary color') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="theme_primary_color_text"
                                       value="{{ old('theme_primary_color', $settings->theme_primary_color ?? '#1B4332') }}"
                                       placeholder="#1B4332">
                            </div>
                            <small class="text-muted">{{ __('Main brand color (dark forest green)') }}</small>
                        </div>

                        <!-- Secondary Color -->
                        <div class="col-md-4">
                            <label for="theme_secondary_color" class="form-label fw-semibold">
                                {{ __('Secondary Color') }} <small class="text-muted">({{ __('Golden') }})</small>
                            </label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="theme_secondary_color" 
                                       name="theme_secondary_color" 
                                       value="{{ old('theme_secondary_color', $settings->theme_secondary_color ?? '#D4AF37') }}"
                                       title="{{ __('Choose secondary color') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="theme_secondary_color_text"
                                       value="{{ old('theme_secondary_color', $settings->theme_secondary_color ?? '#D4AF37') }}"
                                       placeholder="#D4AF37">
                            </div>
                            <small class="text-muted">{{ __('Accent color (golden/brown)') }}</small>
                        </div>

                        <!-- Accent Color -->
                        <div class="col-md-4">
                            <label for="theme_accent_color" class="form-label fw-semibold">
                                {{ __('Accent Color') }}
                            </label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="theme_accent_color" 
                                       name="theme_accent_color" 
                                       value="{{ old('theme_accent_color', $settings->theme_accent_color ?? '#D4AF37') }}"
                                       title="{{ __('Choose accent color') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="theme_accent_color_text"
                                       value="{{ old('theme_accent_color', $settings->theme_accent_color ?? '#D4AF37') }}"
                                       placeholder="#D4AF37">
                            </div>
                            <small class="text-muted">{{ __('Additional accent color') }}</small>
                        </div>

                        <!-- Gradient Colors Section -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">
                                <i class="fas fa-gradient me-2"></i>{{ __('Gradient Colors') }}
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="gradient_start_color" class="form-label fw-semibold">{{ __('Gradient Start Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="gradient_start_color" 
                                       name="gradient_start_color" 
                                       value="{{ old('gradient_start_color', $settings->gradient_start_color ?? '#1B4332') }}"
                                       title="{{ __('Gradient start') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="gradient_start_color_text"
                                       value="{{ old('gradient_start_color', $settings->gradient_start_color ?? '#1B4332') }}"
                                       placeholder="#1B4332">
                            </div>
                            <small class="text-muted">{{ __('Start of gradient effect') }}</small>
                        </div>

                        <div class="col-md-6">
                            <label for="gradient_end_color" class="form-label fw-semibold">{{ __('Gradient End Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="gradient_end_color" 
                                       name="gradient_end_color" 
                                       value="{{ old('gradient_end_color', $settings->gradient_end_color ?? '#2D5016') }}"
                                       title="{{ __('Gradient end') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="gradient_end_color_text"
                                       value="{{ old('gradient_end_color', $settings->gradient_end_color ?? '#2D5016') }}"
                                       placeholder="#2D5016">
                            </div>
                            <small class="text-muted">{{ __('End of gradient effect') }}</small>
                        </div>

                        <!-- Gradient Preview -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('Gradient Preview') }}</label>
                            <div class="border rounded p-4" 
                                 id="gradient-preview"
                                 style="background: linear-gradient(135deg, {{ old('gradient_start_color', $settings->gradient_start_color ?? '#1B4332') }} 0%, {{ old('gradient_end_color', $settings->gradient_end_color ?? '#2D5016') }} 100%); height: 100px;">
                                <div class="text-white text-center d-flex align-items-center justify-content-center h-100">
                                    <span class="fw-bold">{{ __('Gradient Preview') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Text Colors Section -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">
                                <i class="fas fa-font me-2"></i>{{ __('Text Colors') }}
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label for="text_primary_color" class="form-label fw-semibold">{{ __('Primary Text Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="text_primary_color" 
                                       name="text_primary_color" 
                                       value="{{ old('text_primary_color', $settings->text_primary_color ?? '#1B4332') }}"
                                       title="{{ __('Primary text') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="text_primary_color_text"
                                       value="{{ old('text_primary_color', $settings->text_primary_color ?? '#1B4332') }}"
                                       placeholder="#1B4332">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="text_secondary_color" class="form-label fw-semibold">{{ __('Secondary Text Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="text_secondary_color" 
                                       name="text_secondary_color" 
                                       value="{{ old('text_secondary_color', $settings->text_secondary_color ?? '#6C757D') }}"
                                       title="{{ __('Secondary text') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="text_secondary_color_text"
                                       value="{{ old('text_secondary_color', $settings->text_secondary_color ?? '#6C757D') }}"
                                       placeholder="#6C757D">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="text_on_primary_color" class="form-label fw-semibold">{{ __('Text on Primary Color') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="text_on_primary_color" 
                                       name="text_on_primary_color" 
                                       value="{{ old('text_on_primary_color', $settings->text_on_primary_color ?? '#FFFFFF') }}"
                                       title="{{ __('Text on primary background') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="text_on_primary_color_text"
                                       value="{{ old('text_on_primary_color', $settings->text_on_primary_color ?? '#FFFFFF') }}"
                                       placeholder="#FFFFFF">
                            </div>
                        </div>

                        <!-- Background Colors Section -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">
                                <i class="fas fa-fill me-2"></i>{{ __('Background Colors') }}
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label for="bg_primary_color" class="form-label fw-semibold">{{ __('Primary Background') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="bg_primary_color" 
                                       name="bg_primary_color" 
                                       value="{{ old('bg_primary_color', $settings->bg_primary_color ?? '#FFFFFF') }}"
                                       title="{{ __('Primary background') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="bg_primary_color_text"
                                       value="{{ old('bg_primary_color', $settings->bg_primary_color ?? '#FFFFFF') }}"
                                       placeholder="#FFFFFF">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="bg_secondary_color" class="form-label fw-semibold">{{ __('Secondary Background') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="bg_secondary_color" 
                                       name="bg_secondary_color" 
                                       value="{{ old('bg_secondary_color', $settings->bg_secondary_color ?? '#F8F9FA') }}"
                                       title="{{ __('Secondary background') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="bg_secondary_color_text"
                                       value="{{ old('bg_secondary_color', $settings->bg_secondary_color ?? '#F8F9FA') }}"
                                       placeholder="#F8F9FA">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="bg_dark_color" class="form-label fw-semibold">{{ __('Dark Background') }}</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="bg_dark_color" 
                                       name="bg_dark_color" 
                                       value="{{ old('bg_dark_color', $settings->bg_dark_color ?? '#1B4332') }}"
                                       title="{{ __('Dark background') }}">
                                <input type="text" 
                                       class="form-control" 
                                       id="bg_dark_color_text"
                                       value="{{ old('bg_dark_color', $settings->bg_dark_color ?? '#1B4332') }}"
                                       placeholder="#1B4332">
                            </div>
                        </div>

                        <!-- Color Preview Section -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">
                                <i class="fas fa-eye me-2"></i>{{ __('Color Preview') }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="card border">
                                        <div class="card-body p-3 text-center">
                                            <div class="rounded mb-2" 
                                                 id="preview-primary"
                                                 style="background-color: {{ old('theme_primary_color', $settings->theme_primary_color ?? '#1B4332') }}; height: 60px; width: 100%;"></div>
                                            <small class="text-muted">{{ __('Primary') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border">
                                        <div class="card-body p-3 text-center">
                                            <div class="rounded mb-2" 
                                                 id="preview-secondary"
                                                 style="background-color: {{ old('theme_secondary_color', $settings->theme_secondary_color ?? '#D4AF37') }}; height: 60px; width: 100%;"></div>
                                            <small class="text-muted">{{ __('Secondary') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border">
                                        <div class="card-body p-3 text-center">
                                            <div class="rounded mb-2" 
                                                 id="preview-accent"
                                                 style="background-color: {{ old('theme_accent_color', $settings->theme_accent_color ?? '#D4AF37') }}; height: 60px; width: 100%;"></div>
                                            <small class="text-muted">{{ __('Accent') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border">
                                        <div class="card-body p-3 text-center">
                                            <div class="rounded mb-2" 
                                                 id="preview-gradient"
                                                 style="background: linear-gradient(135deg, {{ old('gradient_start_color', $settings->gradient_start_color ?? '#1B4332') }} 0%, {{ old('gradient_end_color', $settings->gradient_end_color ?? '#2D5016') }} 100%); height: 60px; width: 100%;"></div>
                                            <small class="text-muted">{{ __('Gradient') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Tab -->
                <div class="tab-pane fade" id="seo" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('SEO Settings (Multi-Language)') }}</h6>
                        </div>
                        
                        <!-- Multi-Language SEO Fields -->
                        @foreach($locales as $locale)
                            <div class="col-12">
                                <div class="card border mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <span class="me-2">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                                            {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                @php
                                                    $metaTitleValue = old("meta_title.{$locale}", is_array($settings->meta_title) ? ($settings->meta_title[$locale] ?? '') : '');
                                                @endphp
                                                <label for="meta_title_{{ $locale }}" class="form-label fw-semibold">{{ __('Meta Title') }}</label>
                                                <input type="text" 
                                                       name="meta_title[{{ $locale }}]" 
                                                       id="meta_title_{{ $locale }}" 
                                                       value="{{ $metaTitleValue }}" 
                                                       class="form-control"
                                                       placeholder="{{ __('Page title for search engines') }}">
                                            </div>
                                            
                                            <div class="col-12">
                                                @php
                                                    $metaDescValue = old("meta_description.{$locale}", is_array($settings->meta_description) ? ($settings->meta_description[$locale] ?? '') : '');
                                                @endphp
                                                <label for="meta_description_{{ $locale }}" class="form-label fw-semibold">{{ __('Meta Description') }}</label>
                                                <textarea name="meta_description[{{ $locale }}]" 
                                                          id="meta_description_{{ $locale }}" 
                                                          rows="2" 
                                                          class="form-control"
                                                          placeholder="{{ __('Brief description for search engines') }}">{{ $metaDescValue }}</textarea>
                                            </div>
                                            
                                            <div class="col-12">
                                                @php
                                                    $metaKeywordsValue = old("meta_keywords.{$locale}", is_array($settings->meta_keywords) ? ($settings->meta_keywords[$locale] ?? '') : '');
                                                @endphp
                                                <label for="meta_keywords_{{ $locale }}" class="form-label fw-semibold">{{ __('Meta Keywords') }}</label>
                                                <input type="text" 
                                                       name="meta_keywords[{{ $locale }}]" 
                                                       id="meta_keywords_{{ $locale }}" 
                                                       value="{{ $metaKeywordsValue }}" 
                                                       class="form-control"
                                                       placeholder="{{ __('keyword1, keyword2, keyword3') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Contact Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('Contact Information') }}</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="contact_email" class="form-label fw-semibold">{{ __('Contact Email') }}</label>
                            <input type="email" 
                                   name="contact_email" 
                                   id="contact_email" 
                                   value="{{ old('contact_email', $settings->contact_email ?? '') }}" 
                                   class="form-control @error('contact_email') is-invalid @enderror">
                            @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label fw-semibold">{{ __('Contact Phone') }}</label>
                            <input type="text" 
                                   name="contact_phone" 
                                   id="contact_phone" 
                                   value="{{ old('contact_phone', $settings->contact_phone ?? '') }}" 
                                   class="form-control @error('contact_phone') is-invalid @enderror">
                            @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <!-- Multi-Language Contact Address -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('Contact Address') }}</label>
                            <div class="row g-3">
                                @foreach($locales as $locale)
                                    <div class="col-md-6">
                                        <label for="contact_address_{{ $locale }}" class="form-label small">
                                            <span class="me-1">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                                            {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                        </label>
                                        @php
                                            $contactAddressValue = old("contact_address.{$locale}", is_array($settings->contact_address) ? ($settings->contact_address[$locale] ?? '') : ($settings->contact_address ?? ''));
                                        @endphp
                                        <input type="text" 
                                               name="contact_address[{{ $locale }}]" 
                                               id="contact_address_{{ $locale }}" 
                                               value="{{ $contactAddressValue }}" 
                                               class="form-control">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tab -->
                <div class="tab-pane fade" id="footer" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('Footer Settings (Multi-Language)') }}</h6>
                        </div>
                        
                        <!-- Multi-Language Footer Text -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('Footer Text') }}</label>
                            <div class="row g-3">
                                @foreach($locales as $locale)
                                    <div class="col-md-6">
                                        <label for="footer_text_{{ $locale }}" class="form-label small">
                                            <span class="me-1">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                                            {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                        </label>
                                        @php
                                            $footerTextValue = old("footer_text.{$locale}", is_array($settings->footer_text) ? ($settings->footer_text[$locale] ?? '') : ($settings->footer_text ?? ''));
                                        @endphp
                                        <textarea name="footer_text[{{ $locale }}]" 
                                                  id="footer_text_{{ $locale }}" 
                                                  rows="3" 
                                                  class="form-control"
                                                  placeholder="{{ __('Footer description text') }}">{{ $footerTextValue }}</textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Multi-Language Footer Copyright -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('Footer Copyright') }}</label>
                            <div class="row g-3">
                                @foreach($locales as $locale)
                                    <div class="col-md-6">
                                        <label for="footer_copyright_{{ $locale }}" class="form-label small">
                                            <span class="me-1">{{ config("localization.locale_flags.{$locale}", '🌐') }}</span>
                                            {{ config("localization.locale_names.{$locale}", strtoupper($locale)) }}
                                        </label>
                                        @php
                                            $footerCopyrightValue = old("footer_copyright.{$locale}", is_array($settings->footer_copyright) ? ($settings->footer_copyright[$locale] ?? '') : ($settings->footer_copyright ?? ''));
                                        @endphp
                                        <input type="text" 
                                               name="footer_copyright[{{ $locale }}]" 
                                               id="footer_copyright_{{ $locale }}" 
                                               value="{{ $footerCopyrightValue }}" 
                                               class="form-control"
                                               placeholder="{{ __('Copyright © 2024 Your Company') }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Footer Background Image -->
                        <div class="col-12">
                            <label for="footer_bg_image" class="form-label fw-semibold">{{ __('Footer Background Image') }}</label>
                            <input type="file" name="footer_bg_image" id="footer_bg_image" class="form-control @error('footer_bg_image') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp">
                            <small class="text-muted">{{ __('Recommended: High resolution image (1920x1080px or larger). Max size: 5MB') }}</small>
                            @error('footer_bg_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if(!empty($settings->footer_bg_image_path))
                                <div class="mt-3">
                                    <label class="form-label small">{{ __('Current Footer Background Image') }}</label>
                                    <div>
                                        <img src="{{ asset('storage/'.$settings->footer_bg_image_path) }}" alt="Footer Background" class="img-thumbnail" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Footer Menu Groups Management -->
                        <div class="col-12 mt-4">
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="fas fa-list me-2"></i>{{ __('Footer Menu Groups') }}
                                    </h6>
                                    <a href="{{ route('admin.footer-menu-groups.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>{{ __('Add Group') }}
                                    </a>
                                </div>
                                <div class="card-body">
                                    @php
                                        $footerMenuGroups = class_exists(\App\Models\FooterMenuGroup::class)
                                            ? \App\Models\FooterMenuGroup::with('menuItems')->ordered()->get()
                                            : collect();
                                    @endphp
                                    
                                    @if($footerMenuGroups->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Group Name') }}</th>
                                                        <th>{{ __('Menu Items') }}</th>
                                                        <th>{{ __('Order') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th class="text-end">{{ __('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($footerMenuGroups as $group)
                                                        @php
                                                            $currentLocale = app()->getLocale();
                                                            $currentTranslation = $group->translate($currentLocale);
                                                            $enTranslation = $group->translate('en');
                                                            $groupName = ($currentTranslation && $currentTranslation->name) 
                                                                ? $currentTranslation->name 
                                                                : (($enTranslation && $enTranslation->name) ? $enTranslation->name : '-');
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $groupName }}</strong>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info-subtle text-info">
                                                                    {{ $group->menuItems->count() }} {{ __('Items') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-dark text-secondary">
                                                                    {{ $group->order ?? 0 }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($group->is_active)
                                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                                @else
                                                                    <span class="badge bg-warning">{{ __('Inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <a href="{{ route('admin.footer-menu-groups.edit', $group) }}" 
                                                                       class="btn btn-outline-primary" 
                                                                       title="{{ __('Edit') }}"
                                                                       target="_blank">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.menus.index', ['name' => 'footer', 'group' => $group->id]) }}" 
                                                                       class="btn btn-outline-info" 
                                                                       title="{{ __('Manage Menu Items') }}"
                                                                       target="_blank">
                                                                        <i class="fas fa-link"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.footer-menu-groups.index') }}" 
                                                                       class="btn btn-outline-danger" 
                                                                       title="{{ __('Delete (Go to manage page)') }}"
                                                                       target="_blank">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-2">{{ __('No footer menu groups found.') }}</p>
                                            <a href="{{ route('admin.footer-menu-groups.create') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus me-1"></i>{{ __('Create First Group') }}
                                            </a>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 pt-3 border-top">
                                        <a href="{{ route('admin.footer-menu-groups.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ __('Manage All Footer Menu Groups') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Code Tab -->
                <div class="tab-pane fade" id="custom" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-gray-900 mb-3 border-bottom pb-2">{{ __('Custom CSS & JavaScript') }}</h6>
                        </div>
                        
                        <div class="col-12">
                            <label for="custom_styles" class="form-label fw-semibold">
                                {{ __('Custom CSS') }}
                                <small class="text-muted">({{ __('Will be added in <style> tag') }})</small>
                            </label>
                            <textarea name="custom_styles" 
                                      id="custom_styles" 
                                      rows="10" 
                                      class="form-control font-monospace @error('custom_styles') is-invalid @enderror"
                                      placeholder="/* Your custom CSS here */">{{ old('custom_styles', $settings->custom_styles ?? '') }}</textarea>
                            @error('custom_styles')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">{{ __('Add custom CSS styles. These will be injected into the page head.') }}</small>
                        </div>
                        
                        <div class="col-12">
                            <label for="custom_scripts" class="form-label fw-semibold">
                                {{ __('Custom JavaScript') }}
                                <small class="text-muted">({{ __('Will be added before </body> tag') }})</small>
                            </label>
                            <textarea name="custom_scripts" 
                                      id="custom_scripts" 
                                      rows="10" 
                                      class="form-control font-monospace @error('custom_scripts') is-invalid @enderror"
                                      placeholder="// Your custom JavaScript here">{{ old('custom_scripts', $settings->custom_scripts ?? '') }}</textarea>
                            @error('custom_scripts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">{{ __('Add custom JavaScript code. These will be injected before the closing body tag.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Save Button -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-lg">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-primary btn-lg btn-animated">
            <i class="fas fa-save me-2"></i>{{ __('Save All Settings') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync color picker with text input
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(function(colorInput) {
        const textInput = colorInput.nextElementSibling;
        if (textInput && textInput.tagName === 'INPUT') {
            colorInput.addEventListener('change', function() {
                textInput.value = this.value;
                updatePreviews();
            });
            textInput.addEventListener('change', function() {
                // Validate hex color
                const hexPattern = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
                if (hexPattern.test(this.value)) {
                    colorInput.value = this.value;
                    updatePreviews();
                }
            });
        }
    });
    
    // Function to update all previews
    function updatePreviews() {
        // Update gradient preview
        const gradientStart = document.getElementById('gradient_start_color')?.value || '#1B4332';
        const gradientEnd = document.getElementById('gradient_end_color')?.value || '#2D5016';
        const gradientPreview = document.getElementById('gradient-preview');
        if (gradientPreview) {
            gradientPreview.style.background = `linear-gradient(135deg, ${gradientStart} 0%, ${gradientEnd} 100%)`;
        }
        
        // Update color preview boxes
        const primaryColor = document.getElementById('theme_primary_color')?.value || '#1B4332';
        const secondaryColor = document.getElementById('theme_secondary_color')?.value || '#D4AF37';
        const accentColor = document.getElementById('theme_accent_color')?.value || '#D4AF37';
        
        const previewPrimary = document.getElementById('preview-primary');
        const previewSecondary = document.getElementById('preview-secondary');
        const previewAccent = document.getElementById('preview-accent');
        const previewGradient = document.getElementById('preview-gradient');
        
        if (previewPrimary) previewPrimary.style.backgroundColor = primaryColor;
        if (previewSecondary) previewSecondary.style.backgroundColor = secondaryColor;
        if (previewAccent) previewAccent.style.backgroundColor = accentColor;
        if (previewGradient) {
            previewGradient.style.background = `linear-gradient(135deg, ${gradientStart} 0%, ${gradientEnd} 100%)`;
        }
    }
    
    // Initialize previews on load
    updatePreviews();
    
    // Form validation
    const form = document.getElementById('siteSettingsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Ensure at least one brand name is provided
            const brandNameInputs = document.querySelectorAll('input[name^="brand_name["]');
            let hasBrandName = false;
            brandNameInputs.forEach(function(input) {
                if (input.value.trim() !== '') {
                    hasBrandName = true;
                }
            });
            
            if (!hasBrandName) {
                e.preventDefault();
                alert('{{ __("Please provide at least one brand name.") }}');
                document.getElementById('general-tab').click();
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection
