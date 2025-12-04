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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
