@props(['section' => null])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
@endphp

<section class="py-20 md:py-32 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(isset($section->title) && $section->title)
            <div class="text-center mb-12 fade-in-up" data-animate="fade-in-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $section->title }}
                </h2>
                @if(isset($section->subtitle) && $section->subtitle)
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        {{ $section->subtitle }}
                    </p>
                @endif
            </div>
        @endif
        
        @if(isset($section->content) && $section->content)
            <div class="prose prose-lg dark:prose-invert max-w-none fade-in-up" data-animate="fade-in-up" data-delay="0.2">
                {!! nl2br(e($section->content)) !!}
            </div>
        @endif
        
        @if($items->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
                @foreach($items as $index => $item)
                    <div class="fade-in-up" data-animate="fade-in-up" data-delay="{{ $index * 0.1 }}">
                        @if(isset($item->image_path) && $item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" 
                                 alt="{{ isset($item->title) ? $item->title : 'Item' }}" 
                                 class="w-full h-48 object-cover rounded-lg mb-4">
                        @endif
                        @if(isset($item->title) && $item->title)
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $item->title }}
                            </h3>
                        @endif
                        @if(isset($item->content) && $item->content)
                            <p class="text-gray-600 dark:text-gray-300">
                                {{ $item->content }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

