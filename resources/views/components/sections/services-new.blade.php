@props(['section' => null, 'services' => collect()])

@php
    // Helper function to get localized value
    if (!function_exists('getLocalizedValue')) {
        function getLocalizedValue($value, $locale = null) {
            if (empty($value)) return '';
            $locale = $locale ?? app()->getLocale();
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? ($value[array_key_first($value)] ?? '');
            }
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    return $decoded[$locale] ?? $decoded['en'] ?? ($decoded[array_key_first($decoded)] ?? '');
                }
                return $value;
            }
            return '';
        }
    }

    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displayServices = $items->count() > 0 ? $items : ($services ?? collect());
    
    // Get localized values
    $title = getLocalizedValue($section->title ?? '') ?: __('Our Services');
    $subtitle = getLocalizedValue($section->subtitle ?? '');
    
    // Get action_text and action_link from data JSON
    $sectionData = isset($section->data) && is_array($section->data) ? $section->data : [];
    $actionText = getLocalizedValue($sectionData['action_text'] ?? '');
    $actionLink = $sectionData['action_link'] ?? route('public.contact');
@endphp

@php
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = '';
    if ($isTranslatable) {
        $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
            ? $currentTranslation->title 
            : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : '');
    }
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Our Services');
    }
    
    $subtitle = '';
    if ($isTranslatable) {
        $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
            ? $currentTranslation->subtitle 
            : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : '');
    }
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

<!-- services area start  -->
<section class="section services-section" >
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="section-title" style="font-size: 2.5rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 1rem; text-align: center;">{{ $title }}</h2>
                @if($subtitle)
                    <p class="section-subtitle" style="font-size: 1.2rem; color: #6c757d; text-align: center; margin-bottom: 3rem;">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        <div class="row">
            @if($displayServices->count() > 0)
                @foreach($displayServices->take(6) as $index => $service)
                    @php
                        $serviceTitle = '';
                        if (is_object($service) && method_exists($service, 'translate')) {
                            $serviceCurrent = $service->translate($currentLocale);
                            $serviceEn = $service->translate('en');
                            $serviceTitle = ($serviceCurrent && isset($serviceCurrent->title) && $serviceCurrent->title) 
                                ? $serviceCurrent->title 
                                : (($serviceEn && isset($serviceEn->title) && $serviceEn->title) ? $serviceEn->title : '');
                        }
                        if (empty($serviceTitle)) {
                            $serviceTitle = is_array($service->title ?? '') ? ($service->title[$currentLocale] ?? $service->title['en'] ?? '') : ($service->title ?? __('Service'));
                        }
                        
                        $serviceDesc = '';
                        if (is_object($service) && method_exists($service, 'translate')) {
                            $serviceCurrent = $service->translate($currentLocale);
                            $serviceEn = $service->translate('en');
                            $serviceDesc = ($serviceCurrent && isset($serviceCurrent->short_description) && $serviceCurrent->short_description) 
                                ? $serviceCurrent->short_description 
                                : (($serviceEn && isset($serviceEn->short_description) && $serviceEn->short_description) ? $serviceEn->short_description : '');
                        }
                        if (empty($serviceDesc)) {
                            $serviceDesc = is_array($service->short_description ?? '') ? ($service->short_description[$currentLocale] ?? $service->short_description['en'] ?? '') : ($service->short_description ?? $service->description ?? '');
                        }
                        
                        $serviceImage = $service->image_path ?? null;
                        $serviceLink = $service->slug ? route('public.services.show', $service->slug) : ($service->link ?? route('public.services.index'));
                    @endphp
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="{{ $serviceLink }}" class="text-decoration-none">
                            <div class="service-card-modern" style="background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                                {{-- Image Section --}}
                                <div class="service-image-wrapper" style="position: relative; width: 100%; height: 250px; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    @if($serviceImage)
                                        <img src="{{ asset('storage/'.$serviceImage) }}" alt="{{ $serviceTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @elseif($service->icon && !str_contains($service->icon, 'img') && !str_contains($service->icon, 'image'))
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                            <i class="{{ $service->icon }}" style="font-size: 4rem; color: rgba(255, 255, 255, 0.9);"></i>
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                            <i class="fas fa-star" style="font-size: 4rem; color: rgba(255, 255, 255, 0.9);"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Content Overlay --}}
                                <div class="service-content-overlay" style="background: #ffffff; padding: 1.75rem; position: relative; flex-grow: 1; border-radius: 20px 20px 0 0; margin-top: -20px; z-index: 2;">
                                    <h4 class="service-title-modern" style="font-size: 1.35rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.75rem; line-height: 1.3;">
                                        {{ $serviceTitle }}
                                    </h4>
                                    <p class="service-description-modern" style="color: #6b7280; font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                                        {{ Str::limit(strip_tags($serviceDesc), 80) }}
                                    </p>
                                    
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
        {{-- @if($actionText)
            <div class="text-center mt-5">
                <a href="{{ $actionLink }}" class="btn btn-primary-custom" style="background: var(--brand-primary); border: none; padding: 0.75rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px; color: #ffffff; text-decoration: none; transition: all 0.3s ease;">
                    {{ $actionText }}
                </a>
            </div>
        @endif --}}
    </div>
</section>
<!-- services area end  -->

<style>
.service-card-modern {
    cursor: pointer;
}

.service-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.service-card-modern:hover .service-image-wrapper img {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.service-card-modern:hover .service-arrow-icon {
    background: #f59e0b;
    transform: scale(1.1);
}

.service-arrow-icon {
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    background: #3a9a32 !important;
    transform: translateY(-3px);
}
</style>

