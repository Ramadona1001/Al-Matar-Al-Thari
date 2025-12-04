<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@php($site = \App\Models\SiteSetting::query()->first())
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $__env->yieldContent('title', __('Welcome')) }} - {{ $site->brand_name ?? config('app.name') }}</title>
    @if(!empty($site?->favicon_path))
        <link rel="icon" href="{{ asset('storage/'.$site->favicon_path) }}" type="image/png">
    @endif
    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        @php
            echo \App\Services\ThemeService::generateThemeStyles();
        @endphp
        
        /* Public Website Styles with Theme Colors */
        body { 
            background: var(--bg-secondary-color, #f8fafc); 
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif; 
            color: var(--text-primary-color, #1B4332);
        }
        
        .navbar { 
            backdrop-filter: saturate(180%) blur(8px);
            background-color: var(--bg-primary-color, #ffffff) !important;
        }
        
        .hero { 
            background: linear-gradient(135deg, var(--gradient-start-color, #1B4332) 0%, var(--gradient-end-color, #2D5016) 100%);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .card { 
            border: none; 
            border-radius: 1rem; 
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            background-color: var(--bg-primary-color, #ffffff);
        }
        
        .footer { 
            border-top: 1px solid rgba(0,0,0,0.1);
            background-color: var(--bg-primary-color, #ffffff);
        }
        
        /* Buttons with Theme Colors */
        .btn-primary { 
            background-color: var(--theme-primary-color, #1B4332); 
            border-color: var(--theme-primary-color, #1B4332);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .btn-primary:hover { 
            background-color: var(--gradient-end-color, #2D5016); 
            border-color: var(--gradient-end-color, #2D5016);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .btn-outline-primary { 
            color: var(--theme-primary-color, #1B4332); 
            border-color: var(--theme-primary-color, #1B4332); 
        }
        
        .btn-outline-primary:hover { 
            background-color: var(--theme-primary-color, #1B4332); 
            border-color: var(--theme-primary-color, #1B4332);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .text-primary { 
            color: var(--theme-primary-color, #1B4332) !important; 
        }
        
        /* Links with Theme Colors */
        a {
            color: var(--theme-primary-color, #1B4332);
        }
        
        a:hover {
            color: var(--gradient-end-color, #2D5016);
        }
        
        /* Navbar Links */
        .navbar-nav .nav-link {
            color: var(--text-primary-color, #1B4332);
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--theme-primary-color, #1B4332);
        }
        
        /* Badges */
        .badge.bg-primary {
            background-color: var(--theme-primary-color, #1B4332) !important;
        }
        
        /* Text Colors */
        .text-theme-primary {
            color: var(--theme-primary-color, #1B4332) !important;
        }
        
        .text-theme-secondary {
            color: var(--theme-secondary-color, #D4AF37) !important;
        }
        
        /* Background Colors */
        .bg-theme-primary {
            background-color: var(--theme-primary-color, #1B4332) !important;
        }
        
        .bg-theme-secondary {
            background-color: var(--theme-secondary-color, #D4AF37) !important;
        }
        
        /* Gradient Backgrounds */
        .bg-gradient-theme {
            background: linear-gradient(135deg, var(--gradient-start-color, #1B4332) 0%, var(--gradient-end-color, #2D5016) 100%);
        }
    </style>
    @stack('styles')
    @php($localizationService = $localization ?? app(\App\Services\LocalizationService::class))
    @php($alternativeLocales = $localizationService->getAlternativeLocales())
    @php($currentLocale = $localizationService->getCurrentLocale())
    @php($dir = $localizationService->getCurrentDirection())
</head>
<body class="d-flex flex-column min-vh-100" data-theme="light" data-dir="{{ $dir }}">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('public.home') }}">
                @if(!empty($site?->logo_path))
                    <img src="{{ asset('storage/'.$site->logo_path) }}" alt="Logo" style="height:28px" class="me-2">
                @else
                    <i class="fa-solid fa-store text-primary"></i>
                @endif
                <span class="fw-semibold">{{ $site->brand_name ?? config('app.name') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navBar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.home') }}">{{ __('Home') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.about') }}">{{ __('About Us') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.how') }}">{{ __('How It Works') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.faq') }}">{{ __('FAQ') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.contact') }}">{{ __('Contact Us') }}</a></li>
                </ul>
                <ul class="navbar-nav align-items-lg-center gap-2">
                    <li class="nav-item"><a class="btn btn-outline-primary" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                    <li class="nav-item"><a class="btn btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @if(count($alternativeLocales))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $localizationService->getLocaleName($currentLocale) }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach($alternativeLocales as $locale => $data)
                                    <li>
                                        <a class="dropdown-item" href="{{ $data['url'] ?? route('language.switch', $locale) }}">
                                            <span class="me-2">{{ $data['flag'] }}</span>{{ $data['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="footer bg-white py-4 mt-5">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}</div>
            <div class="d-flex gap-3">
                <a href="{{ route('public.terms') }}" class="text-decoration-none">{{ __('Terms & Conditions') }}</a>
                <a href="{{ route('public.privacy') }}" class="text-decoration-none">{{ __('Privacy Policy') }}</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        window.DATATABLE_LOCALE = '{{ app()->getLocale() }}';
        document.addEventListener('DOMContentLoaded', function () {
            const locale = window.DATATABLE_LOCALE || 'en';
            let languageUrl = null;
            switch (locale) {
                case 'ar': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'; break;
                case 'fr': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'; break;
                case 'de': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json'; break;
                case 'es': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'; break;
                default: languageUrl = null;
            }
            const options = {
                responsive: true,
                pageLength: 10,
                lengthChange: true,
                ordering: true,
                language: languageUrl ? { url: languageUrl } : {}
            };
            if (window.jQuery) {
                jQuery('table.datatable').each(function () {
                    const $table = jQuery(this);
                    $table.DataTable(options);
                    $table.closest('.card-body, .container, .table-responsive').find('.pagination').hide();
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
