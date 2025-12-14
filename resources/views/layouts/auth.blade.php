<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

@php
    try {
        $site = \App\Models\SiteSetting::getSettings();
    } catch (\Exception $e) {
        $site = new \App\Models\SiteSetting();
    }

    // Get localized values
    $currentLocale = app()->getLocale();
    $metaTitle = is_array($site->meta_title ?? null)
        ? ($site->meta_title[$currentLocale] ?? reset($site->meta_title ?? []))
        : ($site->meta_title ?? '');
    $metaDescription = is_array($site->meta_description ?? null)
        ? ($site->meta_description[$currentLocale] ?? reset($site->meta_description ?? []))
        : ($site->meta_description ?? '');
    $metaKeywords = is_array($site->meta_keywords ?? null)
        ? ($site->meta_keywords[$currentLocale] ?? reset($site->meta_keywords ?? []))
        : ($site->meta_keywords ?? '');
    $brandName = is_array($site->brand_name ?? null)
        ? ($site->brand_name[$currentLocale] ?? reset($site->brand_name ?? []))
        : ($site->brand_name ?? config('app.name'));
@endphp

@include('layouts.partials.head')

<body>
    <!-- Main Content -->
    @yield('content')

    @include('layouts.partials.scripts')
    
    @stack('scripts')
</body>

</html>

