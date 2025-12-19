@props(['section' => null, 'testimonials' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    // For testimonials sections, always use $testimonials if available, otherwise use activeItems
    $sectionType = $section->type ?? $section->component_name ?? '';
    if (in_array($sectionType, ['testimonials', 'reviews']) && ($testimonials ?? collect())->count() > 0) {
        $displayTestimonials = $testimonials;
    } else {
        $displayTestimonials = $items->count() > 0 ? $items : ($testimonials ?? collect());
    }
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('What Our Clients Say'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('What Our Clients Say');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : '');
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

@if($displayTestimonials->count() > 0)
    <section class="section testimonials-section" style="padding: 80px 0; background: #f5f5f5;">
        <div class="container">
            @if($title)
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem;">{{ $title }}</h2>
                        @if($subtitle)
                            <p style="font-size: 1.2rem; color: #6b7280;">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Swiper Slider -->
            <div class="testimonials-slider-wrapper" style="position: relative; padding: 0;">
                <div class="swiper testimonials-swiper">
                    <div class="swiper-wrapper">
                        @foreach($displayTestimonials as $testimonial)
                            @php
                                $testimonialText = '';
                                $testimonialName = '';
                                $testimonialRole = '';
                                $testimonialCompany = '';
                                if (is_object($testimonial) && method_exists($testimonial, 'translate')) {
                                    $testCurrent = $testimonial->translate($currentLocale);
                                    $testEn = $testimonial->translate('en');
                                    $testimonialText = ($testCurrent && isset($testCurrent->testimonial) && $testCurrent->testimonial) 
                                        ? $testCurrent->testimonial 
                                        : (($testEn && isset($testEn->testimonial) && $testEn->testimonial) ? $testEn->testimonial : '');
                                    $testimonialName = ($testCurrent && isset($testCurrent->name) && $testCurrent->name) 
                                        ? $testCurrent->name 
                                        : (($testEn && isset($testEn->name) && $testEn->name) ? $testEn->name : '');
                                    $testimonialRole = ($testCurrent && isset($testCurrent->position) && $testCurrent->position) 
                                        ? $testCurrent->position 
                                        : (($testEn && isset($testEn->position) && $testEn->position) ? $testEn->position : '');
                                    $testimonialCompany = ($testCurrent && isset($testCurrent->company) && $testCurrent->company) 
                                        ? $testCurrent->company 
                                        : (($testEn && isset($testEn->company) && $testEn->company) ? $testEn->company : '');
                                }
                                if (empty($testimonialText)) {
                                    $testimonialText = is_array($testimonial->testimonial ?? '') ? ($testimonial->testimonial[$currentLocale] ?? $testimonial->testimonial['en'] ?? '') : ($testimonial->testimonial ?? $testimonial->content ?? '');
                                }
                                if (empty($testimonialName)) {
                                    $testimonialName = is_array($testimonial->name ?? '') ? ($testimonial->name[$currentLocale] ?? $testimonial->name['en'] ?? '') : ($testimonial->name ?? '');
                                }
                                if (empty($testimonialRole)) {
                                    $testimonialRole = is_array($testimonial->position ?? '') ? ($testimonial->position[$currentLocale] ?? $testimonial->position['en'] ?? '') : ($testimonial->position ?? $testimonial->role ?? '');
                                }
                                if (empty($testimonialCompany)) {
                                    $testimonialCompany = is_array($testimonial->company ?? '') ? ($testimonial->company[$currentLocale] ?? $testimonial->company['en'] ?? '') : ($testimonial->company ?? '');
                                }
                                
                                $avatar = $testimonial->avatar ?? null;
                                $rating = $testimonial->rating ?? 5;
                            @endphp
                            <div class="swiper-slide">
                                <div class="testimonial-card" style="background: #ffffff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); height: 100%; display: flex; flex-direction: column;">
                                    {{-- Profile Image with Quote Icon --}}
                                    <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 1.5rem;">
                                        @if($avatar)
                                            <img src="{{ asset('storage/'.$avatar) }}" alt="{{ $testimonialName }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                                        @else
                                            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient( color-mix(in srgb, var(--brand-primary) 70%, transparent), color-mix(in srgb, var(--gradient-end-color) 70%, transparent) ); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 2rem; border: 3px solid #ffffff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                                                {{ strtoupper(substr($testimonialName, 0, 1)) }}
                                            </div>
                                        @endif
                                        {{-- Quote Icon Overlay --}}
                                        <div style="position: absolute; bottom: -5px; right: -5px; width: 32px; height: 32px; background: var(--bs-danger-text-emphasis); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3); border: 2px solid #ffffff;">
                                            <i class="fas fa-quote-left" style="font-size: 0.75rem; color: #ffffff;"></i>
                                        </div>
                                    </div>
                                    
                                    {{-- Name --}}
                                    @if($testimonialName)
                                        <h4 style="font-size: 1.25rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; text-align: center;">{{ $testimonialName }}</h4>
                                    @endif
                                    
                                    {{-- Position --}}
                                    @if($testimonialRole)
                                        <p style="font-size: 0.95rem; color: #6b7280; margin-bottom: 1.5rem; text-align: center;">{{ $testimonialRole }}</p>
                                    @endif
                                    
                                    {{-- Testimonial Text --}}
                                    @if($testimonialText)
                                        <p style="color: #6b7280; line-height: 1.7; font-size: 0.95rem; margin-bottom: 1.5rem; flex-grow: 1; text-align: center;">{{ $testimonialText }}</p>
                                    @endif
                                    
                                    {{-- Company Logo (if available) --}}
                                    @if($testimonialCompany)
                                        <div style="text-align: center; margin-bottom: 1rem;">
                                            <span style="font-size: 0.9rem; color: #1a1a1a; font-weight: 600;">{{ $testimonialCompany }}</span>
                                        </div>
                                    @endif
                                    
                                    {{-- Star Rating --}}
                                    <div style="text-align: center; margin-top: auto;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $rating ? '' : '-o' }}" style="color: var(--bs-danger-text-emphasis); font-size: 1rem; margin: 0 2px;"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- Pagination --}}
                <div class="swiper-pagination testimonials-pagination" style="position: relative; margin-top: 2rem;"></div>
            </div>
        </div>
    </section>
    
    {{-- Swiper CSS & JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <style>
        .testimonials-swiper {
            width: 100%;
            padding: 20px 0 50px;
        }
        
        .testimonials-swiper .swiper-slide {
            height: auto;
        }
        
        
        .testimonials-swiper .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: var(--bs-danger-text-emphasis);
            opacity: 0.3;
        }
        
        .testimonials-swiper .swiper-pagination-bullet-active {
            opacity: 1;
            background: var(--bs-danger-text-emphasis);
        }
        
        .testimonial-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
        }
        
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const testimonialsSwiper = new Swiper('.testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.testimonials-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 1,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                },
            });
        });
    </script>
@endif
