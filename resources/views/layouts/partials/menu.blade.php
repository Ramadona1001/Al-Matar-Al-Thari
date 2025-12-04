<nav class="navbar navbar-expand-lg main-navbar sticky-top d-none d-md-flex" aria-label="Main navigation">
        <div class="container">
            <!-- Logo -->
             <a href="{{ route('public.home') }}" class="navbar-brand" >
                @if(!empty($site->logo_path))
                    <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $brandName }}" style="width: 80px;">
                @else
                    <span class="text-white fw-bold">{{ $brandName }}</span>
                @endif
            </a>

            <!-- Centered Menu Items -->
            <ul class="navbar-nav mx-auto">
                @if($menus->count() > 0)
                    {!! renderMenuItems($menus) !!}
                @else
                    <li><a class="nav-link active" href="{{ route('public.home') }}">{{ __('Home') }}</a></li>
                    <li><a class="nav-link" href="{{ route('public.about') }}">{{ __('About') }}</a></li>
                    <li><a class="nav-link" href="{{ route('public.contact') }}">{{ __('Contact') }}</a></li>
                @endif
            </ul>

            <!-- Right Side - Language Switcher & Auth Buttons -->
            <div class="d-flex align-items-center gap-2">
                <!-- Language Switcher Dropdown -->
                <div class="lang-switcher dropdown">
                    <button class="lang-dropdown-btn dropdown-toggle" type="button" id="langDropdownDesktop"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true"
                        aria-label="Language selector">
                        <span id="currentLangTextDesktop">
                            <i class="bi bi-globe" aria-hidden="true"></i>
                            <span>{{ config('localization.locale_names.' . app()->getLocale(), 'English') }}</span>
                        </span>
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <ul class="lang-dropdown-menu dropdown-menu dropdown-menu-end"
                        aria-labelledby="langDropdownDesktop">
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
                                   href="{{ $localeUrl }}" style="color: var(--brand-primary)"
                                   data-lang="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" 
                                   data-text="{{ $localeName }}">
                                    <i class="bi bi-translate" aria-hidden="true"></i>
                                    <span>{{ $localeName }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @auth
                    <!-- User Menu (if logged in) -->
                    <div class="dropdown">
                        <button class="btn btn-get-started dropdown-toggle" type="button" id="userMenuDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
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
                        <button class="btn btn-get-started dropdown-toggle" type="button" id="authMenuDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ __('Account') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authMenuDropdown">
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
            </div>
        </div>
    </nav>