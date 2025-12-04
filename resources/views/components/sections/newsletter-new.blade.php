@props(['section' => null])

@php
    $section = $section ?? (object)[];
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Get Weekly Newsletter & Offers'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Get Weekly Newsletter & Offers');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : __('Stay Updated'));
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
    if (empty($subtitle)) {
        $subtitle = __('Stay Updated');
    }
    
    $sectionData = isset($section->data) && is_array($section->data) ? $section->data : [];
    $formAction = $sectionData['form_action'] ?? route('public.newsletter.subscribe');
    $buttonText = $sectionData['button_text'] ?? __('Subscribe');
    
    // Check if newsletter section setting exists
    $newsletterSectionSetting = class_exists(\App\Models\SectionSetting::class)
        ? \App\Models\SectionSetting::getByKey('newsletter_section')
        : null;
    
    if ($newsletterSectionSetting) {
        $currentTranslation = $newsletterSectionSetting->translate($currentLocale);
        $enTranslation = $newsletterSectionSetting->translate('en');
        $title = ($currentTranslation && $currentTranslation->title) 
            ? $currentTranslation->title 
            : (($enTranslation && $enTranslation->title) ? $enTranslation->title : $title);
        $subtitle = ($currentTranslation && $currentTranslation->subtitle) 
            ? $currentTranslation->subtitle 
            : (($enTranslation && $enTranslation->subtitle) ? $enTranslation->subtitle : $subtitle);
    }
@endphp

{{-- <section class="section" style="padding: 80px 0; background: linear-gradient(135deg, #3D4F60 0%, #17A2B8 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div style="color: #ffffff;">
                    @if($subtitle)
                        <p style="font-size: 1.1rem; margin-bottom: 1rem; opacity: 0.9;">{{ $subtitle }}</p>
                    @endif
                    <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0; color: #ffffff;">{{ $title }}</h2>
                </div>
            </div>
            <div class="col-lg-6">
                <form action="{{ $formAction }}" method="POST" style="display: flex; gap: 0.5rem;">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="{{ __('Enter your email address') }}" 
                           required
                           style="flex: 1; padding: 0.75rem 1.5rem; border: none; border-radius: 50px; font-size: 1rem; outline: none;">
                    <button type="submit" 
                            style="background: #4BB543; border: none; padding: 0.75rem 2rem; font-size: 1rem; font-weight: 600; border-radius: 50px; color: #ffffff; white-space: nowrap; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                        {{ $buttonText }}
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section> --}}

<style>
.newsletter-section button:hover {
    background: #3a9a32 !important;
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
}
.newsletter-section input:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
}
@media (max-width: 991px) {
    .newsletter-section form {
        flex-direction: column;
    }
    .newsletter-section button {
        width: 100%;
    }
}
</style>
