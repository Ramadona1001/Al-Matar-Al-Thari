<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

@php
    try {
        $site = \App\Models\SiteSetting::getSettings();
    } catch (\Exception $e) {
        $site = new \App\Models\SiteSetting();
    }

    try {
        $menus =
            class_exists(\App\Models\Menu::class) && \Schema::hasTable('menus')
                ? \App\Models\Menu::where('name','header')
                    ->active()
                    ->rootItems()
                    ->ordered()
                    ->get()
                : collect();
    } catch (\Exception $e) {
        $menus = collect();
    }

    try {
        // Load Footer Menu Groups
        $footerMenuGroups =
            class_exists(\App\Models\FooterMenuGroup::class) && \Schema::hasTable('footer_menu_groups')
                ? \App\Models\FooterMenuGroup::active()
                    ->ordered()
                    ->with(['menuItems'])
                    ->get()
                : collect();
        
        // Fallback to old footer menus if no groups exist
        $footerMenus =
            class_exists(\App\Models\Menu::class) && \Schema::hasTable('menus')
                ? \App\Models\Menu::forMenu('footer')
                    ->active()
                    ->rootItems()
                    ->ordered()
                    ->get()
                : collect();
    } catch (\Exception $e) {
        $footerMenuGroups = collect();
        $footerMenus = collect();
    }

    try {
        $servicesMenu =
            class_exists(\App\Models\Menu::class) && \Schema::hasTable('menus')
                ? \App\Models\Menu::forMenu('services')
                    ->active()
                    ->rootItems()
                    ->ordered()
                    ->get()
                : collect();
    } catch (\Exception $e) {
        $servicesMenu = collect();
    }

    // Build social links array
    $socialLinks = [];
    try {
        if (isset($site->social_links) && is_array($site->social_links) && !empty($site->social_links)) {
            $socialLinks = $site->social_links;
        } else {
            if (isset($site->facebook_url) && $site->facebook_url) {
                $socialLinks['facebook'] = $site->facebook_url;
            }
            if (isset($site->twitter_url) && $site->twitter_url) {
                $socialLinks['twitter'] = $site->twitter_url;
            }
            if (isset($site->instagram_url) && $site->instagram_url) {
                $socialLinks['instagram'] = $site->instagram_url;
            }
            if (isset($site->linkedin_url) && $site->linkedin_url) {
                $socialLinks['linkedin'] = $site->linkedin_url;
            }
            if (isset($site->youtube_url) && $site->youtube_url) {
                $socialLinks['youtube'] = $site->youtube_url;
            }
            if (isset($site->tiktok_url) && $site->tiktok_url) {
                $socialLinks['tiktok'] = $site->tiktok_url;
            }
        }
    } catch (\Exception $e) {
        $socialLinks = [];
    }

    // Helper function to render menu items recursively
    if (!function_exists('renderMenuItems')) {
        function renderMenuItems($items, $level = 0)
        {
            $html = '';
            $currentLocale = app()->getLocale();
            $currentRoute = request()->route() ? request()->route()->getName() : null;
            $currentUrl = request()->url();
            $currentPath = request()->path();
            
            foreach ($items as $item) {
                $children = $item->children()->active()->ordered()->get();
                $hasChildren = $children->count() > 0;
                
                // Get translated label
                $label = '';
                if (is_object($item) && method_exists($item, 'translate')) {
                    $itemTranslation = $item->translate($currentLocale);
                    $itemEn = $item->translate('en');
                    $label = ($itemTranslation && isset($itemTranslation->label) && $itemTranslation->label) 
                        ? $itemTranslation->label 
                        : (($itemEn && isset($itemEn->label) && $itemEn->label) ? $itemEn->label : '');
                }
                if (empty($label)) {
                    $label = $item->label ?? '';
                }
                
                // Check if menu item is active
                $isActive = false;
                if ($item->route) {
                    // Check if current route matches menu route
                    if ($currentRoute) {
                        // Exact match
                        if ($currentRoute === $item->route) {
                            $isActive = true;
                        }
                        // Pattern match (e.g., 'public.blog.*' matches 'public.blog.show')
                        elseif (str_ends_with($item->route, '.*')) {
                            $routePattern = str_replace('.*', '', $item->route);
                            if (str_starts_with($currentRoute, $routePattern . '.')) {
                                $isActive = true;
                            }
                        }
                        // Check if current route starts with item route (for parent routes)
                        elseif (str_starts_with($currentRoute, $item->route . '.')) {
                            $isActive = true;
                        }
                    }
                } else {
                    // Check URL match
                    $itemUrl = $item->url ?? '#';
                    if ($itemUrl !== '#' && $itemUrl !== '') {
                        try {
                            // Try to get full URL from route if it's a route name
                            $itemFullUrl = $item->full_url;
                            
                            // Parse URLs
                            $itemPath = parse_url($itemFullUrl, PHP_URL_PATH);
                            $currentPathFull = parse_url($currentUrl, PHP_URL_PATH);
                            
                            // Remove locale prefix from both paths for comparison
                            $supportedLocales = config('localization.supported_locales', ['en', 'ar']);
                            foreach ($supportedLocales as $locale) {
                                $localePrefix = '/' . $locale;
                                if (str_starts_with($itemPath, $localePrefix . '/')) {
                                    $itemPath = substr($itemPath, strlen($localePrefix));
                                } elseif ($itemPath === $localePrefix) {
                                    $itemPath = '/';
                                }
                                if (str_starts_with($currentPathFull, $localePrefix . '/')) {
                                    $currentPathFull = substr($currentPathFull, strlen($localePrefix));
                                } elseif ($currentPathFull === $localePrefix) {
                                    $currentPathFull = '/';
                                }
                            }
                            
                            // Normalize paths
                            $itemPath = rtrim($itemPath, '/') ?: '/';
                            $currentPathFull = rtrim($currentPathFull, '/') ?: '/';
                            
                            // Exact path match
                            if ($itemPath === $currentPathFull) {
                                $isActive = true;
                            }
                            // Check if current path starts with item path (for parent pages)
                            elseif ($itemPath !== '/' && str_starts_with($currentPathFull, $itemPath . '/')) {
                                $isActive = true;
                            }
                        } catch (\Exception $e) {
                            // Fallback: simple string comparison
                            if (str_contains($currentUrl, $itemUrl)) {
                                $isActive = true;
                            }
                        }
                    }
                }
                
                $target = $item->open_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
                $activeClass = $isActive ? ' active' : '';
                
                $html .= '<li class="nav-item' . ($hasChildren ? ' dropdown' : '') . ($isActive ? ' active' : '') . '">';
                if ($hasChildren) {
                    $html .= '<a class="nav-link dropdown-toggle' . $activeClass . '" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . e($label) . '</a>';
                    $html .= '<ul class="dropdown-menu">';
                    $html .= renderMenuItems($children, $level + 1);
                    $html .= '</ul>';
                } else {
                    $html .= '<a class="nav-link' . $activeClass . '" href="' . e($item->full_url) . '"' . $target . '>' . e($label) . '</a>';
                }
                $html .= '</li>';
            }
            return $html;
        }
    }

    // Get localized values
    $currentLocale = app()->getLocale();
    $metaTitle = is_array($site->meta_title)
        ? $site->meta_title[$currentLocale] ?? reset($site->meta_title)
        : $site->meta_title ?? '';
    $metaDescription = is_array($site->meta_description)
        ? $site->meta_description[$currentLocale] ?? reset($site->meta_description)
        : $site->meta_description ?? '';
    $metaKeywords = is_array($site->meta_keywords)
        ? $site->meta_keywords[$currentLocale] ?? reset($site->meta_keywords)
        : $site->meta_keywords ?? '';
    $brandName = is_array($site->brand_name)
        ? $site->brand_name[$currentLocale] ?? reset($site->brand_name)
        : $site->brand_name ?? config('app.name');
    $contactAddress = is_array($site->contact_address)
        ? $site->contact_address[$currentLocale] ?? reset($site->contact_address)
        : $site->contact_address ?? '';
    $footerText = is_array($site->footer_text)
        ? $site->footer_text[$currentLocale] ?? reset($site->footer_text)
        : $site->footer_text ?? '';
    $footerCopyright = is_array($site->footer_copyright)
        ? $site->footer_copyright[$currentLocale] ?? reset($site->footer_copyright)
        : $site->footer_copyright ?? __('Copyright');
@endphp
@include('layouts.partials.head')

<body>

    <!-- Preloader -->
    {{-- <div class="preloader" id="preloader">
        <div class="preloader-logo">
            <i class="bi bi-cloud-rain"></i>
        </div>
        <div class="preloader-spinner"></div>
        <div class="preloader-text">Loading...</div>
    </div> --}}
    
    @include('layouts.partials.topnav')
    
    @include('layouts.partials.menu')
    

    @include('layouts.partials.mobile-menu')

    <!-- Main Content -->
    @yield('content')

    @include('layouts.partials.footer')

    @include('layouts.partials.scripts')
</body>

</html>
