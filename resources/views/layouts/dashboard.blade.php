<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    
    <!-- Dynamic Theme Colors -->
    <style>
        body {
            background-image: url('{{ asset('pattern.png') }}');
            background-repeat: repeat;
            background-attachment: fixed;
            background-position: center;
        }
        
        @php
            echo \App\Services\ThemeService::generateThemeStyles();
        @endphp
        
        /* Apply theme colors to existing elements */
        .btn-primary {
            background-color: var(--theme-primary-color);
            border-color: var(--theme-primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--gradient-end-color);
            border-color: var(--gradient-end-color);
        }
        
        .text-primary {
            color: var(--theme-primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--theme-primary-color) !important;
        }
        
        .border-primary {
            border-color: var(--theme-primary-color) !important;
        }
        
        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--gradient-start-color) 0%, var(--gradient-end-color) 100%);
        }
        
        .chart-icon-wrapper.bg-primary-subtle {
            background-color: rgba(27, 67, 50, 0.1) !important;
        }
    </style>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    @stack('styles')
</head>
<body class="dashboard-body" style="font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif;">
    <div class="dashboard-wrapper">
        <aside id="sidebar" class="dashboard-sidebar">
            <div class="sidebar-brand d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-store"></i>
                    <span class="fw-semibold">{{ config('app.name', 'Laravel') }}</span>
                </div>
                <button type="button" class="btn btn-link text-white sidebar-close d-lg-none" aria-label="{{ __('Close sidebar') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="sidebar-content">
                <nav class="sidebar-nav nav flex-column">
                    @include('layouts.partials.sidebar')
                </nav>
            </div>
        </aside>

        <div class="dashboard-main">
            <header class="dashboard-topbar navbar navbar-expand-lg">
                <div class="container-fluid">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary sidebar-toggle" aria-label="{{ __('Toggle sidebar') }}">
                            <i class="fas fa-bars"></i>
                        </button>
                        <a href="{{ route('dashboard') }}" class="navbar-brand d-none d-lg-flex align-items-center gap-2">
                            <i class="fas fa-gauge-high text-primary"></i>
                            <span class="fw-semibold">{{ __('Dashboard') }}</span>
                        </a>
                    </div>

                    @php($localizationService = $localization ?? app(\App\Services\LocalizationService::class))
                    @php($alternativeLocales = $localizationService->getAlternativeLocales())

                    @php($unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0)
                    @php($unreadNotifications = ($unreadNotifications ?? null) ?? (auth()->check() ? auth()->user()->unreadNotifications : collect()))
                    <div class="topbar-actions ms-auto d-flex align-items-center gap-2">
                        @if(count($alternativeLocales))
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span>{{ $localizationService->getCurrentFlag() }}</span>
                                    <span class="d-none d-md-inline">{{ $localizationService->getLocaleName($localizationService->getCurrentLocale()) }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                    @foreach($alternativeLocales as $locale => $data)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ $data['url'] ?? route('language.switch', $locale) }}">
                                                <span>{{ $data['flag'] }}</span>
                                                <span>{{ $data['name'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <button class="btn btn-outline-light d-flex align-items-center gap-2" type="button" id="themeToggle" aria-label="{{ __('Toggle dark mode') }}">
                            <i class="fas fa-moon" data-theme-icon="moon"></i>
                            <i class="fas fa-sun d-none" data-theme-icon="sun"></i>
                            <span class="d-none d-md-inline" data-theme-label-dark>{{ __('Dark Mode') }}</span>
                            <span class="d-none d-md-inline d-none" data-theme-label-light>{{ __('Light Mode') }}</span>
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @if($unreadCount)
                                    <span class="notification-dot">{{ $unreadCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown">
                                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                    <span class="fw-semibold">{{ __('Notifications') }}</span>
                                    @if($unreadCount)
                                        <button class="btn btn-link p-0 small" data-notification-mark-all="{{ route('notifications.read-all') }}">{{ __('Mark all as read') }}</button>
                                    @endif
                                </div>
                                <div class="notification-list">
                                    @forelse($unreadNotifications as $notification)
                                        <button type="button" class="dropdown-item text-start notification-item" data-notification-read="{{ route('notifications.read', $notification) }}" @if(isset($notification->data['url'])) data-notification-url="{{ $notification->data['url'] }}" @endif>
                                            <div class="d-flex gap-2">
                                                <span class="text-primary pt-1">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small fw-semibold">{{ $notification->data['title'] ?? __('Notification') }}</p>
                                                    <p class="mb-0 small text-muted">{{ $notification->data['message'] ?? __('You have a new notification.') }}</p>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </button>
                                    @empty
                                        <div class="px-3 py-4 text-center text-muted small">
                                            {{ __('You are all caught up!') }}
                                        </div>
                                    @endforelse
                                </div>
                                @if(Route::has('notifications.index'))
                                    <div class="border-top text-center">
                                        <a class="dropdown-item text-primary" href="{{ route('notifications.index') }}">{{ __('View all') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-outline-light d-flex align-items-center gap-2" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                                <span class="d-none d-md-inline fw-semibold">{{ trim(auth()->user()->first_name.' '.auth()->user()->last_name) ?: auth()->user()->email }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i>{{ __('Profile') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <main class="dashboard-content flex-grow-1">
                <div class="container-fluid py-4">
                    <div class="page-header d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-start align-items-lg-center mb-4">
                        <div>
                            <h1 class="page-title mb-2">@yield('title', __('Dashboard'))</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    @hasSection('breadcrumb')
                                        @yield('breadcrumb')
                                    @endif
                                </ol>
                            </nav>
                        </div>
                        @hasSection('actions')
                            <div class="page-actions d-flex flex-wrap gap-2">
                                @yield('actions')
                            </div>
                        @endif
                    </div>

                    @foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning'] as $flashKey => $flashType)
                        @if(session($flashKey))
                            <div class="alert alert-{{ $flashType }} alert-dismissible fade show" role="alert">
                                <i class="fas fa-circle-info me-2"></i>
                                {{ session($flashKey) }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                            </div>
                        @endif
                    @endforeach

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="sidebar-backdrop d-lg-none" aria-hidden="true"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
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
                    // Skip tables that have data-dt-init="false" (custom initialization)
                    if ($table.data('dt-init') === false) {
                        return;
                    }
                    $table.DataTable(options);
                    // Hide Laravel blade pagination near DataTables to avoid duplicate pagers
                    $table.closest('.card-body, .container, .table-responsive').find('.pagination').hide();
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
