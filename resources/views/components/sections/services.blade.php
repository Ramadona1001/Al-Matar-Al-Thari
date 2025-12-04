@props(['section' => null, 'services' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displayServices = $items->count() > 0 ? $items : ($services ?? collect());
    $title = isset($section->title) ? $section->title : __('Our Services');
    $subtitle = isset($section->subtitle) ? $section->subtitle : __('What We Offer');
@endphp

<section id="services" class="py-20 md:py-32 bg-white dark:bg-gray-900 relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-100/30 dark:bg-primary-900/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-200/30 dark:bg-primary-800/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16" data-animate="fade-in-up">
            @if($subtitle)
                <div class="inline-flex items-center px-4 py-2 mb-4 bg-primary-100 dark:bg-primary-900/50 rounded-full text-primary-600 dark:text-primary-400 text-sm font-semibold">
                    <i class="fas fa-sparkles mr-2"></i>
                    {{ $subtitle }}
                </div>
            @endif
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $title }}
            </h2>
        </div>
        
        <!-- Services Grid -->
        @if($displayServices->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($displayServices as $index => $service)
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 overflow-hidden border border-gray-100 dark:border-gray-700"
                         data-animate="fade-in-up" 
                         data-delay="{{ $index * 0.1 }}">
                        <!-- Service Image -->
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800">
                            @if(isset($service->image_path) && $service->image_path)
                                <img src="{{ asset('storage/' . $service->image_path) }}" 
                                     alt="{{ $service->title ?? 'Service' }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @elseif(isset($service->icon) && $service->icon)
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="{{ $service->icon }} text-6xl text-primary-600 dark:text-primary-400 group-hover:scale-125 transition-transform duration-500"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-concierge-bell text-6xl text-primary-600 dark:text-primary-400"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        
                        <!-- Service Content -->
                        <div class="p-6">
                            @if(isset($service->title) && $service->title)
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $service->title }}
                                </h3>
                            @endif
                            
                            @if(isset($service->description) && $service->description)
                                <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                    {{ $service->description }}
                                </p>
                            @elseif(isset($service->short_description) && $service->short_description)
                                <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                    {{ $service->short_description }}
                                </p>
                            @elseif(isset($service->content) && $service->content)
                                <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                    {{ Str::limit(strip_tags($service->content), 120) }}
                                </p>
                            @endif
                            
                            <a href="{{ $service->link ?? ($service->slug ? route('public.services.show', $service->slug) : '#') }}" 
                               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 group/btn">
                                <span>{{ $service->link_text ?? __('Show Details') }}</span>
                                <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Placeholder Services -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @for($i = 0; $i < 6; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-4 overflow-hidden border border-gray-100 dark:border-gray-700"
                         data-animate="fade-in-up" 
                         data-delay="{{ $i * 0.1 }}">
                        <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center">
                            <i class="fas fa-concierge-bell text-6xl text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Service') }} {{ $i + 1 }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">{{ __('Add your service description here from CMS') }}</p>
                            <a href="#" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                <span>{{ __('Show Details') }}</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</section>
