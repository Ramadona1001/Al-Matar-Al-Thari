@props(['section' => null, 'blogs' => collect()])

@php
    $section = $section ?? (object)[];
    $displayBlogs = $blogs->count() > 0 ? $blogs : collect();
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Latest News'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Latest News');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : __('Stay Updated'));
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

@if($displayBlogs->count() > 0)
    <section class="section" style="padding: 80px 0; background: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $title }}</h2>
                    @if($subtitle)
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="row">
                @foreach($displayBlogs as $blog)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="blog-card" style="background: #ffffff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; overflow: hidden; height: 100%; display: flex; flex-direction: column;">
                            @if(isset($blog->featured_image) && $blog->featured_image)
                                <div style="width: 100%; height: 200px; overflow: hidden;">
                                    <a href="{{ route('public.blog.show', $blog->slug) }}">
                                        <img src="{{ asset('storage/'.$blog->featured_image) }}" alt="{{ $blog->title ?? 'Blog' }}" style="width: 100%; height: 100%; object-fit: cover; transition: all 0.3s ease;">
                                    </a>
                                </div>
                            @endif
                            <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                                <div class="mb-2" style="font-size: 0.9rem; color: #6c757d;">
                                    @if(isset($blog->published_at) && $blog->published_at)
                                        <i class="bi bi-calendar"></i> {{ $blog->published_at->format('F d, Y') }}
                                    @endif
                                    @if(isset($blog->author) && $blog->author)
                                        <span class="ms-2"><i class="bi bi-person"></i> {{ $blog->author->name ?? 'Admin' }}</span>
                                    @endif
                                </div>
                                @if(isset($blog->title) && $blog->title)
                                    <h3 style="font-size: 1.3rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">
                                        <a href="{{ route('public.blog.show', $blog->slug) }}" style="color: #3D4F60; text-decoration: none;">{{ $blog->title }}</a>
                                    </h3>
                                @endif
                                @if(isset($blog->excerpt) && $blog->excerpt)
                                    <p style="color: #6c757d; line-height: 1.6; flex: 1; margin-bottom: 1rem;">{{ Str::limit($blog->excerpt, 100) }}</p>
                                @endif
                                <a href="{{ route('public.blog.show', $blog->slug) }}" style="color: #17A2B8; text-decoration: none; font-weight: 600;">
                                    {{ __('Read More') }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<style>
.blog-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
.blog-card:hover img {
    transform: scale(1.1);
}
</style>
