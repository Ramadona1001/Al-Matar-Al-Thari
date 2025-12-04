@extends('layouts.new-design')

@section('meta_title', __('Home'))
@section('meta_description', __('Discover great offers from top companies'))

@section('content')
    <!-- Dynamic Sections from CMS -->
    @if(isset($sections) && $sections->count() > 0)
        @foreach($sections as $section)
            <x-section-renderer-new 
                :section="$section" 
                :banners="$banners ?? collect()" 
                :services="$services ?? collect()" 
                :testimonials="$testimonials ?? collect()" 
                :statistics="$statistics ?? collect()"
                :steps="$steps ?? collect()"
                :partners="$partners ?? collect()"
                :blogs="$blogs ?? collect()"
            />
        @endforeach
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
@endsection

