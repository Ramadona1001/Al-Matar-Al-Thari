@extends('layouts.new-design')

@section('meta_title', __('FAQ'))
@section('meta_description', __('Frequently Asked Questions'))

@section('content')
    <x-page-title 
        :title="__('FAQ')" 
        :subtitle="__('Frequently Asked Questions')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('FAQ'), 'url' => '#']
        ]"
    />
    
    @php
        // Load FAQs from database if available
        $faqsFromDb = isset($faqs) && $faqs->count() > 0 ? $faqs : collect();
        
        // Load sections from CMS
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->forPage('faq')
                ->with(['activeItems', 'translations'])
                ->ordered()
                ->get()
            : collect();
        
        // Use FAQ section setting if available
        $sectionTitle = __('Frequently Asked Questions');
        $sectionSubtitle = __('FAQ');
        if (isset($faqSectionSetting) && $faqSectionSetting) {
            $currentLocale = app()->getLocale();
            $currentTranslation = $faqSectionSetting->translate($currentLocale);
            $enTranslation = $faqSectionSetting->translate('en');
            $sectionTitle = ($currentTranslation && $currentTranslation->title) 
                ? $currentTranslation->title 
                : (($enTranslation && $enTranslation->title) ? $enTranslation->title : $sectionTitle);
            $sectionSubtitle = ($currentTranslation && $currentTranslation->subtitle) 
                ? $currentTranslation->subtitle 
                : (($enTranslation && $enTranslation->subtitle) ? $enTranslation->subtitle : $sectionSubtitle);
        }
    @endphp
    
    @php
        if ($sections->count() > 0) {
            // Group sections by rows based on builder_data
            $rows = [];
            foreach ($sections as $section) {
                $builderData = $section->builder_data ?? ['row' => 0, 'column' => 0, 'width' => 12];
                $row = $builderData['row'] ?? 0;
                if (!isset($rows[$row])) {
                    $rows[$row] = [];
                }
                $rows[$row][] = $section;
            }
            
            // Sort sections within each row by column
            foreach ($rows as $rowIndex => $rowSections) {
                usort($rows[$rowIndex], function($a, $b) {
                    $aCol = ($a->builder_data['column'] ?? 0);
                    $bCol = ($b->builder_data['column'] ?? 0);
                    return $aCol <=> $bCol;
                });
            }
            
            ksort($rows);
        } else {
            $rows = [];
        }
    @endphp
    
    @if(!empty($rows))
        @foreach($rows as $rowIndex => $rowSections)
            <div class="container">
                <div class="row g-4 mb-4">
                    @foreach($rowSections as $section)
                        @php
                            $builderData = $section->builder_data ?? ['width' => 12];
                            $width = $builderData['width'] ?? 12;
                            $colClass = match($width) {
                                1 => 'col-12 col-md-1',
                                2 => 'col-12 col-md-2',
                                3 => 'col-12 col-md-3',
                                4 => 'col-12 col-md-4',
                                5 => 'col-12 col-md-5',
                                6 => 'col-12 col-md-6',
                                7 => 'col-12 col-md-7',
                                8 => 'col-12 col-md-8',
                                9 => 'col-12 col-md-9',
                                10 => 'col-12 col-md-10',
                                11 => 'col-12 col-md-11',
                                12 => 'col-12',
                                default => 'col-12'
                            };
                        @endphp
                        <div class="{{ $colClass }}">
                            <x-section-renderer-new 
                                :section="$section" 
                                :banners="collect()" 
                                :services="collect()" 
                                :testimonials="collect()" 
                                :statistics="collect()"
                                :steps="collect()"
                                :partners="collect()"
                                :blogs="collect()"
                                :faqs="$faqs ?? collect()"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @elseif($faqsFromDb->count() > 0)
        {{-- Display FAQs from database --}}
        <section class="section" style="padding: 80px 0; background: #f8f9fa;">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $sectionTitle }}</h2>
                        @if($sectionSubtitle)
                            <p style="font-size: 1.2rem; color: #6c757d;">{{ $sectionSubtitle }}</p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="accordion" id="faqAccordion">
                            @foreach($faqsFromDb as $index => $faq)
                                @php
                                    $currentLocale = app()->getLocale();
                                    $currentTranslation = $faq->translate($currentLocale);
                                    $enTranslation = $faq->translate('en');
                                    $question = ($currentTranslation && $currentTranslation->question) 
                                        ? $currentTranslation->question 
                                        : (($enTranslation && $enTranslation->question) ? $enTranslation->question : __('Question'));
                                    $answer = ($currentTranslation && $currentTranslation->answer) 
                                        ? $currentTranslation->answer 
                                        : (($enTranslation && $enTranslation->answer) ? $enTranslation->answer : '');
                                @endphp
                                <div class="accordion-item mb-3" style="background: #ffffff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border: none; overflow: hidden;">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $index }}" 
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $index }}"
                                                style="background: #ffffff; color: #3D4F60; font-weight: 600; font-size: 1.1rem; padding: 1.5rem; border: none; box-shadow: none;">
                                            {{ $question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" 
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $index }}" 
                                         data-bs-parent="#faqAccordion">
                                        <div class="accordion-body" style="padding: 1.5rem; color: #6c757d; line-height: 1.8;">
                                            {!! $answer !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <style>
        .accordion-button:not(.collapsed) {
            background: #f8f9fa !important;
            color: #3D4F60 !important;
        }
        .accordion-button:focus {
            box-shadow: none !important;
            border-color: transparent !important;
        }
        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%233D4F60'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        </style>
    @else
        @php
            $page = class_exists(\App\Models\Page::class)
                ? \App\Models\Page::where('slug', 'faq')
                    ->where('is_published', true)
                    ->first()
                : null;
        @endphp
        
        @if($page)
            @php
                $isTranslatable = is_object($page) && method_exists($page, 'translate');
                $currentLocale = app()->getLocale();
                $enTranslation = $isTranslatable ? $page->translate('en') : null;
                $currentTranslation = $isTranslatable ? $page->translate($currentLocale) : null;
                
                $pageContent = ($currentTranslation && isset($currentTranslation->content) && $currentTranslation->content) 
                    ? $currentTranslation->content 
                    : (($enTranslation && isset($enTranslation->content) && $enTranslation->content) ? $enTranslation->content : '');
            @endphp
            
            @if($pageContent)
                <section class="section" style="padding: 80px 0; background: #ffffff;">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-10 mx-auto">
                                <div style="background: #ffffff; padding: 2rem; border-radius: 15px;">
                                    <div class="prose" style="max-width: none;">
                                        {!! $pageContent !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endif
    @endif
@endsection
