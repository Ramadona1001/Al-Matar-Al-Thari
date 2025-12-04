@extends('layouts.new-design')

@section('meta_title', __('Terms & Conditions'))
@section('meta_description', __('Terms and Conditions'))

@section('content')
    @php
        $page = class_exists(\App\Models\Page::class)
            ? \App\Models\Page::where('slug', 'terms')
                ->where('locale', app()->getLocale())
                ->where('is_published', true)
                ->first()
            : null;
        $pageTitle = $page ? (is_array($page->title) ? ($page->title[app()->getLocale()] ?? reset($page->title)) : ($page->title ?? __('Terms & Conditions'))) : __('Terms & Conditions');
    @endphp
    
    <x-page-title 
        :title="$pageTitle" 
        :subtitle="__('Provides hassle-free backyard transformation')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => $pageTitle, 'url' => '#']
        ]"
    />
    
    <!-- page content area start  -->
    <section class="blog-area pt-120 pb-60">
        <div class="container">
            <div class="row wow fadeInUp" data-wow-delay=".3s">
                <div class="col-xl-12">
                    <div class="blog-details-wrapper">
                        @if($page && $page->content)
                            @php
                                $pageContent = is_array($page->content) ? ($page->content[app()->getLocale()] ?? reset($page->content)) : ($page->content ?? '');
                            @endphp
                            <div class="blog-content">
                                <div class="prose prose-lg max-w-none">
                                    {!! $pageContent !!}
                                </div>
                            </div>
                        @else
                            <div class="blog-content">
                                <p>{{ __('By using this service, you agree to the following terms...') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- page content area end  -->
@endsection
