@props(['section' => null, 'testimonials' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displayTestimonials = $testimonials->count() > 0 ? $testimonials : $items;
    $title = isset($section->title) ? $section->title : __('What Our Clients Say');
    $subtitle = isset($section->subtitle) ? $section->subtitle : __('Testimonials');
@endphp

<section id="testimonials" class="py-20 md:py-32 bg-gray-50 dark:bg-gray-800 relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-100/20 dark:bg-primary-900/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-200/20 dark:bg-primary-800/10 rounded-full blur-3xl"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16" data-animate="fade-in-up">
            @if($subtitle)
                <div class="inline-flex items-center px-4 py-2 mb-4 bg-primary-100 dark:bg-primary-900/50 rounded-full text-primary-600 dark:text-primary-400 text-sm font-semibold">
                    <i class="fas fa-quote-right mr-2"></i>
                    {{ $subtitle }}
                </div>
            @endif
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $title }}
            </h2>
        </div>
        
        <!-- Testimonials Slider -->
        @if($displayTestimonials->count() > 0)
            <div class="swiper testimonials-swiper" data-animate="fade-in-up" data-delay="0.2">
                <div class="swiper-wrapper">
                    @foreach($displayTestimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-8 h-full border border-gray-100 dark:border-gray-700">
                                <!-- Rating Stars -->
                                <div class="flex items-center gap-1 mb-6">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($testimonial->rating ?? 5) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} text-lg"></i>
                                    @endfor
                                </div>
                                
                                <!-- Testimonial Text -->
                                <blockquote class="text-gray-700 dark:text-gray-300 mb-6 text-lg italic leading-relaxed">
                                    "{{ $testimonial->testimonial ?? $testimonial->content ?? '' }}"
                                </blockquote>
                                
                                <!-- Author Info -->
                                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    @if(isset($testimonial->avatar) && $testimonial->avatar)
                                        <img src="{{ asset('storage/' . $testimonial->avatar) }}" 
                                             alt="{{ $testimonial->name ?? 'Client' }}" 
                                             class="w-14 h-14 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-800">
                                    @elseif(isset($testimonial->image_path) && $testimonial->image_path)
                                        <img src="{{ asset('storage/' . $testimonial->image_path) }}" 
                                             alt="{{ $testimonial->name ?? 'Client' }}" 
                                             class="w-14 h-14 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-800">
                                    @else
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center ring-2 ring-primary-200 dark:ring-primary-800">
                                            <i class="fas fa-user text-primary-600 dark:text-primary-400 text-xl"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-lg">
                                            {{ $testimonial->name ?? 'Client' }}
                                        </div>
                                        @if(isset($testimonial->position) && $testimonial->position || isset($testimonial->company) && $testimonial->company)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ ($testimonial->position ?? '') . ($testimonial->position && $testimonial->company ? ', ' : '') . ($testimonial->company ?? '') }}
                                            </div>
                                        @elseif(isset($testimonial->role) && $testimonial->role)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $testimonial->role }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination mt-8"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        @else
            <!-- Placeholder Testimonials -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @for($i = 0; $i < 3; $i++)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8" data-animate="fade-in-up" data-delay="{{ $i * 0.1 }}">
                        <div class="flex items-center gap-1 mb-6">
                            @for($j = 1; $j <= 5; $j++)
                                <i class="fas fa-star text-yellow-400 text-lg"></i>
                            @endfor
                        </div>
                        <blockquote class="text-gray-700 dark:text-gray-300 mb-6 text-lg italic">
                            "{{ __('Excellent platform and great customer support. Highly recommended!') }}"
                        </blockquote>
                        <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center">
                                <i class="fas fa-user text-primary-600 dark:text-primary-400 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">{{ __('Customer') }} {{ $i + 1 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Client') }}</div>
                            </div>
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
    const testimonialsSwiper = document.querySelector('.testimonials-swiper');
    if (testimonialsSwiper) {
        new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            loop: true,
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }
});
</script>
@endpush

@section('styles')
@endsection