<?php

use App\Services\LocalizationService;

if (!function_exists('localize')) {
    /**
     * Get localization service instance.
     */
    function localize(): LocalizationService
    {
        return app(LocalizationService::class);
    }
}

if (!function_exists('current_locale')) {
    /**
     * Get current locale.
     */
    function current_locale(): string
    {
        return localize()->getCurrentLocale();
    }
}

if (!function_exists('current_direction')) {
    /**
     * Get current text direction.
     */
    function current_direction(): string
    {
        return localize()->getCurrentDirection();
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL.
     */
    function is_rtl(): bool
    {
        return localize()->isRtl();
    }
}

if (!function_exists('is_ltr')) {
    /**
     * Check if current locale is LTR.
     */
    function is_ltr(): bool
    {
        return localize()->isLtr();
    }
}

if (!function_exists('localized_url')) {
    /**
     * Get localized URL.
     */
    function localized_url(string $locale, string $path = null): string
    {
        return localize()->getLocalizedUrl($locale, $path);
    }
}

if (!function_exists('alternative_locales')) {
    /**
     * Get alternative locales for current page.
     */
    function alternative_locales(): array
    {
        return localize()->getAlternativeLocales();
    }
}

if (!function_exists('locale_name')) {
    /**
     * Get locale name.
     */
    function locale_name(string $locale): string
    {
        return localize()->getLocaleName($locale);
    }
}

if (!function_exists('locale_flag')) {
    /**
     * Get locale flag emoji.
     */
    function locale_flag(string $locale): string
    {
        return localize()->getLocaleFlag($locale);
    }
}

if (!function_exists('current_flag')) {
    /**
     * Get current locale flag emoji.
     */
    function current_flag(): string
    {
        return localize()->getCurrentFlag();
    }
}

if (!function_exists('trans_with_fallback')) {
    /**
     * Translate with fallback locale.
     */
    function trans_with_fallback(string $key, array $replace = [], ?string $locale = null): string
    {
        return localize()->translateWithFallback($key, $replace, $locale);
    }
}

if (!function_exists('localized_date')) {
    /**
     * Get localized date.
     */
    function localized_date($date, string $format = null, ?string $locale = null): string
    {
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }

        return localize()->getLocalizedDate($date, $format, $locale);
    }
}

if (!function_exists('localized_number')) {
    /**
     * Get localized number.
     */
    function localized_number($number, int $decimals = 0, ?string $locale = null): string
    {
        return localize()->getLocalizedNumber($number, $decimals, $locale);
    }
}

if (!function_exists('localized_currency')) {
    /**
     * Get localized currency.
     */
    function localized_currency(float $amount, string $currency = 'SAR', ?string $locale = null): string
    {
        return localize()->getLocalizedCurrency($amount, $currency, $locale);
    }
}

if (!function_exists('switch_locale')) {
    /**
     * Switch locale and redirect back.
     */
    function switch_locale(string $locale): \Illuminate\Http\RedirectResponse
    {
        if (!localize()->isValidLocale($locale)) {
            return back()->with('error', __('Invalid locale.'));
        }

        localize()->setLocale($locale);
        
        return back()->with('success', __('Language changed successfully.'));
    }
}

if (!function_exists('locale_selector')) {
    /**
     * Generate locale selector HTML.
     */
    function locale_selector(array $options = []): string
    {
        $currentLocale = current_locale();
        $alternatives = alternative_locales();
        
        $type = $options['type'] ?? 'dropdown'; // dropdown, buttons, links
        $showFlags = $options['show_flags'] ?? true;
        $showNames = $options['show_names'] ?? true;
        $class = $options['class'] ?? '';

        $html = '';

        if ($type === 'dropdown') {
            $html .= '<div class="dropdown locale-selector ' . $class . '">';
            $html .= '<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">';
            if ($showFlags) {
                $html .= current_flag() . ' ';
            }
            if ($showNames) {
                $html .= locale_name($currentLocale);
            }
            $html .= '</button>';
            $html .= '<ul class="dropdown-menu">';
            
            foreach ($alternatives as $locale => $data) {
                $html .= '<li>';
                $html .= '<a class="dropdown-item" href="' . localized_url($locale) . '">';
                if ($showFlags) {
                    $html .= $data['flag'] . ' ';
                }
                if ($showNames) {
                    $html .= $data['name'];
                }
                $html .= '</a>';
                $html .= '</li>';
            }
            
            $html .= '</ul>';
            $html .= '</div>';
        } elseif ($type === 'buttons') {
            $html .= '<div class="btn-group locale-selector ' . $class . '" role="group">';
            
            foreach (array_merge([$currentLocale => [
                'name' => locale_name($currentLocale),
                'flag' => current_flag(),
                'url' => '#'
            ]], $alternatives) as $locale => $data) {
                $active = $locale === $currentLocale ? ' active' : '';
                $html .= '<a href="' . ($locale === $currentLocale ? '#' : localized_url($locale)) . '" ';
                $html .= 'class="btn btn-outline-secondary' . $active . '">';
                if ($showFlags) {
                    $html .= $data['flag'] . ' ';
                }
                if ($showNames) {
                    $html .= $data['name'];
                }
                $html .= '</a>';
            }
            
            $html .= '</div>';
        }

        return $html;
    }
}