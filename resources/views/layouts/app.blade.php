<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @php
            // Check for Vite manifest first
            $manifestPath = public_path('build/manifest.json');
            $hasManifest = file_exists($manifestPath);
            
            // Check for static files
            $staticCss = file_exists(public_path('css/app.css'));
            $staticJs = file_exists(public_path('js/app.js'));
        @endphp
        @if($hasManifest)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @elseif($staticCss || $staticJs)
            <!-- Using static files (no npm build needed) -->
            @if($staticCss)
                <link rel="stylesheet" href="{{ asset('css/app.css') }}">
            @endif
            <!-- Alpine.js from CDN -->
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
            <!-- GSAP from CDN -->
            <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
            @if($staticJs)
                <script src="{{ asset('js/app.js') }}" defer></script>
            @endif
        @else
            <!-- Fallback: Try to load from build directory if exists -->
            @php
                $buildDir = public_path('build');
                $cssFiles = is_dir($buildDir . '/assets') ? glob($buildDir . '/assets/*.css') : [];
                $jsFiles = is_dir($buildDir . '/assets') ? glob($buildDir . '/assets/*.js') : [];
            @endphp
            @if(!empty($cssFiles))
                @foreach($cssFiles as $cssFile)
                    <link rel="stylesheet" href="{{ asset('build/assets/' . basename($cssFile)) }}">
                @endforeach
            @endif
            @if(!empty($jsFiles))
                @foreach($jsFiles as $jsFile)
                    <script src="{{ asset('build/assets/' . basename($jsFile)) }}" defer></script>
                @endforeach
            @endif
        @endif
    </head>
    <style>
        body { 
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
            background-image: url('{{ asset('pattern.png') }}');
            background-repeat: repeat;
            background-attachment: fixed;
            background-position: center;
        }
        
        @php
            echo \App\Services\ThemeService::generateThemeStyles();
        @endphp
    </style>
    <body class="antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>
    </body>
</html>
