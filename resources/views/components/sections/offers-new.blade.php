@props(['section' => null, 'offers' => collect()])

@php
    $section = $section ?? (object)[];
    $displayOffers = $offers->count() > 0 ? $offers : collect();
    if ($displayOffers->count() === 0 && isset($section->activeItems)) {
        $displayOffers = $section->activeItems;
    }
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Featured Offers'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Featured Offers');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : '');
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

@if($displayOffers->count() > 0)
    <section class="section" >
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $title }}</h2>
                    @if($subtitle)
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <div id="offersCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($displayOffers->chunk(3) as $chunkIndex => $chunk)
                        <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $offer)
                                    <div class="col-md-4 mb-4">
                                        <div class="offer-card" style="background: #ffffff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; overflow: hidden; height: 100%; position: relative;">
                                            @php
                                                $offerTitle = $offer->title ?? $offer->name ?? '';
                                                $offerDesc = $offer->description ?? $offer->content ?? '';
                                                $badgeText = '';
                                                if (isset($offer->discount_percentage) && $offer->discount_percentage) {
                                                    $badgeText = number_format($offer->discount_percentage, 0) . '% ' . __('OFF');
                                                } elseif (isset($offer->discount_amount) && $offer->discount_amount) {
                                                    $badgeText = __('Cashback');
                                                } elseif (isset($offer->type)) {
                                                    $badgeText = ucfirst($offer->type);
                                                } else {
                                                    $badgeText = __('Special Offer');
                                                }
                                            @endphp
                                            @if($badgeText)
                                                <span style="position: absolute; top: 1rem; right: 1rem; background: #4BB543; color: #ffffff; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600; z-index: 1;">{{ $badgeText }}</span>
                                            @endif
                                            <div style="padding: 1.5rem;">
                                                @if($offerTitle)
                                                    <h5 style="font-size: 1.3rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ $offerTitle }}</h5>
                                                @endif
                                                @if($offerDesc)
                                                    <p style="color: #6c757d; line-height: 1.6; margin-bottom: 1rem;">{{ Str::limit($offerDesc, 100) }}</p>
                                                @endif
                                                @if(isset($offer->slug))
                                                    <a href="{{ route('public.offers.show', $offer->slug) }}" style="background: #17A2B8; color: #ffffff; padding: 0.5rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 600; display: inline-block; transition: all 0.3s ease;">
                                                        {{ __('View Details') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($displayOffers->count() > 3)
                    <button class="carousel-control-prev" type="button" data-bs-target="#offersCarousel" data-bs-slide="prev" style="width: 5%;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#offersCarousel" data-bs-slide="next" style="width: 5%;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                @endif
            </div>
        </div>
    </section>
@endif

<style>
.offer-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
.offer-card a:hover {
    background: #3D4F60 !important;
    transform: translateY(-2px);
}
</style>
