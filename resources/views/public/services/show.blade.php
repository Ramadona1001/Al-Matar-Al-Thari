@extends('layouts.new-design')

@php
    $currentLocale = app()->getLocale();
    $serviceTitle = is_array($service->title) ? ($service->title[$currentLocale] ?? reset($service->title)) : ($service->title ?? '');
    $serviceDescription = is_array($service->description) ? ($service->description[$currentLocale] ?? reset($service->description)) : ($service->description ?? '');
    $serviceShortDescription = is_array($service->short_description ?? '') ? ($service->short_description[$currentLocale] ?? $service->short_description['en'] ?? '') : ($service->short_description ?? '');
    $metaTitle = is_array($service->meta_title) ? ($service->meta_title[$currentLocale] ?? reset($service->meta_title)) : ($service->meta_title ?? $serviceTitle);
    $metaDescription = is_array($service->meta_description) ? ($service->meta_description[$currentLocale] ?? reset($service->meta_description)) : ($service->meta_description ?? '');
    
    $createdDate = $service->created_at->format('d M, Y');
@endphp

@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)

@section('content')
    <x-page-title 
        :title="$serviceTitle" 
        :subtitle="__('Service Details')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Services'), 'url' => route('public.services.index')],
            ['label' => $serviceTitle, 'url' => '#']
        ]"
    />
    
    <!-- Service Single Section -->
    <section class="service-single-section" style="padding: 80px 0; background: #f5f5f5;">
        <div class="container">
            <div class="row">
                <!-- Main Content Area -->
                <div class="col-lg-8 col-md-12 mb-5 mb-lg-0">
                    <!-- Service Post -->
                    <article class="service-post-card mb-4" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                        {{-- Featured Image --}}
                        @if($service->image_path)
                            <div class="service-featured-image" style="width: 100%; height: 500px; overflow: hidden;">
                                <img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $serviceTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        
                        <div class="service-post-body" style="padding: 2.5rem;">
                            {{-- Metadata --}}
                            <div class="service-meta mb-4" style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.9rem; color: #6b7280;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-calendar" style="color: var(--brand-primary);"></i>
                                    <span>{{ $createdDate }}</span>
                                </div>
                                @if($service->icon)
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="{{ $service->icon }}" style="color: var(--brand-primary);"></i>
                                        <span>{{ __('Service') }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Title --}}
                            <h1 class="service-title mb-4" style="font-size: 2.5rem; font-weight: 700; color: #1a1a1a; line-height: 1.3;">
                                {{ $serviceTitle }}
                            </h1>
                            
                            {{-- Short Description / Excerpt --}}
                            @if($serviceShortDescription)
                                <div class="service-excerpt mb-4" style="font-size: 1.1rem; color: #6b7280; line-height: 1.8;">
                                    {{ $serviceShortDescription }}
                                </div>
                            @endif
                            
                            {{-- Content --}}
                            @if($serviceDescription)
                                <div class="service-content mb-4" style="color: #4b5563; line-height: 1.8; font-size: 1rem;">
                                    {!! $serviceDescription !!}
                                </div>
                            @endif
                            
                            {{-- Features Section --}}
                            @if(isset($service->features) && is_array($service->features) && count($service->features) > 0)
                                <div class="service-features-section mb-4">
                                    <h3 class="mb-3" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">{{ __('How We Work') }}</h3>
                                    <div class="how-works-wrapper" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                                        @foreach($service->features as $index => $feature)
                                            <div class="work-step" style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; text-align: center;">
                                                <div class="work-step-icon mb-3" style="font-size: 2.5rem; color: var(--brand-primary);">
                                                    @if(isset($feature['icon']))
                                                        <i class="{{ $feature['icon'] }}"></i>
                                                    @else
                                                        <i class="fas fa-check-circle"></i>
                                                    @endif
                                                </div>
                                                @if(isset($feature['title']))
                                                    <h4 style="font-size: 1.1rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.5rem;">{{ $feature['title'] }}</h4>
                                                @endif
                                                @if(isset($feature['description']))
                                                    <p style="font-size: 0.9rem; color: #6b7280; margin: 0; line-height: 1.6;">{{ $feature['description'] }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Core Features List --}}
                            @if(isset($service->features) && is_array($service->features) && count($service->features) > 0)
                                <div class="service-features-list mb-4">
                                    <h3 class="mb-3" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">{{ __('Core Features') }}</h3>
                                    <div class="service-feature-list" style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                                        @foreach($service->features as $feature)
                                            @if(isset($feature['title']))
                                                <span class="feature-badge" style="display: inline-block; background: #f3f4f6; color: #6b7280; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">
                                                    <i class="fas fa-check-circle me-2" style="color: var(--brand-primary);"></i>{{ $feature['title'] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Share Section --}}
                            <div class="service-footer mt-5 pt-4" style="border-top: 1px solid #e5e7eb;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mb-3" style="font-size: 1rem; font-weight: 600; color: #1a1a1a;">{{ __('Share') }}:</h5>
                                        @php
                                            $site = \App\Models\SiteSetting::getSettings();
                                            $socialLinks = isset($site->social_links) && is_array($site->social_links) ? $site->social_links : [];
                                            $shareUrl = route('public.services.show', $service->slug);
                                        @endphp
                                        <div class="social-share" style="display: flex; gap: 0.75rem;">
                                            @if(isset($socialLinks['facebook']) && $socialLinks['facebook'])
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #1877f2; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['twitter']) && $socialLinks['twitter'])
                                                <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($serviceTitle) }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #1da1f2; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['linkedin']) && $socialLinks['linkedin'])
                                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #0077b5; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['instagram']) && $socialLinks['instagram'])
                                                <a href="{{ $socialLinks['instagram'] }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #e4405f; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <div class="service-sidebar">
                        {{-- Search Widget --}}
                        <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                            <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Search Services') }}</h3>
                            <form action="{{ route('public.services.index') }}" method="GET">
                                <div class="input-group" style="display: flex; gap: 0;">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Enter Keyword') }}" value="{{ request('search') }}" style="border: 1px solid #e5e7eb; border-radius: 6px 0 0 6px; padding: 12px 15px; font-size: 0.95rem;">
                                    <button type="submit" class="btn-search" style="background: var(--brand-primary); color: #ffffff; border: none; border-radius: 0 6px 6px 0; padding: 12px 20px; cursor: pointer;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- All Services Widget --}}
                        @php
                            $allServices = class_exists(\App\Models\Service::class)
                                ? \App\Models\Service::active()
                                    ->ordered()
                                    ->limit(10)
                                    ->get()
                                : collect();
                        @endphp
                        @if($allServices->count() > 0)
                            <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Our Services') }}</h3>
                                <ul class="services-list" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach($allServices as $s)
                                        @php
                                            $sTitle = is_array($s->title ?? '') ? ($s->title[$currentLocale] ?? $s->title['en'] ?? '') : ($s->title ?? __('Service'));
                                        @endphp
                                        <li style="padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                            <a href="{{ route('public.services.show', $s->slug) }}" style="display: flex; align-items: center; justify-content: space-between; color: {{ $s->slug === $service->slug ? 'var(--brand-primary)' : '#6b7280' }}; text-decoration: none; font-weight: {{ $s->slug === $service->slug ? '600' : '400' }};">
                                                <span>{{ $sTitle }}</span>
                                                <i class="fas fa-chevron-right" style="font-size: 0.75rem; color: var(--brand-primary);"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        {{-- Related Services Widget --}}
                        @if($related->count() > 0)
                            <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Related Services') }}</h3>
                                <ul class="recent-services-list" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach($related->take(3) as $relatedService)
                                        @php
                                            $relatedTitle = is_array($relatedService->title ?? '') ? ($relatedService->title[$currentLocale] ?? $relatedService->title['en'] ?? '') : ($relatedService->title ?? __('Service'));
                                            $relatedDate = $relatedService->created_at->format('d M, Y');
                                        @endphp
                                        <li style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #f3f4f6;">
                                            @if($relatedService->image_path)
                                                <div style="width: 80px; height: 80px; flex-shrink: 0; border-radius: 8px; overflow: hidden;">
                                                    <a href="{{ route('public.services.show', $relatedService->slug) }}">
                                                        <img src="{{ asset('storage/'.$relatedService->image_path) }}" alt="{{ $relatedTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </a>
                                                </div>
                                            @endif
                                            <div style="flex: 1;">
                                                <div class="mb-1" style="font-size: 0.85rem; color: #6b7280;">
                                                    <i class="fas fa-clock me-1"></i>{{ $relatedDate }}
                                                </div>
                                                <h5 style="font-size: 0.95rem; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0;">
                                                    <a href="{{ route('public.services.show', $relatedService->slug) }}" style="color: inherit; text-decoration: none;">
                                                        {{ Str::limit($relatedTitle, 60) }}
                                                    </a>
                                                </h5>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        {{-- Contact Banner Widget --}}
                        <div class="sidebar-widget" style="background: linear-gradient(135deg, var(--theme-primary-color, #1B4332) 0%, var(--gradient-end-color, #2D5016) 100%); border-radius: 12px; padding: 2.5rem; text-align: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                            <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin-bottom: 1rem;">{{ __('Need Help?') }}</h3>
                            <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 1.5rem; font-size: 1rem;">{{ __('We Are Here To Help You') }}</p>
                            <a href="{{ route('public.contact') }}" class="btn-contact" style="display: inline-block; background: var(--theme-secondary-color, #D4AF37); color: #ffffff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;">
                                {{ __('Contact Us') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        .social-icon:hover {
            transform: translateY(-3px);
        }
        
        .btn-search:hover {
            background: var(--gradient-end-color, #2D5016) !important;
        }
        
        .services-list li a:hover {
            color: var(--brand-primary) !important;
        }
        
        .feature-badge {
            transition: all 0.3s ease;
        }
        
        .feature-badge:hover {
            background: var(--brand-primary) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }
        
        .btn-contact:hover {
            background: #b8860b !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        
        .work-step {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .work-step:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
