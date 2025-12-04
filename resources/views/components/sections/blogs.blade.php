@props(['section' => null, 'blogs' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    $displayBlogs = $items->count() > 0 ? $items : ($blogs ?? collect());
    $title = isset($section->title) ? $section->title : __('Latest News');
    $subtitle = isset($section->subtitle) ? $section->subtitle : __('Stay Updated');
@endphp

<section id="blogs" class="py-20 md:py-32 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16" data-animate="fade-in-up">
            @if($subtitle)
                <div class="inline-flex items-center px-4 py-2 mb-4 bg-primary-100 dark:bg-primary-900/50 rounded-full text-primary-600 dark:text-primary-400 text-sm font-semibold">
                    <i class="fas fa-newspaper mr-2"></i>
                    {{ $subtitle }}
                </div>
            @endif
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $title }}
            </h2>
        </div>
        
        <!-- Blogs Grid -->
        @if($displayBlogs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($displayBlogs as $index => $blog)
                    <article class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden group"
                             data-animate="fade-in-up" 
                             data-delay="{{ $index * 0.1 }}">
                        <!-- Featured Image -->
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800">
                            @if(isset($blog->featured_image) && $blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                                     alt="{{ $blog->title ?? 'Blog Post' }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-newspaper text-6xl text-primary-600 dark:text-primary-400"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 bg-white/90 dark:bg-gray-800/90 rounded-full text-xs font-semibold text-primary-600 dark:text-primary-400">
                                    {{ __('News') }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Blog Content -->
                        <div class="p-6">
                            <!-- Date & Author -->
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                                @if(isset($blog->published_at) && $blog->published_at)
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span>{{ $blog->published_at->format('M d, Y') }}</span>
                                @endif
                                @if(isset($blog->author) && $blog->author)
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-user mr-2"></i>
                                    <span>{{ $blog->author->name ?? $blog->author_name ?? '' }}</span>
                                @endif
                            </div>
                            
                            <!-- Title -->
                            @if(isset($blog->title) && $blog->title)
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">
                                    {{ $blog->title }}
                                </h3>
                            @endif
                            
                            <!-- Excerpt/Short Description -->
                            @if(isset($blog->excerpt) && $blog->excerpt)
                                <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                    {{ $blog->excerpt }}
                                </p>
                            @elseif(isset($blog->content) && $blog->content)
                                <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                    {{ Str::limit(strip_tags($blog->content), 120) }}
                                </p>
                            @endif
                            
                            <!-- Read More Link -->
                            <a href="{{ $blog->slug ? route('public.blogs.show', $blog->slug) : '#' }}" 
                               class="inline-flex items-center text-primary-600 dark:text-primary-400 font-semibold hover:text-primary-700 dark:hover:text-primary-300 transition-colors group/link">
                                <span>{{ __('Read More') }}</span>
                                <i class="fas fa-arrow-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <!-- Placeholder Blogs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @for($i = 0; $i < 3; $i++)
                    <article class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-6" data-animate="fade-in-up" data-delay="{{ $i * 0.1 }}">
                        <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900 dark:to-primary-800 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-newspaper text-6xl text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ __('Published') }} {{ now()->subDays($i)->format('M d, Y') }}</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Blog Post') }} {{ $i + 1 }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">{{ __('Add your blog content from CMS') }}</p>
                        <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary-400 font-semibold">
                            <span>{{ __('Read More') }}</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </article>
                @endfor
            </div>
        @endif
    </div>
</section>

