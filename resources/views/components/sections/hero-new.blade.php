@props(['section' => null, 'banners' => collect()])

@php
    // Helper function to get localized value
    // Supports both JSON arrays and Translatable models
    if (!function_exists('getLocalizedValue')) {
        function getLocalizedValue($value, $locale = null) {
            if (empty($value)) return '';
            $locale = $locale ?? app()->getLocale();
            
            // If value is already a string (from Translatable), return it directly
            if (is_string($value) && !empty($value)) {
                // Try to decode as JSON first
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // It's JSON, get the locale value
                    return $decoded[$locale] ?? $decoded['en'] ?? ($decoded[array_key_first($decoded)] ?? '');
                }
                // It's a plain string, return it
                return $value;
            }
            
            // If value is an array (from JSON cast)
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? ($value[array_key_first($value)] ?? '');
            }
            
            return '';
        }
    }

    $section = $section ?? (object)[];
    $displayBanners = $banners->count() > 0 ? $banners : collect();
    $title = getLocalizedValue($section->title ?? '') ?: __('Transform Your Business');
    $subtitle = getLocalizedValue($section->subtitle ?? '');
@endphp

<!-- hero section start -->
@if($displayBanners->count() > 0)
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                @foreach($displayBanners as $index => $banner)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="hero-slide" style="background: linear-gradient( color-mix(in srgb, var(--brand-primary) 70%, transparent), color-mix(in srgb, var(--gradient-end-color) 70%, transparent) ), url('{{ isset($banner->image_path) && $banner->image_path ? asset('storage/'.$banner->image_path) : 'https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80' }}') center/cover no-repeat;">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8 text-center hero-content">
                                        <h1 class="hero-title">
                                            {{ $banner->title ?? $title }}
                                        </h1>
                                        @if($banner->subtitle)
                                            <h2 class="hero-subtitle">{{ $banner->subtitle }}</h2>
                                        @elseif($subtitle)
                                            <h2 class="hero-subtitle">{{ $subtitle }}</h2>
                                        @endif
                                        @if($banner->description)
                                            <p class="hero-description">
                                                {{ $banner->description }}
                                            </p>
                                        @endif
                                        <div class="hero-buttons">
                                            @if($banner->button_text && $banner->button_link)
                                                <a href="{{ $banner->button_link }}" class="btn btn-primary-custom me-3">{{ $banner->button_text }}</a>
                                            @else
                                                <a href="{{ route('public.contact') }}" class="btn btn-primary-custom me-3">{{ __('Get a Quote') }}</a>
                                            @endif
                                            
                                            @if(isset($banner->secondary_button_text) && isset($banner->secondary_button_link))
                                                <a href="{{ $banner->secondary_button_link }}" class="btn btn-outline-custom">{{ $banner->secondary_button_text }}</a>
                                            @else
                                                <a href="{{ route('public.about') }}" class="btn btn-outline-custom">{{ __('About Us') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('Previous') }}</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('Next') }}</span>
            </button>
            
            <!-- Carousel Indicators -->
            @if($displayBanners->count() > 1)
                <div class="carousel-indicators">
                    @foreach($displayBanners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" 
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="{{ __('Slide') }} {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@else
    <!-- Default Hero Section if no banners exist -->
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                <!-- Default Slide 1 -->
                <div class="carousel-item active">
                    <div class="hero-slide" style="background: linear-gradient(rgba(61, 79, 96, 0.7), rgba(23, 162, 184, 0.7)), url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat;">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 text-center hero-content">
                                    <h1 class="hero-title">{{ $title }}</h1>
                                    @if($subtitle)
                                        <h2 class="hero-subtitle">{{ $subtitle }}</h2>
                                    @endif
                                    <p class="hero-description">
                                        {{ __('Transform your business with our comprehensive loyalty and marketing platform. Earn points, get discounts, and grow your network.') }}
                                    </p>
                                    <div class="hero-buttons">
                                        <a href="{{ route('public.contact') }}" class="btn btn-primary-custom me-3">{{ __('Get Started') }}</a>
                                        <a href="{{ route('public.about') }}" class="btn btn-outline-custom">{{ __('Learn More') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
<!-- hero section end -->