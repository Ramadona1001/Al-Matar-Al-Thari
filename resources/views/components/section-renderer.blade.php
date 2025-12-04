@props(['section' => null, 'banners' => collect(), 'services' => collect(), 'testimonials' => collect(), 'statistics' => collect(), 'steps' => collect(), 'partners' => collect(), 'blogs' => collect()])

@php
    if (!$section) {
        return;
    }
    // Ensure section is an object
    $section = is_object($section) ? $section : (object)$section;
    $componentName = isset($section->component_name) ? $section->component_name : (isset($section->type) ? $section->type : 'content');
    $isVisible = isset($section->is_visible) ? $section->is_visible : true;
@endphp

@if($isVisible)
    @switch($componentName)
        @case('hero')
            <x-sections.hero :section="$section" :banners="$banners" />
            @break
        @case('about')
            <x-sections.about :section="$section" />
            @break
        @case('services')
            <x-sections.services :section="$section" :services="$services" />
            @break
        @case('features')
        @case('why-choose-us')
            <x-sections.features :section="$section" />
            @break
        @case('statistics')
        @case('counters')
            <x-sections.statistics :section="$section" :statistics="$statistics" />
            @break
        @case('testimonials')
            <x-sections.testimonials :section="$section" :testimonials="$testimonials" />
            @break
        @case('how-it-works')
            <x-sections.how-it-works :section="$section" :steps="$steps" />
            @break
        @case('companies')
        @case('partners')
            <x-sections.companies-partners :section="$section" :partners="$partners" />
            @break
        @case('blogs')
        @case('news')
            <x-sections.blogs :section="$section" :blogs="$blogs" />
            @break
        @case('cta')
            <x-sections.cta :section="$section" />
            @break
        @case('contact')
            <x-sections.contact :section="$section" />
            @break
        @default
            <x-sections.content :section="$section" />
    @endswitch
@endif
