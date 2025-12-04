@extends('layouts.new-design')

@section('meta_title', __('Contact Us'))
@section('meta_description', __('Get in touch with us'))

@section('content')
    <x-page-title 
        :title="__('Contact Us')" 
        :subtitle="__('Provides hassle-free backyard transformation')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Contact'), 'url' => '#']
        ]"
    />
    
    @php
        // Use contact section setting if available
        $contactSection = null;
        if (isset($contactSectionSetting) && $contactSectionSetting) {
            $currentLocale = app()->getLocale();
            $currentTranslation = $contactSectionSetting->translate($currentLocale);
            $enTranslation = $contactSectionSetting->translate('en');
            $title = ($currentTranslation && $currentTranslation->title) 
                ? $currentTranslation->title 
                : (($enTranslation && $enTranslation->title) ? $enTranslation->title : __('Contact Us'));
            $subtitle = ($currentTranslation && $currentTranslation->subtitle) 
                ? $currentTranslation->subtitle 
                : (($enTranslation && $enTranslation->subtitle) ? $enTranslation->subtitle : __('Get in Touch'));
            $contactSection = (object)[
                'title' => $title,
                'subtitle' => $subtitle,
            ];
        } elseif (isset($section) && $section) {
            $contactSection = $section;
        } else {
            $contactSection = (object)[
                'title' => __('Send Us A Message For Next Project'),
                'subtitle' => __('Call to Action')
            ];
        }
    @endphp
    
    <x-contact-form-new :section="$contactSection" :siteSettings="$siteSettings ?? null" />
@endsection
