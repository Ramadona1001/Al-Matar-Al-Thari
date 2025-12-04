@extends('layouts.new-design')

@php
    $pageTitle = $title ?? __('Page');
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $pageTitle)

@section('content')
    <x-page-title 
        :title="$pageTitle" 
        :subtitle="__('Provides hassle-free backyard transformation')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => $pageTitle, 'url' => '#']
        ]"
    />
    
    @php
        if (isset($sections) && $sections->count() > 0) {
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
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
@endsection

