@props(['section' => null])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
@endphp

<section id="features" class="py-20 md:py-32 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 fade-in-up" data-animate="fade-in-up">
            @if(isset($section->title) && $section->title)
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $section->title }}
                </h2>
            @endif
            
            @if(isset($section->subtitle) && $section->subtitle)
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    {{ $section->subtitle }}
                </p>
            @endif
        </div>
        
        @if($items->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($items as $index => $item)
                    <div class="group relative bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl p-8 hover:shadow-2xl transition-all duration-300 fade-in-up" 
                         data-animate="fade-in-up" 
                         data-delay="{{ $index * 0.1 }}">
                        @if(isset($item->icon) && $item->icon)
                            <div class="w-16 h-16 rounded-xl bg-primary-100 dark:bg-primary-900 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <i class="{{ $item->icon }} text-primary-600 dark:text-primary-400 text-2xl"></i>
                            </div>
                        @endif
                        
                        @if(isset($item->title) && $item->title)
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                {{ $item->title }}
                            </h3>
                        @endif
                        
                        @if(isset($item->content) && $item->content)
                            <p class="text-gray-600 dark:text-gray-300">
                                {{ $item->content }}
                            </p>
                        @endif
                        
                        @if(isset($item->subtitle) && $item->subtitle)
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-primary-600 dark:text-primary-400 font-semibold">
                                    {{ $item->subtitle }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Placeholder Features -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach(['rocket', 'shield', 'bolt', 'chart-line', 'users', 'cog'] as $index => $icon)
                    <div class="group relative bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl p-8 hover:shadow-2xl transition-all duration-300 fade-in-up" 
                         data-animate="fade-in-up" 
                         data-delay="{{ $index * 0.1 }}">
                        <div class="w-16 h-16 rounded-xl bg-primary-100 dark:bg-primary-900 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-{{ $icon }} text-primary-600 dark:text-primary-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Feature') }} {{ $index + 1 }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ __('Add your feature description here') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

