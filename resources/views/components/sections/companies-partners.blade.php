@props(['section' => null, 'partners' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displayPartners = $items->count() > 0 ? $items : ($partners ?? collect());
    $title = isset($section->title) ? $section->title : __('Our Partners');
    $subtitle = isset($section->subtitle) ? $section->subtitle : __('Trusted by Leading Companies');
@endphp

<section id="partners" class="py-16 md:py-24 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12" data-animate="fade-in-up">
            @if($subtitle)
                <div class="inline-flex items-center px-4 py-2 mb-4 bg-primary-100 dark:bg-primary-900/50 rounded-full text-primary-600 dark:text-primary-400 text-sm font-semibold">
                    <i class="fas fa-handshake mr-2"></i>
                    {{ $subtitle }}
                </div>
            @endif
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $title }}
            </h2>
        </div>
        
        <!-- Partners Slider -->
        @if($displayPartners->count() > 0)
            <div class="swiper partners-swiper" data-animate="fade-in-up" data-delay="0.2">
                <div class="swiper-wrapper">
                    @foreach($displayPartners as $partner)
                        <div class="swiper-slide">
                            <div class="flex items-center justify-center h-32 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-gray-100 dark:border-gray-700">
                                @if(isset($partner->logo_path) && $partner->logo_path)
                                    <a href="{{ $partner->website_url ?? '#' }}" 
                                       target="{{ $partner->website_url ? '_blank' : '_self' }}"
                                       class="block w-full h-full flex items-center justify-center group">
                                        <img src="{{ asset('storage/' . $partner->logo_path) }}" 
                                             alt="{{ $partner->name ?? 'Partner' }}" 
                                             class="max-h-16 max-w-full object-contain opacity-70 group-hover:opacity-100 transition-opacity duration-300 grayscale group-hover:grayscale-0">
                                    </a>
                                @else
                                    <div class="text-center">
                                        <div class="w-16 h-16 mx-auto rounded-lg bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center mb-2">
                                            <i class="fas fa-building text-2xl text-primary-600 dark:text-primary-400"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $partner->name ?? 'Partner' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination mt-8"></div>
            </div>
        @else
            <!-- Placeholder Partners -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @for($i = 0; $i < 6; $i++)
                    <div class="flex items-center justify-center h-32 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto rounded-lg bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center mb-2">
                                <i class="fas fa-building text-2xl text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Partner') }} {{ $i + 1 }}</p>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const partnersSwiper = document.querySelector('.partners-swiper');
    if (partnersSwiper) {
        new Swiper('.partners-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            loop: true,
            breakpoints: {
                640: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                768: {
                    slidesPerView: 4,
                    spaceBetween: 40,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 50,
                },
                1280: {
                    slidesPerView: 6,
                    spaceBetween: 50,
                },
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }
});
</script>
@endpush

