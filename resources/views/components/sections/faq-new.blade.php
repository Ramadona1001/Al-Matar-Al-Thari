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
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Frequently Asked Questions'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Frequently Asked Questions');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : __('FAQ'));
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
    
    $uniqueId = 'faq-' . uniqid();
@endphp

@if($displayItems->count() > 0)
    <section class="section" style="padding: 80px 0; background: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $title }}</h2>
                    @if($subtitle)
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="accordionExample-{{ $uniqueId }}">
                        @foreach($displayItems as $index => $item)
                            @php
                                // Check if item is a Faq model
                                $isFaqModel = is_object($item) && get_class($item) === 'App\Models\Faq';
                                
                                if ($isFaqModel) {
                                    $itemCurrent = $item->translate($currentLocale);
                                    $itemEn = $item->translate('en');
                                    $itemTitle = ($itemCurrent && isset($itemCurrent->question) && $itemCurrent->question) 
                                        ? $itemCurrent->question 
                                        : (($itemEn && isset($itemEn->question) && $itemEn->question) ? $itemEn->question : __('Question'));
                                    $itemContent = ($itemCurrent && isset($itemCurrent->answer) && $itemCurrent->answer) 
                                        ? $itemCurrent->answer 
                                        : (($itemEn && isset($itemEn->answer) && $itemEn->answer) ? $itemEn->answer : '');
                                } else {
                                    $itemTitle = '';
                                    if (is_object($item) && method_exists($item, 'translate')) {
                                        $itemCurrent = $item->translate($currentLocale);
                                        $itemEn = $item->translate('en');
                                        $itemTitle = ($itemCurrent && isset($itemCurrent->title) && $itemCurrent->title) 
                                            ? $itemCurrent->title 
                                            : (($itemEn && isset($itemEn->title) && $itemEn->title) ? $itemEn->title : '');
                                    }
                                    if (empty($itemTitle)) {
                                        $itemTitle = $item->title ?? __('Question');
                                    }
                                    
                                    $itemContent = '';
                                    if (is_object($item) && method_exists($item, 'translate')) {
                                        $itemCurrent = $item->translate($currentLocale);
                                        $itemEn = $item->translate('en');
                                        $itemContent = ($itemCurrent && isset($itemCurrent->content) && $itemCurrent->content) 
                                            ? $itemCurrent->content 
                                            : (($itemEn && isset($itemEn->content) && $itemEn->content) ? $itemEn->content : '');
                                    }
                                    if (empty($itemContent)) {
                                        $itemContent = $item->content ?? $item->description ?? __('Answer content goes here.');
                                    }
                                }
                            @endphp
                            <div class="accordion-item mb-3" style="background: #ffffff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border: none; overflow: hidden;">
                                <h2 class="accordion-header" id="heading{{ $index }}-{{ $uniqueId }}">
                                    <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                                            type="button" 
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $index }}-{{ $uniqueId }}" 
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                            aria-controls="collapse{{ $index }}-{{ $uniqueId }}"
                                            style="background: #ffffff; color: #3D4F60; font-weight: 600; font-size: 1.1rem; padding: 1.5rem; border: none; box-shadow: none;">
                                        {{ $itemTitle }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}-{{ $uniqueId }}" 
                                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                     aria-labelledby="heading{{ $index }}-{{ $uniqueId }}" 
                                     data-bs-parent="#accordionExample-{{ $uniqueId }}">
                                    <div class="accordion-body" style="padding: 1.5rem; color: #6c757d; line-height: 1.8;">
                                        {!! $itemContent !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<style>
.accordion-button:not(.collapsed) {
    background: #f8f9fa !important;
    color: #3D4F60 !important;
}
.accordion-button:focus {
    box-shadow: none !important;
    border-color: transparent !important;
}
.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%233D4F60'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}
</style>
