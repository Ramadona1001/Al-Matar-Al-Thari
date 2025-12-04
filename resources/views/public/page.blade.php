@extends('layouts.new-design')

@php
    $currentLocale = app()->getLocale();
    $isTranslatable = is_object($page) && method_exists($page, 'translate');
    $enTranslation = $isTranslatable ? $page->translate('en') : null;
    $currentTranslation = $isTranslatable ? $page->translate($currentLocale) : null;
    
    $pageTitle = '';
    if ($isTranslatable) {
        $pageTitle = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
            ? $currentTranslation->title 
            : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : '');
    }
    if (empty($pageTitle) && isset($page->title)) {
        $pageTitle = is_string($page->title) ? $page->title : (is_array($page->title) ? ($page->title[$currentLocale] ?? reset($page->title) ?? '') : '');
    }
    
    $pageContent = '';
    if ($isTranslatable) {
        $pageContent = ($currentTranslation && isset($currentTranslation->content) && $currentTranslation->content) 
            ? $currentTranslation->content 
            : (($enTranslation && isset($enTranslation->content) && $enTranslation->content) ? $enTranslation->content : '');
    }
    if (empty($pageContent) && isset($page->content)) {
        $pageContent = is_string($page->content) ? $page->content : (is_array($page->content) ? ($page->content[$currentLocale] ?? reset($page->content) ?? '') : '');
    }
    
    $pageExcerpt = '';
    if ($isTranslatable) {
        $pageExcerpt = ($currentTranslation && isset($currentTranslation->excerpt) && $currentTranslation->excerpt) 
            ? $currentTranslation->excerpt 
            : (($enTranslation && isset($enTranslation->excerpt) && $enTranslation->excerpt) ? $enTranslation->excerpt : '');
    }
    if (empty($pageExcerpt) && isset($page->excerpt)) {
        $pageExcerpt = is_string($page->excerpt) ? $page->excerpt : (is_array($page->excerpt) ? ($page->excerpt[$currentLocale] ?? reset($page->excerpt) ?? '') : '');
    }
    
    $metaTitle = '';
    if ($isTranslatable) {
        $metaTitle = ($currentTranslation && isset($currentTranslation->meta_title) && $currentTranslation->meta_title) 
            ? $currentTranslation->meta_title 
            : (($enTranslation && isset($enTranslation->meta_title) && $enTranslation->meta_title) ? $enTranslation->meta_title : '');
    }
    if (empty($metaTitle) && isset($page->meta_title)) {
        $metaTitle = is_string($page->meta_title) ? $page->meta_title : (is_array($page->meta_title) ? ($page->meta_title[$currentLocale] ?? reset($page->meta_title)) : '');
    }
    if (empty($metaTitle)) {
        $metaTitle = $pageTitle;
    }
    
    $metaDescription = '';
    if ($isTranslatable) {
        $metaDescription = ($currentTranslation && isset($currentTranslation->meta_description) && $currentTranslation->meta_description) 
            ? $currentTranslation->meta_description 
            : (($enTranslation && isset($enTranslation->meta_description) && $enTranslation->meta_description) ? $enTranslation->meta_description : '');
    }
    if (empty($metaDescription) && isset($page->meta_description)) {
        $metaDescription = is_string($page->meta_description) ? $page->meta_description : (is_array($page->meta_description) ? ($page->meta_description[$currentLocale] ?? reset($page->meta_description)) : '');
    }
@endphp

@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)

@section('content')
    <x-page-title 
        :title="$pageTitle" 
        :subtitle="$pageExcerpt"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => $pageTitle, 'url' => '#']
        ]"
    />
    
    <section class="section" style="padding: 80px 0; background: #ffffff;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    @if(isset($page->featured_image) && $page->featured_image)
                        <div class="mb-4" style="border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                            <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $pageTitle }}" style="width: 100%; height: auto; display: block;">
                        </div>
                    @endif
                    <div style="background: #ffffff; padding: 2rem; border-radius: 15px;">
                        <div class="prose" style="max-width: none; color: #6c757d; line-height: 1.8;">
                            {!! $pageContent !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
