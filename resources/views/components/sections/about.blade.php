@props(['section' => null])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
@endphp

<section id="about" class="py-20 md:py-32 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="fade-in-up" data-animate="fade-in-up" data-delay="0.1">
                @if(isset($section->title) && $section->title)
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ $section->title }}
                    </h2>
                @endif
                
                @if(isset($section->subtitle) && $section->subtitle)
                    <p class="text-xl text-primary-600 dark:text-primary-400 mb-6 font-semibold">
                        {{ $section->subtitle }}
                    </p>
                @endif
                
                @if(isset($section->content) && $section->content)
                    <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
                        {!! nl2br(e($section->content)) !!}
                    </div>
                @endif
                
                @if($items->count() > 0)
                    <div class="space-y-4">
                        @foreach($items as $item)
                            <div class="flex items-start gap-4">
                                @if(isset($item->icon) && $item->icon)
                                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                        <i class="{{ $item->icon }} text-primary-600 dark:text-primary-400 text-xl"></i>
                                    </div>
                                @endif
                                <div>
                                    @if(isset($item->title) && $item->title)
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            {{ $item->title }}
                                        </h3>
                                    @endif
                                    @if(isset($item->content) && $item->content)
                                        <p class="text-gray-600 dark:text-gray-300">
                                            {{ $item->content }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <div class="relative fade-in-up" data-animate="fade-in-up" data-delay="0.3">
                @if(isset($section->image_path) && $section->image_path)
                    <img src="{{ asset('storage/' . $section->image_path) }}" 
                         alt="{{ $section->title ?? 'About' }}" 
                         class="rounded-2xl shadow-2xl w-full">
                @elseif($items->first() && isset($items->first()->image_path) && $items->first()->image_path)
                    <img src="{{ asset('storage/' . $items->first()->image_path) }}" 
                         alt="{{ $section->title ?? 'About' }}" 
                         class="rounded-2xl shadow-2xl w-full">
                @else
                    <div class="bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 rounded-2xl p-16 shadow-2xl">
                        <div class="text-center">
                            <i class="fas fa-lightbulb text-6xl text-primary-600 dark:text-primary-400 mb-6"></i>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('About Us') }}</h3>
                            <p class="text-gray-600 dark:text-gray-300">{{ __('Learn more about our mission and values') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

