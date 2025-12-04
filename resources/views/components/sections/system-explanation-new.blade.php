@props(['section' => null])

@php
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
    $title = getLocalizedValue($section->title ?? '') ?: __('What is Al-Matar Al-Thari?');
    $subtitle = getLocalizedValue($section->subtitle ?? '');
    $content = getLocalizedValue($section->content ?? '');
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    
    // Default items if none provided
    if ($items->count() === 0) {
        $items = collect([
            (object)['title' => __('For Customers'), 'content' => __('Earn loyalty points, get exclusive discounts, and enjoy a rewarding shopping experience.')],
            (object)['title' => __('For Merchants'), 'content' => __('Attract customers, manage offers, track analytics, and grow your business.')],
            (object)['title' => __('For Affiliates'), 'content' => __('Build your network, refer customers, and earn commissions through our affiliate program.')],
        ]);
    }
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
        $title = __('What is Al-Matar Al-Thari?');
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
    if (empty($subtitle) && isset($section->content)) {
        $content = $isTranslatable ? (($currentTranslation && isset($currentTranslation->content) && $currentTranslation->content) ? $currentTranslation->content : (($enTranslation && isset($enTranslation->content) && $enTranslation->content) ? $enTranslation->content : '')) : '';
        if (empty($content)) {
            $content = is_string($section->content) ? $section->content : (is_array($section->content) ? ($section->content[$currentLocale] ?? $section->content['en'] ?? '') : '');
        }
        if (!empty($content)) {
            $subtitle = $content;
        }
    }
    if (empty($subtitle)) {
        $subtitle = __('A revolutionary digital platform that connects customers, merchants, and affiliates in a seamless ecosystem of rewards, discounts, and marketing opportunities.');
    }
@endphp

<!-- System Explanation Section -->
<section id="system" class="section system-section" style="background: #f8f9fa; padding: 80px 0;" aria-labelledby="system-heading">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="section-title" id="system-heading" style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $title }}</h2>
                <p class="section-subtitle" style="font-size: 1.2rem; color: #6c757d; margin-bottom: 3rem;">{{ $subtitle }}</p>
            </div>
        </div>
        <div class="row">
            @foreach($items as $index => $item)
                <div class="col-md-4 mb-4">
                    <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%;">
                        <h4 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">
                            @php
                                $itemTitle = '';
                                if (is_object($item) && method_exists($item, 'translate')) {
                                    $itemCurrent = $item->translate($currentLocale);
                                    $itemEn = $item->translate('en');
                                    $itemTitle = ($itemCurrent && isset($itemCurrent->title) && $itemCurrent->title) 
                                        ? $itemCurrent->title 
                                        : (($itemEn && isset($itemEn->title) && $itemEn->title) ? $itemEn->title : '');
                                }
                                if (empty($itemTitle)) {
                                    $itemTitle = $item->title ?? $item->name ?? '';
                                }
                            @endphp
                            {{ $itemTitle }}
                        </h4>
                        <p style="color: #6c757d; line-height: 1.6; margin: 0;">
                            @php
                                $itemContent = '';
                                if (is_object($item) && method_exists($item, 'translate')) {
                                    $itemCurrent = $item->translate($currentLocale);
                                    $itemEn = $item->translate('en');
                                    $itemContent = ($itemCurrent && isset($itemCurrent->content) && $itemCurrent->content) 
                                        ? $itemCurrent->content 
                                        : (($itemEn && isset($itemEn->content) && $itemEn->content) ? $itemEn->content : '');
                                }
                                if (empty($itemContent)) {
                                    $itemContent = $item->content ?? $item->description ?? '';
                                }
                            @endphp
                            {{ $itemContent }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.system-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
</style>

