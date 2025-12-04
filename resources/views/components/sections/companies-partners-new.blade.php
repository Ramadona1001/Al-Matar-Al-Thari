@props(['section' => null, 'partners' => collect()])

@php
    $section = $section ?? (object)[];
    $displayPartners = $partners->count() > 0 ? $partners : collect();
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Our Partners'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Our Partners');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : '');
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

@if($displayPartners->count() > 0)
    <section class="section" style="padding: 80px 0; background: #ffffff;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $title }}</h2>
                    @if($subtitle)
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="row align-items-center">
                @foreach($displayPartners as $partner)
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <div class="partner-card text-center" style="background: #ffffff; padding: 1.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; align-items: center; justify-content: center;">
                            @php
                                $partnerName = '';
                                if (is_object($partner) && method_exists($partner, 'translate')) {
                                    $partnerCurrent = $partner->translate($currentLocale);
                                    $partnerEn = $partner->translate('en');
                                    $partnerName = ($partnerCurrent && isset($partnerCurrent->name) && $partnerCurrent->name) 
                                        ? $partnerCurrent->name 
                                        : (($partnerEn && isset($partnerEn->name) && $partnerEn->name) ? $partnerEn->name : '');
                                }
                                if (empty($partnerName)) {
                                    $partnerName = $partner->name ?? '';
                                }
                            @endphp
                            @if(isset($partner->logo_path) && $partner->logo_path)
                                <a href="{{ $partner->website_url ?? '#' }}" target="_blank" style="display: block;">
                                    <img src="{{ asset('storage/'.$partner->logo_path) }}" alt="{{ $partnerName }}" style="max-width: 100%; height: auto; max-height: 80px; object-fit: contain; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s ease;">
                                </a>
                            @elseif($partnerName)
                                <a href="{{ $partner->website_url ?? '#' }}" target="_blank" style="color: #3D4F60; text-decoration: none; font-weight: 600;">
                                    {{ $partnerName }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<style>
.partner-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
.partner-card:hover img {
    filter: grayscale(0%) !important;
    opacity: 1 !important;
}
</style>
