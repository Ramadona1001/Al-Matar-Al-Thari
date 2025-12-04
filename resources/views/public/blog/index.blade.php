@extends('layouts.new-design')

@section('meta_title', __('Blog & Insights'))
@section('meta_description', __('Latest news and insights'))

@section('content')
    <x-page-title 
        :title="__('Blog & Insights')" 
        :subtitle="__('Latest news and insights')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Blog'), 'url' => '#']
        ]"
    />
    
    <!-- Blog Section -->
    <section class="blog-section" style="padding: 80px 0; background: #f5f5f5;">
        <div class="container">
            <div class="row">
                <!-- Main Content Area -->
                <div class="col-lg-8 col-md-12 mb-5 mb-lg-0">
                    @forelse($blogs as $blog)
                        @php
                            $blogTitle = '';
                            $blogExcerpt = '';
                            $blogContent = '';
                            if (is_object($blog) && method_exists($blog, 'translate')) {
                                $currentLocale = app()->getLocale();
                                $blogCurrent = $blog->translate($currentLocale);
                                $blogEn = $blog->translate('en');
                                $blogTitle = ($blogCurrent && isset($blogCurrent->title) && $blogCurrent->title) 
                                    ? $blogCurrent->title 
                                    : (($blogEn && isset($blogEn->title) && $blogEn->title) ? $blogEn->title : '');
                                $blogExcerpt = ($blogCurrent && isset($blogCurrent->excerpt) && $blogCurrent->excerpt) 
                                    ? $blogCurrent->excerpt 
                                    : (($blogEn && isset($blogEn->excerpt) && $blogEn->excerpt) ? $blogEn->excerpt : '');
                                $blogContent = ($blogCurrent && isset($blogCurrent->content) && $blogCurrent->content) 
                                    ? $blogCurrent->content 
                                    : (($blogEn && isset($blogEn->content) && $blogEn->content) ? $blogEn->content : '');
                            }
                            if (empty($blogTitle)) {
                                $blogTitle = is_array($blog->title ?? '') ? ($blog->title[app()->getLocale()] ?? $blog->title['en'] ?? '') : ($blog->title ?? __('Blog Post'));
                            }
                            if (empty($blogExcerpt)) {
                                $blogExcerpt = is_array($blog->excerpt ?? '') ? ($blog->excerpt[app()->getLocale()] ?? $blog->excerpt['en'] ?? '') : ($blog->excerpt ?? '');
                            }
                            if (empty($blogExcerpt) && !empty($blogContent)) {
                                $blogExcerpt = Str::limit(strip_tags($blogContent), 200);
                            }
                            
                            $authorName = $blog->author_name ?? ($blog->author->name ?? 'Admin');
                            $publishedDate = $blog->published_at ? $blog->published_at->format('d M, Y') : $blog->created_at->format('d M, Y');
                            $category = is_array($blog->categories ?? []) && count($blog->categories) > 0 ? $blog->categories[0] : 'Uncategorized';
                        @endphp
                        <div class="blog-card mb-4" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); transition: all 0.3s ease;">
                            {{-- Featured Image --}}
                            @if($blog->featured_image)
                                <div class="blog-image" style="width: 100%; height: 400px; overflow: hidden;">
                                    <a href="{{ route('public.blog.show', $blog->slug) }}">
                                        <img src="{{ asset('storage/'.$blog->featured_image) }}" alt="{{ $blogTitle }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                    </a>
                                </div>
                            @endif
                            
                            <div class="blog-card-body" style="padding: 2rem;">
                                {{-- Metadata --}}
                                <div class="blog-meta mb-3" style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.9rem; color: #6b7280;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-user" style="color: var(--brand-primary);"></i>
                                        <span>by {{ $authorName }}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-clock" style="color: var(--brand-primary);"></i>
                                        <span>{{ $publishedDate }}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-tag" style="color: var(--brand-primary);"></i>
                                        <span>{{ $category }}</span>
                                    </div>
                                </div>
                                
                                {{-- Title --}}
                                <h2 class="blog-title mb-3" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a; line-height: 1.3;">
                                    <a href="{{ route('public.blog.show', $blog->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.3s ease;">
                                        {{ $blogTitle }}
                                    </a>
                                </h2>
                                
                                {{-- Excerpt --}}
                                @if($blogExcerpt)
                                    <p class="blog-excerpt mb-4" style="color: #6b7280; line-height: 1.7; font-size: 1rem;">
                                        {{ $blogExcerpt }}
                                    </p>
                                @endif
                                
                                {{-- Read Details Button --}}
                                <a href="{{ route('public.blog.show', $blog->slug) }}" class="btn-read-details" style="display: inline-block; background: var(--brand-primary); color: #ffffff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;">
                                    {{ __('Read Details') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="blog-card" style="background: #ffffff; border-radius: 12px; padding: 3rem; text-align: center;">
                            <h2 style="font-size: 2rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem;">{{ __('No blog posts found') }}</h2>
                            <p style="color: #6b7280; font-size: 1.1rem;">{{ __('Check back later for new posts') }}</p>
                        </div>
                    @endforelse
                    
                    {{-- Pagination --}}
                    @if($blogs->hasPages())
                        <div class="pagination-wrapper mt-5" style="display: flex; justify-content: center;">
                            {{ $blogs->links() }}
                        </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <div class="blog-sidebar">
                        {{-- Search Widget --}}
                        <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                            <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Search Here') }}</h3>
                            <form action="{{ route('public.blog.index') }}" method="GET" class="search-form">
                                <div class="input-group" style="display: flex; gap: 0;">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Enter Keyword') }}" value="{{ request('search') }}" style="border: 1px solid #e5e7eb; border-radius: 6px 0 0 6px; padding: 12px 15px; font-size: 0.95rem;">
                                    <button type="submit" class="btn-search" style="background: var(--brand-primary); color: #ffffff; border: none; border-radius: 0 6px 6px 0; padding: 12px 20px; cursor: pointer; transition: background 0.3s ease;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- Categories Widget --}}
                        @if(isset($allCategories) && $allCategories->count() > 0)
                            <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Categories') }}</h3>
                                <ul class="categories-list" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach($allCategories as $category)
                                        <li style="padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                            <a href="{{ route('public.blog.index', ['category' => $category]) }}" style="display: flex; align-items: center; justify-content: space-between; color: #6b7280; text-decoration: none; transition: color 0.3s ease;">
                                                <span>{{ $category }}</span>
                                                <i class="fas fa-chevron-right" style="font-size: 0.75rem; color: var(--brand-primary);"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        {{-- Recent Posts Widget --}}
                        @if(isset($recentPosts) && $recentPosts->count() > 0)
                            <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Recent Posts') }}</h3>
                                <ul class="recent-posts-list" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach($recentPosts as $recentPost)
                                        @php
                                            $recentTitle = '';
                                            if (is_object($recentPost) && method_exists($recentPost, 'translate')) {
                                                $currentLocale = app()->getLocale();
                                                $recentCurrent = $recentPost->translate($currentLocale);
                                                $recentEn = $recentPost->translate('en');
                                                $recentTitle = ($recentCurrent && isset($recentCurrent->title) && $recentCurrent->title) 
                                                    ? $recentCurrent->title 
                                                    : (($recentEn && isset($recentEn->title) && $recentEn->title) ? $recentEn->title : '');
                                            }
                                            if (empty($recentTitle)) {
                                                $recentTitle = is_array($recentPost->title ?? '') ? ($recentPost->title[app()->getLocale()] ?? $recentPost->title['en'] ?? '') : ($recentPost->title ?? __('Blog Post'));
                                            }
                                            $recentDate = $recentPost->published_at ? $recentPost->published_at->format('d M, Y') : $recentPost->created_at->format('d M, Y');
                                        @endphp
                                        <li style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #f3f4f6;">
                                            @if($recentPost->featured_image)
                                                <div class="recent-post-thumb" style="width: 80px; height: 80px; flex-shrink: 0; border-radius: 8px; overflow: hidden;">
                                                    <a href="{{ route('public.blog.show', $recentPost->slug) }}">
                                                        <img src="{{ asset('storage/'.$recentPost->featured_image) }}" alt="{{ $recentTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="recent-post-content" style="flex: 1;">
                                                <div class="recent-post-date mb-1" style="font-size: 0.85rem; color: #6b7280;">{{ $recentDate }}</div>
                                                <h5 class="recent-post-title" style="font-size: 0.95rem; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0;">
                                                    <a href="{{ route('public.blog.show', $recentPost->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.3s ease;">
                                                        {{ Str::limit($recentTitle, 60) }}
                                                    </a>
                                                </h5>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        {{-- Popular Tags Widget --}}
                        @if(isset($allTags) && $allTags->count() > 0)
                            <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Popular Tags') }}</h3>
                                <div class="tags-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                    @foreach($allTags as $tag)
                                        <a href="{{ route('public.blog.index', ['tag' => $tag]) }}" class="tag-item" style="display: inline-block; background: #f3f4f6; color: #6b7280; padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 0.85rem; transition: all 0.3s ease;">
                                            {{ $tag }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        {{-- Contact Banner Widget --}}
                        <div class="sidebar-widget" style="background: #1e3a5f; border-radius: 12px; padding: 2.5rem; text-align: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                            <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin-bottom: 1rem;">{{ __('Need Help?') }}</h3>
                            <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 1.5rem; font-size: 1rem;">{{ __('We Are Here To Help You') }}</p>
                            <a href="{{ route('public.contact') }}" class="btn-contact" style="display: inline-block; background: var(--brand-primary); color: #ffffff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;">
                                {{ __('Contact Us') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
        }
        
        .blog-card:hover .blog-image img {
            transform: scale(1.05);
        }
        
        .blog-title a:hover {
            color: var(--brand-primary) !important;
        }
        
        .btn-read-details:hover {
            background: #b91c1c !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        
        .btn-search:hover {
            background: #b91c1c !important;
        }
        
        .categories-list li a:hover {
            color: var(--brand-primary) !important;
        }
        
        .categories-list li a:hover i {
            transform: translateX(3px);
        }
        
        .recent-post-title a:hover {
            color: var(--brand-primary) !important;
        }
        
        .tag-item:hover {
            background: var(--brand-primary) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }
        
        .btn-contact:hover {
            background: #b91c1c !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        
        .pagination-wrapper .pagination {
            justify-content: center;
        }
        
        .pagination-wrapper .page-link {
            color: var(--brand-primary);
            border-color: #e5e7eb;
        }
        
        .pagination-wrapper .page-item.active .page-link {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
        }
        
        .pagination-wrapper .page-link:hover {
            color: #b91c1c;
            background: #f3f4f6;
        }
    </style>
@endsection
