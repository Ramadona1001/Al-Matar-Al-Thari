<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
        @php
            echo \App\Services\ThemeService::generateThemeStyles();
        @endphp
        
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-primary-color, #1B4332);
            background-color: var(--bg-secondary-color, #f3f4f6);
        }
        
        .btn-primary {
            background-color: var(--theme-primary-color, #1B4332);
            border-color: var(--theme-primary-color, #1B4332);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .btn-primary:hover {
            background-color: var(--gradient-end-color, #2D5016);
            border-color: var(--gradient-end-color, #2D5016);
        }
        
        a {
            color: var(--theme-primary-color, #1B4332);
        }
        
        a:hover {
            color: var(--gradient-end-color, #2D5016);
        }
    </style>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>
        </div>
    </body>
</html>
