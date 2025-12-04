@extends('layouts.new-design')

@php
    $currentLocale = app()->getLocale();
    if (is_object($blog) && method_exists($blog, 'translate')) {
        $blogCurrent = $blog->translate($currentLocale);
        $blogEn = $blog->translate('en');
        $blogTitle = ($blogCurrent && isset($blogCurrent->title) && $blogCurrent->title) 
            ? $blogCurrent->title 
            : (($blogEn && isset($blogEn->title) && $blogEn->title) ? $blogEn->title : '');
        $blogContent = ($blogCurrent && isset($blogCurrent->content) && $blogCurrent->content) 
            ? $blogCurrent->content 
            : (($blogEn && isset($blogEn->content) && $blogEn->content) ? $blogEn->content : '');
        $blogExcerpt = ($blogCurrent && isset($blogCurrent->excerpt) && $blogCurrent->excerpt) 
            ? $blogCurrent->excerpt 
            : (($blogEn && isset($blogEn->excerpt) && $blogEn->excerpt) ? $blogEn->excerpt : '');
    } else {
        $blogTitle = is_array($blog->title ?? '') ? ($blog->title[$currentLocale] ?? $blog->title['en'] ?? '') : ($blog->title ?? '');
        $blogContent = is_array($blog->content ?? '') ? ($blog->content[$currentLocale] ?? $blog->content['en'] ?? '') : ($blog->content ?? '');
        $blogExcerpt = is_array($blog->excerpt ?? '') ? ($blog->excerpt[$currentLocale] ?? $blog->excerpt['en'] ?? '') : ($blog->excerpt ?? '');
    }
    $metaTitle = is_array($blog->meta_title ?? '') ? ($blog->meta_title[$currentLocale] ?? $blog->meta_title['en'] ?? $blogTitle) : ($blog->meta_title ?? $blogTitle);
    $metaDescription = is_array($blog->meta_description ?? '') ? ($blog->meta_description[$currentLocale] ?? $blog->meta_description['en'] ?? '') : ($blog->meta_description ?? '');
    
    $authorName = $blog->author_name ?? ($blog->author->name ?? 'Admin');
    $publishedDate = $blog->published_at ? $blog->published_at->format('d M, Y') : $blog->created_at->format('d M, Y');
    $category = is_array($blog->categories ?? []) && count($blog->categories) > 0 ? $blog->categories[0] : '';
@endphp

@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)

