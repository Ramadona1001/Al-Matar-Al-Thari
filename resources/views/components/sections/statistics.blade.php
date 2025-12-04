@props(['section' => null, 'statistics' => null])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displayStats = $statistics ?? collect();
@endphp

<section id="statistics" class="py-20 md:py-32 bg-gradient-to-br from-primary-600 to-primary-800 dark:from-primary-900 dark:to-primary-700 text-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(isset($section->title) && $section->title)
            <div class="text-center mb-16 fade-in-up" data-animate="fade-in-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    {{ $section->title }}
                </h2>
                @if(isset($section->subtitle) && $section->subtitle)
                    <p class="text-xl text-primary-100 max-w-3xl mx-auto">
                        {{ $section->subtitle }}
                    </p>
                @endif
            </div>
        @endif
        
        @if($displayStats->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach($displayStats as $index => $stat)
                    <div class="text-center fade-in-up" data-animate="fade-in-up" data-delay="{{ $index * 0.1 }}">
                        @if($stat->icon)
                            <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-4">
                                <i class="{{ $stat->icon }} text-2xl"></i>
                            </div>
                        @endif
                        <div class="text-4xl md:text-5xl font-bold mb-2">
                            {{ $stat->value }}{{ $stat->suffix ?? '' }}
                        </div>
                        <div class="text-lg text-primary-100 font-semibold mb-2">
                            {{ $stat->label }}
                        </div>
                        @if($stat->description)
                            <p class="text-sm text-primary-200">
                                {{ $stat->description }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif($items->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach($items as $index => $item)
                    <div class="text-center fade-in-up" data-animate="fade-in-up" data-delay="{{ $index * 0.1 }}">
                        @if(isset($item->icon) && $item->icon)
                            <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-4">
                                <i class="{{ $item->icon }} text-2xl"></i>
                            </div>
                        @endif
                        <div class="text-4xl md:text-5xl font-bold mb-2">
                            {{ isset($item->title) ? $item->title : '0+' }}
                        </div>
                        <div class="text-lg text-primary-100 font-semibold">
                            {{ isset($item->content) ? $item->content : (isset($item->subtitle) ? $item->subtitle : 'Statistic') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Placeholder Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach([
                    ['value' => '360+', 'label' => __('Clients worldwide')],
                    ['value' => '20+', 'label' => __('Years experience')],
                    ['value' => '50+', 'label' => __('Countries')],
                    ['value' => '100+', 'label' => __('Experts')]
                ] as $index => $stat)
                    <div class="text-center fade-in-up" data-animate="fade-in-up" data-delay="{{ $index * 0.1 }}">
                        <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stat['value'] }}</div>
                        <div class="text-lg text-primary-100 font-semibold">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

