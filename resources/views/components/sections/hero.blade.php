@props(['section' => null, 'banners' => collect()])

@php
    $section = $section ?? (object)[];
    // Get banner data from section or use first banner
    $heroBanner = $banners->first() ?? null;
    $title = isset($section->title) ? $section->title : (isset($heroBanner->title) ? $heroBanner->title : __('Transform Your Business with Innovation'));
    $subtitle = isset($section->subtitle) ? $section->subtitle : (isset($heroBanner->subtitle) ? $heroBanner->subtitle : '');
    $description = isset($section->content) ? $section->content : (isset($heroBanner->content) ? $heroBanner->content : (isset($heroBanner->description) ? $heroBanner->description : __('The end-to-end solution built to simplify complexity and accelerate transformation.')));
    $image = isset($section->image_path) ? $section->image_path : (isset($heroBanner->image_path) ? $heroBanner->image_path : null);
    $buttonText = isset($heroBanner->button_text) ? $heroBanner->button_text : (isset($section->button_text) ? $section->button_text : __('Let\'s Start'));
    $buttonLink = isset($heroBanner->button_link) ? $heroBanner->button_link : (isset($section->button_link) ? $section->button_link : route('register', ['type' => 'customer']));
@endphp

<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-primary-50 via-white to-primary-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900" 
         id="hero"
         data-animate="fade-in">
    @if($image)
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('storage/' . $image) }}" 
                 alt="{{ $title }}" 
                 class="w-full h-full object-cover opacity-20 dark:opacity-10">
            <div class="absolute inset-0 bg-gradient-to-r from-primary-600/80 to-primary-800/80 dark:from-gray-900/90 dark:to-gray-800/90"></div>
        </div>
    @else
        <!-- Animated Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-100/50 via-transparent to-primary-200/50 dark:from-primary-900/30 dark:via-transparent dark:to-primary-800/30"></div>
            <div class="absolute top-0 left-0 w-96 h-96 bg-primary-300/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>
    @endif

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 text-center">
        <div class="max-w-4xl mx-auto" data-animate="fade-in-up" data-delay="0.2">
            @if($subtitle)
                <div class="inline-flex items-center px-4 py-2 mb-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full text-primary-600 dark:text-primary-400 text-sm font-semibold shadow-lg animate-fade-in-down">
                    <i class="fas fa-sparkles mr-2"></i>
                    {{ $subtitle }}
                </div>
            @endif
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 dark:text-white mb-6 leading-tight animate-fade-in-up" 
                style="animation-delay: 0.3s;">
                {{ $title }}
            </h1>
            
            <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-10 max-w-2xl mx-auto animate-fade-in-up" 
               style="animation-delay: 0.4s;">
                {{ $description }}
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fade-in-up" 
                 style="animation-delay: 0.5s;">
                <a href="{{ $buttonLink }}" 
                   class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                    <span class="relative z-10 flex items-center">
                        <i class="fas fa-rocket mr-2 group-hover:translate-x-1 transition-transform"></i>
                        {{ $buttonText }}
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-400 to-primary-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                
                <a href="#about" 
                   class="inline-flex items-center px-8 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-primary-600 dark:text-primary-400 border-2 border-primary-600 dark:border-primary-400 font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('Learn More') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce z-10">
        <a href="#about" class="flex flex-col items-center text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <span class="text-sm mb-2">{{ __('Scroll') }}</span>
            <i class="fas fa-chevron-down text-2xl"></i>
        </a>
    </div>
</section>
