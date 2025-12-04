@extends('layouts.new-design')

@section('meta_title', __('Home'))
@section('meta_description', __('Discover great offers from top companies'))

@section('content')
    <!-- Dynamic Sections from CMS -->
    @php
        if (isset($sections) && $sections->count() > 0) {
            // Group sections by rows based on builder_data
            $rows = [];
            foreach ($sections as $section) {
                $builderData = $section->builder_data ?? ['row' => 0, 'column' => 0, 'width' => 12];
                $row = $builderData['row'] ?? 0;
                if (!isset($rows[$row])) {
                    $rows[$row] = [];
                }
                $rows[$row][] = $section;
            }
            
            // Sort sections within each row by column
            foreach ($rows as $rowIndex => $rowSections) {
                usort($rows[$rowIndex], function($a, $b) {
                    $aCol = ($a->builder_data['column'] ?? 0);
                    $bCol = ($b->builder_data['column'] ?? 0);
                    return $aCol <=> $bCol;
                });
            }
            
            ksort($rows);
        } else {
            $rows = [];
        }
    @endphp
    
    @if(!empty($rows))
        @foreach($rows as $rowIndex => $rowSections)
            @php
                // Separate hero sections from regular sections
                $heroSections = [];
                $regularSections = [];
                foreach ($rowSections as $section) {
                    $sectionType = $section->type ?? $section->component_name ?? 'content';
                    if (in_array($sectionType, ['hero', 'banner'])) {
                        $heroSections[] = $section;
                    } else {
                        $regularSections[] = $section;
                    }
                }
            @endphp
            
            {{-- Hero sections: Full width without container --}}
            @foreach($heroSections as $section)
                <x-section-renderer-new 
                    :section="$section" 
                    :banners="$banners ?? collect()" 
                    :services="$services ?? collect()" 
                    :testimonials="$testimonials ?? collect()" 
                    :statistics="$statistics ?? collect()"
                    :steps="$steps ?? collect()"
                    :partners="$partners ?? collect()"
                    :blogs="$blogs ?? collect()"
                    :offers="$offers ?? collect()"
                    :companies="$companies ?? collect()"
                    :faqs="$faqs ?? collect()"
                />
            @endforeach
            
            {{-- Regular sections: Inside container --}}
            @if(!empty($regularSections))
                <div class="container">
                    <div class="row g-4 mb-4">
                        @foreach($regularSections as $section)
                            @php
                                $builderData = $section->builder_data ?? ['width' => 12];
                                $width = $builderData['width'] ?? 12;
                                $colClass = match($width) {
                                    1 => 'col-12 col-md-1',
                                    2 => 'col-12 col-md-2',
                                    3 => 'col-12 col-md-3',
                                    4 => 'col-12 col-md-4',
                                    5 => 'col-12 col-md-5',
                                    6 => 'col-12 col-md-6',
                                    7 => 'col-12 col-md-7',
                                    8 => 'col-12 col-md-8',
                                    9 => 'col-12 col-md-9',
                                    10 => 'col-12 col-md-10',
                                    11 => 'col-12 col-md-11',
                                    12 => 'col-12',
                                    default => 'col-12'
                                };
                            @endphp
                            <div class="{{ $colClass }}">
                                <x-section-renderer-new 
                                    :section="$section" 
                                    :banners="$banners ?? collect()" 
                                    :services="$services ?? collect()" 
                                    :testimonials="$testimonials ?? collect()" 
                                    :statistics="$statistics ?? collect()"
                                    :steps="$steps ?? collect()"
                                    :partners="$partners ?? collect()"
                                    :blogs="$blogs ?? collect()"
                                    :offers="$offers ?? collect()"
                                    :companies="$companies ?? collect()"
                                    :faqs="$faqs ?? collect()"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        
        {{-- Check if statistics and testimonials sections exist in CMS, if not, show default --}}
        @php
            $hasStatisticsSection = false;
            $hasTestimonialsSection = false;
            if (isset($sections) && $sections->count() > 0) {
                foreach ($sections as $section) {
                    $sectionType = $section->type ?? $section->component_name ?? '';
                    if (in_array($sectionType, ['statistics', 'counters'])) {
                        $hasStatisticsSection = true;
                    }
                    if (in_array($sectionType, ['testimonials', 'reviews'])) {
                        $hasTestimonialsSection = true;
                    }
                }
            }
        @endphp
        
        {{-- Show statistics if not already shown in CMS sections --}}
        @if(!$hasStatisticsSection && isset($statistics) && $statistics->count() > 0)
            <x-sections.statistics-new 
                :section="(object)['title' => __('Our Impact'), 'subtitle' => __('By The Numbers'), 'activeItems' => collect()]" 
                :statistics="$statistics" 
            />
        @endif
        
        {{-- Show testimonials if not already shown in CMS sections --}}
        @if(!$hasTestimonialsSection && isset($testimonials) && $testimonials->count() > 0)
            <x-sections.testimonials-new 
                :section="(object)['title' => __('What Our Clients Say'), 'subtitle' => __('Testimonials'), 'activeItems' => collect()]" 
                :testimonials="$testimonials" 
            />
        @endif
    @else
        <!-- Default Sections (when no CMS content exists) -->
        
        <!-- Hero/Banner Section -->
        <x-sections.hero-new 
            :section="(object)['title' => __('Transform Your Business with Innovation'), 'subtitle' => __('Welcome'), 'content' => __('The end-to-end solution built to simplify complexity and accelerate transformation.'), 'button_text' => __('Let\'s Start'), 'button_link' => route('register', ['type' => 'customer'])]" 
            :banners="collect()" 
        />

        <!-- Services Section -->
        <x-sections.services-new 
            :section="(object)['title' => __('Our Services'), 'subtitle' => __('What We Offer'), 'activeItems' => collect()]" 
            :services="$services ?? collect()" 
        />

        <!-- About Section -->
        <x-sections.about-new 
            :section="(object)['title' => __('About Us'), 'subtitle' => __('Who We Are'), 'content' => __('We are a leading platform built to simplify complexity and accelerate transformation.'), 'activeItems' => collect()]" 
        />

        <!-- How It Works Section -->
        @if(isset($steps) && $steps->count() > 0)
            <x-sections.how-it-works-new 
                :section="(object)['title' => __('How It Works'), 'subtitle' => __('Simple Steps to Get Started'), 'activeItems' => collect()]" 
                :steps="$steps" 
            />
        @endif

        <!-- Statistics Section -->
        @if(isset($statistics) && $statistics->count() > 0)
            <x-sections.statistics-new 
                :section="(object)['title' => __('Our Impact'), 'subtitle' => __('By The Numbers'), 'activeItems' => collect()]" 
                :statistics="$statistics" 
            />
        @endif

        <!-- Testimonials Section -->
        @if(isset($testimonials) && $testimonials->count() > 0)
            <x-sections.testimonials-new 
                :section="(object)['title' => __('What Our Clients Say'), 'subtitle' => __('Testimonials'), 'activeItems' => collect()]" 
                :testimonials="$testimonials" 
            />
        @endif

        <!-- Companies/Partners Section -->
        @if(isset($partners) && $partners->count() > 0)
            <x-sections.companies-partners-new 
                :section="(object)['title' => __('Our Partners'), 'subtitle' => __('Trusted by Leading Companies'), 'activeItems' => collect()]" 
                :partners="$partners" 
            />
        @endif

        <!-- Latest News/Blogs Section -->
        @if(isset($blogs) && $blogs->count() > 0)
            <x-sections.blogs-new 
                :section="(object)['title' => __('Latest News'), 'subtitle' => __('Stay Updated'), 'activeItems' => collect()]" 
                :blogs="$blogs" 
            />
        @endif
    @endif

    <!-- Legacy Content (if exists and no CMS sections) -->
    @if((!isset($sections) || $sections->count() === 0) && isset($homepageCards) && $homepageCards->count() > 0)
        <section class="py-16 md:py-24 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 fade-in-up" data-animate="fade-in-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Loyalty Cards') }}</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">{{ __('Explore our featured loyalty programs') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($homepageCards as $index => $card)
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden fade-in-up" 
                             data-animate="fade-in-up" 
                             data-delay="{{ $index * 0.1 }}">
                            @if($card->image_path)
                                <img src="{{ asset('storage/' . $card->image_path) }}" 
                                     alt="{{ $card->title }}" 
                                     class="w-full h-48 object-cover">
                            @endif
                            <div class="p-6">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="h-10 w-10 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                        <i class="fas fa-id-card text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $card->title }}</h3>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">{{ Str::limit($card->description, 90) }}</p>
                                <a href="{{ route('public.cards.show', ['slug' => $card->slug]) }}" 
                                   class="block w-full text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition-colors">
                                    {{ __('Details') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