@section('content')
    <x-page-title 
        :title="$blogTitle" 
        :subtitle="__('Blog Post')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Blog'), 'url' => route('public.blog.index')],
            ['label' => $blogTitle, 'url' => '#']
        ]"
    />
    
    <!-- Blog Single Section -->
    <section class="blog-single-section" style="padding: 80px 0; background: #f5f5f5;">
        <div class="container">
            <div class="row">
                <!-- Main Content Area -->
                <div class="col-lg-8 col-md-12 mb-5 mb-lg-0">
                    <!-- Blog Post -->
                    <article class="blog-post-card mb-4" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                        {{-- Featured Image --}}
                        @if($blog->featured_image)
                            <div class="blog-featured-image" style="width: 100%; height: 500px; overflow: hidden;">
                                <img src="{{ asset('storage/'.$blog->featured_image) }}" alt="{{ $blogTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        
                        <div class="blog-post-body" style="padding: 2.5rem;">
                            {{-- Metadata --}}
                            <div class="blog-meta mb-4" style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.9rem; color: #6b7280;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-user" style="color: var(--brand-primary);"></i>
                                    <span>by {{ $authorName }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-clock" style="color: var(--brand-primary);"></i>
                                    <span>{{ $publishedDate }}</span>
                                </div>
                                @if($category)
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-tag" style="color: var(--brand-primary);"></i>
                                        <span>{{ $category }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Title --}}
                            <h1 class="blog-title mb-4" style="font-size: 2.5rem; font-weight: 700; color: #1a1a1a; line-height: 1.3;">
                                {{ $blogTitle }}
                            </h1>
                            
                            {{-- Excerpt --}}
                            @if($blogExcerpt)
                                <div class="blog-excerpt mb-4" style="font-size: 1.1rem; color: #6b7280; line-height: 1.8;">
                                    {{ $blogExcerpt }}
                                </div>
                            @endif
                            
                            {{-- Content --}}
                            <div class="blog-content" style="color: #4b5563; line-height: 1.8; font-size: 1rem;">
                                {!! $blogContent !!}
                            </div>
                            
                            {{-- Tags and Share --}}
                            <div class="blog-footer mt-5 pt-4" style="border-top: 1px solid #e5e7eb;">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <h5 class="mb-3" style="font-size: 1rem; font-weight: 600; color: #1a1a1a;">{{ __('Tags') }}:</h5>
                                        @if($blog->tags && count($blog->tags) > 0)
                                            <div class="tags-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                                @foreach($blog->tags as $tag)
                                                    <a href="{{ route('public.blog.index', ['tag' => $tag]) }}" class="tag-badge" style="display: inline-block; background: #f3f4f6; color: #6b7280; padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 0.85rem; transition: all 0.3s ease;">
                                                        {{ $tag }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-3" style="font-size: 1rem; font-weight: 600; color: #1a1a1a;">{{ __('Share') }}:</h5>
                                        @php
                                            $site = \App\Models\SiteSetting::getSettings();
                                            $socialLinks = isset($site->social_links) && is_array($site->social_links) ? $site->social_links : [];
                                            $shareUrl = route('public.blog.show', $blog->slug);
                                        @endphp
                                        <div class="social-share" style="display: flex; gap: 0.75rem;">
                                            @if(isset($socialLinks['facebook']) && $socialLinks['facebook'])
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #1877f2; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['twitter']) && $socialLinks['twitter'])
                                                <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($blogTitle) }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #1da1f2; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['linkedin']) && $socialLinks['linkedin'])
                                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #0077b5; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </a>
                                            @endif
                                            @if(isset($socialLinks['instagram']) && $socialLinks['instagram'])
                                                <a href="{{ $socialLinks['instagram'] }}" target="_blank" class="social-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #e4405f; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease;">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    
                    {{-- Comments Section --}}
                    <div class="comments-section mb-4" style="background: #ffffff; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                        <h3 class="mb-4" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">{{ __('Leave a Reply') }}</h3>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form action="{{ route('public.blog.comment', $blog->slug) }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="{{ __('Your Name') }}" required style="padding: 12px 15px; border: 1px solid #e5e7eb; border-radius: 6px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="{{ __('Your Email') }}" required style="padding: 12px 15px; border: 1px solid #e5e7eb; border-radius: 6px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="website" class="form-control" placeholder="{{ __('Website (Optional)') }}" style="padding: 12px 15px; border: 1px solid #e5e7eb; border-radius: 6px;">
                            </div>
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="6" placeholder="{{ __('Your Message') }}" required style="padding: 12px 15px; border: 1px solid #e5e7eb; border-radius: 6px; resize: vertical;"></textarea>
                            </div>
                            <button type="submit" class="btn-submit" style="background: var(--brand-primary); color: #ffffff; padding: 12px 30px; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;">
                                {{ __('Submit Message') }}
                            </button>
                        </form>
                    </div>
                    
                    {{-- Display Comments --}}
                    @if(isset($comments) && $comments->count() > 0)
                        <div class="comments-list" style="background: #ffffff; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                            <h3 class="mb-4" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">{{ __('Comments') }} ({{ $comments->count() }})</h3>
                            
                            @foreach($comments as $comment)
                                <div class="comment-item mb-4 pb-4" style="border-bottom: 1px solid #e5e7eb;">
                                    <div class="d-flex gap-3">
                                        <div class="comment-avatar" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 1.5rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($comment->name, 0, 1)) }}
                                        </div>
                                        <div class="comment-content flex-grow-1">
                                            <h6 class="mb-1 fw-bold" style="color: #1a1a1a;">{{ $comment->name }}</h6>
                                            <small class="text-muted d-block mb-2">{{ $comment->created_at->format('d M, Y') }}</small>
                                            <p style="color: #6b7280; line-height: 1.7; margin: 0;">{{ $comment->comment }}</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Replies --}}
                                    @if($comment->approvedReplies->count() > 0)
                                        <div class="replies mt-3 ps-4" style="border-left: 3px solid var(--brand-primary);">
                                            @foreach($comment->approvedReplies as $reply)
                                                <div class="reply-item mb-3">
                                                    <div class="d-flex gap-3">
                                                        <div class="comment-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 1.2rem; flex-shrink: 0;">
                                                            {{ strtoupper(substr($reply->name, 0, 1)) }}
                                                        </div>
                                                        <div class="comment-content flex-grow-1">
                                                            <h6 class="mb-1 fw-bold" style="color: #1a1a1a;">{{ $reply->name }}</h6>
                                                            <small class="text-muted d-block mb-2">{{ $reply->created_at->format('d M, Y') }}</small>
                                                            <p style="color: #6b7280; line-height: 1.7; margin: 0;">{{ $reply->comment }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <div class="blog-sidebar">
                        {{-- Search Widget --}}
                        <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                            <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Search Here') }}</h3>
                            <form action="{{ route('public.blog.index') }}" method="GET">
                                <div class="input-group" style="display: flex; gap: 0;">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Enter Keyword') }}" value="{{ request('search') }}" style="border: 1px solid #e5e7eb; border-radius: 6px 0 0 6px; padding: 12px 15px; font-size: 0.95rem;">
                                    <button type="submit" class="btn-search" style="background: var(--brand-primary); color: #ffffff; border: none; border-radius: 0 6px 6px 0; padding: 12px 20px; cursor: pointer;">
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
                                    @foreach($allCategories as $cat)
                                        <li style="padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                            <a href="{{ route('public.blog.index', ['category' => $cat]) }}" style="display: flex; align-items: center; justify-content: space-between; color: #6b7280; text-decoration: none;">
                                                <span>{{ $cat }}</span>
                                                <i class="fas fa-chevron-right" style="font-size: 0.75rem; color: var(--brand-primary);"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        {{-- Recent Posts Widget --}}
                        @if(isset($recent) && $recent->count() > 0)
                            <div class="sidebar-widget mb-4" style="background: #ffffff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                                <h3 class="widget-title mb-3" style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a;">{{ __('Recent Posts') }}</h3>
                                <ul class="recent-posts-list" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach($recent as $recentPost)
                                        @php
                                            $recentTitle = '';
                                            if (is_object($recentPost) && method_exists($recentPost, 'translate')) {
                                                $recentCurrent = $recentPost->translate($currentLocale);
                                                $recentEn = $recentPost->translate('en');
                                                $recentTitle = ($recentCurrent && isset($recentCurrent->title) && $recentCurrent->title) 
                                                    ? $recentCurrent->title 
                                                    : (($recentEn && isset($recentEn->title) && $recentEn->title) ? $recentEn->title : '');
                                            } else {
                                                $recentTitle = is_array($recentPost->title ?? '') ? ($recentPost->title[$currentLocale] ?? $recentPost->title['en'] ?? '') : ($recentPost->title ?? '');
                                            }
                                            $recentDate = $recentPost->published_at ? $recentPost->published_at->format('d M, Y') : $recentPost->created_at->format('d M, Y');
                                        @endphp
                                        <li style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #f3f4f6;">
                                            @if($recentPost->featured_image)
                                                <div style="width: 80px; height: 80px; flex-shrink: 0; border-radius: 8px; overflow: hidden;">
                                                    <a href="{{ route('public.blog.show', $recentPost->slug) }}">
                                                        <img src="{{ asset('storage/'.$recentPost->featured_image) }}" alt="{{ $recentTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </a>
                                                </div>
                                            @endif
                                            <div style="flex: 1;">
                                                <div class="mb-1" style="font-size: 0.85rem; color: #6b7280;">
                                                    <i class="fas fa-clock me-1"></i>{{ $recentDate }}
                                                </div>
                                                <h5 style="font-size: 0.95rem; font-weight: 600; color: #1a1a1a; line-height: 1.4; margin: 0;">
                                                    <a href="{{ route('public.blog.show', $recentPost->slug) }}" style="color: inherit; text-decoration: none;">
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
        .tag-badge:hover {
            background: var(--brand-primary) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
        }
        
        .btn-submit:hover {
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
    </style>
@endsection
