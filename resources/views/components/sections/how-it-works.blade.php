@props(['section' => null, 'steps' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displaySteps = $items->count() > 0 ? $items : ($steps ?? collect());
    $title = isset($section->title) ? $section->title : __('How It Works');
    $subtitle = isset($section->subtitle) ? $section->subtitle : __('Simple Steps to Get Started');
@endphp

<section id="how-it-works" class="py-20 md:py-32 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle, #3b82f6 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16" data-animate="fade-in-up">
            @if($subtitle)
                <div class="inline-flex items-center px-4 py-2 mb-4 bg-primary-100 dark:bg-primary-900/50 rounded-full text-primary-600 dark:text-primary-400 text-sm font-semibold">
                    <i class="fas fa-route mr-2"></i>
                    {{ $subtitle }}
                </div>
            @endif
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $title }}
            </h2>
        </div>
        
        <!-- Steps -->
        @if($displaySteps->count() > 0)
            <div class="relative">
                <!-- Connection Line (Desktop) -->
                <div class="hidden lg:block absolute top-24 left-0 right-0 h-0.5 bg-gradient-to-r from-primary-200 via-primary-400 to-primary-200 dark:from-primary-800 dark:via-primary-600 dark:to-primary-800"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($displaySteps as $index => $step)
                        <div class="relative" data-animate="fade-in-up" data-delay="{{ $index * 0.15 }}">
                            <!-- Step Number Badge -->
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10 hidden lg:block">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ $step->step_number ?? ($index + 1) }}
                                </div>
                            </div>
                            
                            <!-- Step Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 p-8 text-center border-2 border-transparent hover:border-primary-200 dark:hover:border-primary-800">
                                <!-- Icon/Image -->
                                <div class="mb-6">
                                    @if(isset($step->image_path) && $step->image_path)
                                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 p-2">
                                            <img src="{{ asset('storage/' . $step->image_path) }}" 
                                                 alt="{{ $step->title ?? 'Step' }}" 
                                                 class="w-full h-full object-cover rounded-full">
                                        </div>
                                    @elseif(isset($step->icon) && $step->icon)
                                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center">
                                            <i class="{{ $step->icon }} text-3xl text-primary-600 dark:text-primary-400"></i>
                                        </div>
                                    @else
                                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center">
                                            <i class="fas fa-check-circle text-3xl text-primary-600 dark:text-primary-400"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Step Number (Mobile) -->
                                <div class="lg:hidden mb-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-white font-bold">
                                        {{ $step->step_number ?? ($index + 1) }}
                                    </span>
                                </div>
                                
                                <!-- Title -->
                                @if(isset($step->title) && $step->title)
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                                        {{ $step->title }}
                                    </h3>
                                @endif
                                
                                <!-- Description -->
                                @if(isset($step->description) && $step->description)
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ $step->description }}
                                    </p>
                                @elseif(isset($step->content) && $step->content)
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ $step->content }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Default Steps -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $defaultSteps = [
                        ['icon' => 'fas fa-user-plus', 'title' => __('Sign Up'), 'description' => __('Create your account in seconds')],
                        ['icon' => 'fas fa-user-check', 'title' => __('Complete Profile'), 'description' => __('Fill in your information')],
                        ['icon' => 'fas fa-hand-pointer', 'title' => __('Choose Service'), 'description' => __('Select the service you need')],
                        ['icon' => 'fas fa-check-circle', 'title' => __('Enjoy The Platform'), 'description' => __('Start using our platform')],
                    ];
                @endphp
                @foreach($defaultSteps as $index => $step)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center" data-animate="fade-in-up" data-delay="{{ $index * 0.15 }}">
                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 flex items-center justify-center mb-6">
                            <i class="{{ $step['icon'] }} text-3xl text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $step['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

