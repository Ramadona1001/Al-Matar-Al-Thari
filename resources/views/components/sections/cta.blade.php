@props(['section' => null])

@php
    $section = $section ?? (object)[];
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Ready to Get Started?'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Ready to Get Started?');
    }
    
    $description = ($currentTranslation && isset($currentTranslation->content) && $currentTranslation->content) 
        ? $currentTranslation->content 
        : (($enTranslation && isset($enTranslation->content) && $enTranslation->content) ? $enTranslation->content : '');
    if (empty($description) && isset($section->subtitle)) {
        $description = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
    if (empty($description)) {
        $description = __('Join thousands of satisfied customers and merchants');
    }
    
    $sectionData = isset($section->data) && is_array($section->data) ? $section->data : [];
    $buttonText = $sectionData['button_text'] ?? __('Join Now');
    $buttonLink = $sectionData['button_link'] ?? route('register', ['type' => 'customer']);
    $secondaryButtonText = $sectionData['secondary_button_text'] ?? __('Contact Us');
    $secondaryButtonLink = $sectionData['secondary_button_link'] ?? route('public.contact');
@endphp

<section class="cta-section" style="background: linear-gradient(135deg, #3D4F60 0%, #17A2B8 100%); color: #ffffff; padding: 80px 0; text-align: center;">
    <div class="container">
        <h2 class="cta-title" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">{{ $title }}</h2>
        <p class="cta-description" style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">{{ $description }}</p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ $buttonLink }}" style="background: #4BB543; border: none; padding: 0.75rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px; color: #ffffff; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                {{ $buttonText }}
            </a>
            <a href="{{ $secondaryButtonLink }}" style="background: rgba(255, 255, 255, 0.2); border: 2px solid rgba(255, 255, 255, 0.5); padding: 0.75rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px; color: #ffffff; text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                {{ $secondaryButtonText }}
            </a>
        </div>
    </div>
</section>

<style>
.cta-section a:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
.cta-section a:first-child:hover {
    background: #3a9a32 !important;
}
.cta-section a:last-child:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    border-color: rgba(255, 255, 255, 0.8) !important;
}
</style>
