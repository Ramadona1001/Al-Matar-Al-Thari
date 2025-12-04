@extends('layouts.new-design')

@section('meta_title', __('How It Works'))
@section('meta_description', __('Learn how our platform works'))

@section('content')
    <x-page-title 
        :title="__('How It Works')" 
        :subtitle="__('Simple Steps to Get Started')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('How It Works'), 'url' => '#']
        ]"
    />
    
    @php
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->where(function($q) {
                    $q->where('page', 'how-it-works')
                      ->orWhere('type', 'how-it-works');
                })
                ->with(['activeItems', 'translations'])
                ->ordered()
                ->get()
            : collect();
        
        $steps = class_exists(\App\Models\HowItWorksStep::class)
            ? \App\Models\HowItWorksStep::active()
                ->ordered()
                ->get()
            : collect();
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
                                :steps="$steps"
                                :partners="collect()"
                                :blogs="collect()"
                                :faqs="collect()"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @elseif($steps->count() > 0)
        <x-section-renderer-new 
            :section="(object)['type' => 'how-it-works', 'title' => __('How It Works'), 'subtitle' => __('Simple Steps')]" 
            :banners="collect()" 
            :services="collect()" 
            :testimonials="collect()" 
            :statistics="collect()"
            :steps="$steps"
            :partners="collect()"
            :blogs="collect()"
            :faqs="collect()"
        />
    @endif
@endsection
