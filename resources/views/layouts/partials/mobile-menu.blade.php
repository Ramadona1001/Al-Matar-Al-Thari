<!-- Mobile Header (Mobile Only) -->
    <div class="mobile-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <a href="{{ route('public.home') }}" class="mobile-logo text-decoration-none">
                    @if(!empty($site->logo_path))
                        <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $brandName }}" style="height: 30px;">
                    @else
                        <i class="bi bi-cloud-rain" aria-hidden="true"></i>
                        <span>{{ $brandName }}</span>
                    @endif
                </a>

                <div class="d-flex align-items-center gap-2">
                    <!-- Mobile Menu Toggle Button -->
                    @if($menus && $menus->count() > 0)
                        <button class="mobile-lang-btn mobile-menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuOffcanvas" aria-controls="mobileMenuOffcanvas" aria-label="{{ __('Menu') }}" title="{{ __('Menu') }}">
                            <i class="bi bi-list" style="font-size: 1.2rem;"></i>
                        </button>
                    @endif
                    @auth
                        <!-- User Menu (if logged in) -->
                        <div class="dropdown">
                            <button class="mobile-lang-btn dropdown-toggle" type="button" id="mobileUserMenuDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileUserMenuDropdown">
                                <li><a class="dropdown-item" href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}">
                                    <i class="bi bi-speedometer2 me-2"></i>{{ __('Dashboard') }}
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Auth Dropdown (Login & Register) -->
                        <div class="dropdown">
                            <button class="mobile-lang-btn dropdown-toggle" type="button" id="mobileAuthMenuDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileAuthMenuDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('login', ['locale' => app()->getLocale()]) }}" style="color:var(--brand-primary)">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        {{ __('Login') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('register', ['locale' => app()->getLocale()]) }}" style="color:var(--brand-primary)">
                                        <i class="bi bi-person-plus me-2"></i>
                                        {{ __('Register') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endauth

                    <!-- Language Dropdown -->
                    <div class="mobile-lang dropdown">
                        <button class="mobile-lang-btn dropdown-toggle" type="button" id="langDropdownMobile"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true"
                            aria-label="Language selector">
                            <i class="bi bi-globe" aria-hidden="true"></i>
                            <span id="currentLangTextMobile">{{ strtoupper(app()->getLocale()) }}</span>
                            <i class="bi bi-chevron-down" style="font-size: 0.6rem;" aria-hidden="true"></i>
                        </button>
                        <ul class="lang-dropdown-menu dropdown-menu dropdown-menu-end"
                            aria-labelledby="langDropdownMobile">
                            @foreach(config('localization.supported_locales', ['en']) as $locale)
                                @php
                                    $localeName = config('localization.locale_names.' . $locale, ucfirst($locale));
                                    $isCurrent = app()->getLocale() === $locale;
                                    
                                    // Get current path without locale
                                    $currentPath = request()->path();
                                    $supportedLocales = config('localization.supported_locales', ['en']);
                                    foreach ($supportedLocales as $loc) {
                                        if (str_starts_with($currentPath, $loc . '/')) {
                                            $currentPath = substr($currentPath, strlen($loc) + 1);
                                            break;
                                        } elseif ($currentPath === $loc) {
                                            $currentPath = '';
                                            break;
                                        }
                                    }
                                    
                                    // Build URL with new locale
                                    if (empty($currentPath)) {
                                        $localeUrl = url('/' . $locale);
                                    } else {
                                        $localeUrl = url('/' . $locale . '/' . $currentPath);
                                    }
                                    
                                    // Preserve query parameters
                                    if (request()->hasAny(request()->query())) {
                                        $localeUrl .= '?' . http_build_query(request()->query());
                                    }
                                @endphp
                                <li>
                                    <a class="lang-dropdown-item {{ $isCurrent ? 'active' : '' }}" 
                                       href="{{ $localeUrl }}"
                                       data-lang="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" 
                                       data-text="{{ strtoupper($locale) }}">
                                        <i class="bi bi-translate" aria-hidden="true"></i>
                                        <span>{{ $localeName }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Offcanvas (Sidebar) -->
    @if($menus && $menus->count() > 0)
        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileMenuOffcanvasLabel">{{ __('Menu') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="offcanvas-body">
                <nav class="mobile-menu-nav">
                    <ul class="list-unstyled mb-0">
                        @php
                            $currentLocale = app()->getLocale();
                        @endphp
                        @foreach($menus as $menu)
                            @php
                                $children = $menu->children()->active()->ordered()->get();
                                $hasChildren = $children->count() > 0;
                                
                                // Get translated label
                                $label = '';
                                if (method_exists($menu, 'translate')) {
                                    $menuTranslation = $menu->translate($currentLocale);
                                    $menuEn = $menu->translate('en');
                                    $label = ($menuTranslation && isset($menuTranslation->label) && $menuTranslation->label) 
                                        ? $menuTranslation->label 
                                        : (($menuEn && isset($menuEn->label) && $menuEn->label) ? $menuEn->label : '');
                                }
                                if (empty($label)) {
                                    $label = $menu->label ?? '';
                                }
                            @endphp
                            <li class="mobile-menu-item {{ $hasChildren ? 'has-children' : '' }}">
                                @if($hasChildren)
                                    <a href="#" class="mobile-menu-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#mobileSubmenu{{ $menu->id }}" aria-expanded="false">
                                        <span>{{ $label }}</span>
                                        <i class="bi bi-chevron-down"></i>
                                    </a>
                                    <div class="collapse" id="mobileSubmenu{{ $menu->id }}">
                                        <ul class="list-unstyled ps-3 mt-2">
                                            @foreach($children as $child)
                                                @php
                                                    $childLabel = '';
                                                    if (method_exists($child, 'translate')) {
                                                        $childTranslation = $child->translate($currentLocale);
                                                        $childEn = $child->translate('en');
                                                        $childLabel = ($childTranslation && isset($childTranslation->label) && $childTranslation->label) 
                                                            ? $childTranslation->label 
                                                            : (($childEn && isset($childEn->label) && $childEn->label) ? $childEn->label : '');
                                                    }
                                                    if (empty($childLabel)) {
                                                        $childLabel = $child->label ?? '';
                                                    }
                                                @endphp
                                                <li class="mobile-menu-item">
                                                    <a href="{{ $child->full_url }}" class="mobile-menu-link" target="{{ $child->open_in_new_tab ? '_blank' : '_self' }}">
                                                        {{ $childLabel }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <a href="{{ $menu->full_url }}" class="mobile-menu-link" target="{{ $menu->open_in_new_tab ? '_blank' : '_self' }}">
                                        {{ $label }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    @endif

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="mobile-bottom-nav" aria-label="Mobile navigation">
        <div class="nav-container">
            @if($menus && $menus->count() > 0)
                @php
                    $homeMenu = $menus->firstWhere('route', 'public.home') ?? $menus->first();
                    $offersMenu = $menus->firstWhere('route', 'public.offers.index');
                    $companiesMenu = $menus->firstWhere('route', 'public.companies.index');
                    $featuresMenu = $menus->firstWhere('route', 'public.features');
                @endphp
                
                <a href="{{ route('public.home') }}" class="nav-item {{ request()->routeIs('public.home') ? 'active' : '' }}" data-page="home" aria-current="page">
                    <i class="bi bi-house-door" aria-hidden="true"></i>
                    <span>{{ __('Home') }}</span>
                </a>
                
                @if($offersMenu)
                    <a href="{{ $offersMenu->full_url }}" class="nav-item {{ request()->routeIs('public.offers.*') ? 'active' : '' }}" data-page="offers">
                        <i class="bi bi-tag" aria-hidden="true"></i>
                        <span>{{ $offersMenu->translate(app()->getLocale())->label ?? __('Offers') }}</span>
                    </a>
                @else
                    <a href="{{ route('public.offers.index') }}" class="nav-item {{ request()->routeIs('public.offers.*') ? 'active' : '' }}" data-page="offers">
                        <i class="bi bi-tag" aria-hidden="true"></i>
                        <span>{{ __('Offers') }}</span>
                    </a>
                @endif
                
                @if($companiesMenu)
                    <a href="{{ $companiesMenu->full_url }}" class="nav-item {{ request()->routeIs('public.companies.*') ? 'active' : '' }}" data-page="companies">
                        <i class="bi bi-building" aria-hidden="true"></i>
                        <span>{{ $companiesMenu->translate(app()->getLocale())->label ?? __('Companies') }}</span>
                    </a>
                @else
                    <a href="{{ route('public.companies.index') }}" class="nav-item {{ request()->routeIs('public.companies.*') ? 'active' : '' }}" data-page="companies">
                        <i class="bi bi-building" aria-hidden="true"></i>
                        <span>{{ __('Companies') }}</span>
                    </a>
                @endif
                
                @if($featuresMenu)
                    <a href="{{ $featuresMenu->full_url }}" class="nav-item {{ request()->routeIs('public.features') ? 'active' : '' }}" data-page="features">
                        <i class="bi bi-star" aria-hidden="true"></i>
                        <span>{{ $featuresMenu->translate(app()->getLocale())->label ?? __('Features') }}</span>
                    </a>
                @else
                    <a href="{{ route('public.features') }}" class="nav-item {{ request()->routeIs('public.features') ? 'active' : '' }}" data-page="features">
                        <i class="bi bi-star" aria-hidden="true"></i>
                        <span>{{ __('Features') }}</span>
                    </a>
                @endif
            @else
                <!-- Fallback to static menu if no dynamic menus -->
                <a href="{{ route('public.home') }}" class="nav-item {{ request()->routeIs('public.home') ? 'active' : '' }}" data-page="home" aria-current="page">
                    <i class="bi bi-house-door" aria-hidden="true"></i>
                    <span>{{ __('Home') }}</span>
                </a>
                <a href="{{ route('public.offers.index') }}" class="nav-item {{ request()->routeIs('public.offers.*') ? 'active' : '' }}" data-page="offers">
                    <i class="bi bi-tag" aria-hidden="true"></i>
                    <span>{{ __('Offers') }}</span>
                </a>
                <a href="{{ route('public.companies.index') }}" class="nav-item {{ request()->routeIs('public.companies.*') ? 'active' : '' }}" data-page="companies">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    <span>{{ __('Companies') }}</span>
                </a>
                <a href="{{ route('public.features') }}" class="nav-item {{ request()->routeIs('public.features') ? 'active' : '' }}" data-page="features">
                    <i class="bi bi-star" aria-hidden="true"></i>
                    <span>{{ __('Features') }}</span>
                </a>
            @endif
            
            @auth
                <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-page="account">
                    <i class="bi bi-person" aria-hidden="true"></i>
                    <span>{{ __('Account') }}</span>
                </a>
            @else
                <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}" data-page="account">
                    <i class="bi bi-person" aria-hidden="true"></i>
                    <span>{{ __('Login') }}</span>
                </a>
            @endauth
        </div>
    </nav>

    <style>
        /* Mobile Menu Styles */
        .mobile-menu-nav .mobile-menu-item {
            border-bottom: 1px solid #e9ecef;
        }
        
        .mobile-menu-nav .mobile-menu-item:last-child {
            border-bottom: none;
        }
        
        .mobile-menu-nav .mobile-menu-link {
            display: block;
            padding: 0.75rem 1rem;
            color: #212529;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .mobile-menu-nav .mobile-menu-link:hover,
        .mobile-menu-nav .mobile-menu-link:focus {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        
        .mobile-menu-nav .mobile-menu-item.has-children .mobile-menu-link {
            font-weight: 500;
        }
        
        .mobile-menu-nav .collapse .mobile-menu-link {
            padding-left: 1.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .mobile-menu-nav .collapse .mobile-menu-link:hover {
            color: #0d6efd;
        }
        
        .mobile-menu-nav .bi-chevron-down {
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }
        
        .mobile-menu-nav .mobile-menu-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        
        /* Responsive improvements for mobile menu */
        @media (max-width: 575.98px) {
            .mobile-header {
                padding: 0.5rem 0;
            }
            
            .mobile-header .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .mobile-lang-btn {
                padding: 0.35rem 0.6rem;
                font-size: 0.75rem;
            }
            
            .mobile-menu-nav .mobile-menu-link {
                padding: 0.6rem 0.75rem;
                font-size: 0.9rem;
            }
            
            .mobile-menu-nav .collapse .mobile-menu-link {
                padding-left: 1.25rem;
                font-size: 0.85rem;
            }
            
            .offcanvas {
                max-width: 85%;
            }
        }
        
        /* Fix offcanvas for RTL */
        [dir="rtl"] .offcanvas-start {
            right: 0;
            left: auto;
        }
        
        /* Ensure mobile bottom nav doesn't overlap content */
        @media (max-width: 767.98px) {
            body {
                padding-bottom: var(--bottom-nav-height, 70px) !important;
            }
            
            /* Fix for safe area on iOS */
            .mobile-bottom-nav {
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
        
        /* Fix for very small screens */
        @media (max-width: 360px) {
            .mobile-bottom-nav .nav-item span {
                font-size: 0.55rem;
            }
            
            .mobile-bottom-nav .nav-item i {
                font-size: 1.1rem;
            }
        }
        
        /* Ensure mobile menu toggle button is visible */
        .mobile-menu-toggle {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Make sure mobile header is visible on mobile */
        @media (max-width: 767.98px) {
            .mobile-header {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            .mobile-bottom-nav {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }
    </style>