@props(['section' => null, 'items' => collect()])

@php
    $section = $section ?? (object)[];
    $displayItems = isset($section->activeItems) ? $section->activeItems : ($items ?? collect());
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Watch Our Gallery'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Watch Our Gallery');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : __('Showcase'));
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

@if($displayItems->count() > 0)
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
            <div class="row g-4">
                @foreach($displayItems as $index => $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="gallery-item" style="position: relative; overflow: hidden; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                            @php
                                $itemTitle = '';
                                $itemSubtitle = '';
                                if (is_object($item) && method_exists($item, 'translate')) {
                                    $itemCurrent = $item->translate($currentLocale);
                                    $itemEn = $item->translate('en');
                                    $itemTitle = ($itemCurrent && isset($itemCurrent->title) && $itemCurrent->title) 
                                        ? $itemCurrent->title 
                                        : (($itemEn && isset($itemEn->title) && $itemEn->title) ? $itemEn->title : '');
                                    $itemSubtitle = ($itemCurrent && isset($itemCurrent->subtitle) && $itemCurrent->subtitle) 
                                        ? $itemCurrent->subtitle 
                                        : (($itemEn && isset($itemEn->subtitle) && $itemEn->subtitle) ? $itemEn->subtitle : '');
                                }
                                if (empty($itemTitle)) {
                                    $itemTitle = $item->title ?? __('Portfolio Item');
                                }
                                if (empty($itemSubtitle)) {
                                    $itemSubtitle = $item->subtitle ?? __('Portfolio');
                                }
                            @endphp
                            <a href="{{ $item->link ?? '#' }}" style="display: block; position: relative;">
                                <img src="{{ isset($item->image_path) && $item->image_path ? asset('storage/'.$item->image_path) : asset('assets/img/portfolio/portfolio-'.(14+$index).'.jpg') }}" 
                                     alt="{{ $itemTitle }}" 
                                     style="width: 100%; height: 300px; object-fit: cover; transition: all 0.3s ease;">
                                <div class="gallery-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent 0%, rgba(61, 79, 96, 0.9) 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 1.5rem; opacity: 0; transition: all 0.3s ease;">
                                    <span style="color: #4BB543; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $itemSubtitle }}</span>
                                    <h4 style="color: #ffffff; font-size: 1.3rem; font-weight: 600; margin: 0;">{{ $itemTitle }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<style>
.gallery-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
.gallery-item:hover .gallery-overlay {
    opacity: 1;
}
.gallery-item:hover img {
    transform: scale(1.1);
}
</style>
