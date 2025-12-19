@props(['section' => null, 'banners' => collect(), 'services' => collect(), 'testimonials' => collect(), 'statistics' => collect(), 'steps' => collect(), 'partners' => collect(), 'blogs' => collect(), 'offers' => collect(), 'companies' => collect(), 'faqs' => collect()])

@php
    if (!$section) {
        return;
    }
    // Ensure section is an object
    $section = is_object($section) ? $section : (object)$section;
    $componentName = isset($section->component_name) ? $section->component_name : (isset($section->type) ? $section->type : 'content');
    
    // If type is 'content' and page is 'about', treat it as 'about'
    if ($componentName === 'content' && isset($section->page) && $section->page === 'about') {
        $componentName = 'about';
    }
    
    $isVisible = isset($section->is_visible) ? $section->is_visible : true;
@endphp

@if($isVisible)
    <div class="website-section">
        @switch($componentName)
            @case('hero')
            @case('banner')
                <x-sections.hero-new :section="$section" :banners="$banners" />
                @break
            @case('about')
                <x-sections.about-new :section="$section" />
                @break
            @case('services')
                <x-sections.services-new :section="$section" :services="$services" />
                @break
            @case('features')
            @case('why-choose-us')
                <x-sections.features :section="$section" />
                @break
            @case('statistics')
            @case('counters')
                <x-sections.statistics-new :section="$section" :statistics="$statistics" />
                @break
            @case('testimonials')
                <x-sections.testimonials-new :section="$section" :testimonials="$testimonials" />
                @break
            @case('how-it-works')
                <x-sections.how-it-works-new :section="$section" :steps="$steps" />
                @break
            @case('companies')
            @case('partners')
                <x-sections.companies-partners-new :section="$section" :partners="$partners" />
                @break
            @case('blogs')
            @case('news')
                <x-sections.blogs-new :section="$section" :blogs="$blogs" />
                @break
            @case('portfolio')
            @case('gallery')
                <x-sections.portfolio-new :section="$section" :items="$section->activeItems ?? collect()" />
                @break
            @case('pricing-cta')
            @case('pricing_cta')
            @case('quote')
                <x-sections.pricing-cta-new :section="$section" :services="$services" />
                @break
            @case('faq')
                @php
                    $faqItems = $faqs->count() > 0 ? $faqs : ($section->activeItems ?? collect());
                @endphp
                <x-sections.faq-new :section="$section" :items="$faqItems" />
                @break
            @case('newsletter')
                <x-sections.newsletter-new :section="$section" />
                @break
            @case('cta')
                <x-sections.cta :section="$section" />
                @break
            @case('contact')
                <x-sections.contact :section="$section" />
                @break
            @case('system-explanation')
            @case('system')
            @case('what-is')
                <x-sections.system-explanation-new :section="$section" />
                @break
            @case('offers')
            @case('featured-offers')
                <x-sections.offers-new :section="$section" :offers="$offers" />
                @break
            @default
                <x-sections.content :section="$section" />
        @endswitch
    </div>
@endif

