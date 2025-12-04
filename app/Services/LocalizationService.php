<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class LocalizationService
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales;

    /**
     * Current locale.
     */
    protected string $currentLocale;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->supportedLocales = config('localization.supported_locales', ['en']);
        $this->currentLocale = App::getLocale();
    }

    /**
     * Get supported locales.
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * Get current locale.
     */
    public function getCurrentLocale(): string
    {
        return $this->currentLocale;
    }

    /**
     * Set locale.
     */
    public function setLocale(string $locale): bool
    {
        if (!$this->isValidLocale($locale)) {
            return false;
        }

        App::setLocale($locale);
        Session::put(config('localization.locale_session_key'), $locale);
        
        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        $this->currentLocale = $locale;

        return true;
    }

    /**
     * Check if locale is valid.
     */
    public function isValidLocale(string $locale): bool
    {
        return in_array($locale, $this->supportedLocales);
    }

    /**
     * Get default locale.
     */
    public function getDefaultLocale(): string
    {
        return config('localization.default_locale', 'en');
    }

    /**
     * Get fallback locale.
     */
    public function getFallbackLocale(): string
    {
        return config('localization.fallback_locale', 'en');
    }

    /**
     * Get locale name.
     */
    public function getLocaleName(string $locale): string
    {
        $names = config('localization.locale_names', []);

        return $names[$locale] ?? ucfirst($locale);
    }

    /**
     * Get locale direction.
     */
    public function getLocaleDirection(string $locale): string
    {
        $rtlLocales = ['ar', 'he', 'fa', 'ur'];
        
        return in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';
    }

    /**
     * Get current locale direction.
     */
    public function getCurrentDirection(): string
    {
        return $this->getLocaleDirection($this->currentLocale);
    }

    /**
     * Check if current locale is RTL.
     */
    public function isRtl(): bool
    {
        return $this->getCurrentDirection() === 'rtl';
    }

    /**
     * Check if current locale is LTR.
     */
    public function isLtr(): bool
    {
        return $this->getCurrentDirection() === 'ltr';
    }

    /**
     * Get locale flag emoji.
     */
    public function getLocaleFlag(string $locale): string
    {
        $flags = config('localization.locale_flags', []);

        return $flags[$locale] ?? '🏳️';
    }

    /**
     * Get current locale flag.
     */
    public function getCurrentFlag(): string
    {
        return $this->getLocaleFlag($this->currentLocale);
    }

    /**
     * Get localized URL.
     */
    public function getLocalizedUrl(string $locale, string $path = null): string
    {
        $path = $path ?? request()->path();
        
        // Remove existing locale from path
        $currentLocale = $this->getCurrentLocale();
        if (str_starts_with($path, $currentLocale . '/')) {
            $path = substr($path, strlen($currentLocale) + 1);
        }

        // Build localized URL
        $localizedPath = $locale . '/' . ltrim($path, '/');
        
        return url($localizedPath);
    }

    /**
     * Get alternative locales for current page.
     */
    public function getAlternativeLocales(): array
    {
        $alternatives = [];
        $currentPath = request()->path();
        
        foreach ($this->supportedLocales as $locale) {
            if ($locale === $this->currentLocale) {
                continue;
            }
            
            $alternatives[$locale] = [
                'name' => $this->getLocaleName($locale),
                'flag' => $this->getLocaleFlag($locale),
                'direction' => $this->getLocaleDirection($locale),
                'url' => $this->getLocalizedUrl($locale),
            ];
        }

        return $alternatives;
    }

    /**
     * Translate with fallback.
     */
    public function translateWithFallback(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->currentLocale;
        
        // Try to translate in requested locale
        $translation = trans($key, $replace, $locale);
        
        // If translation is missing, try fallback locale
        if ($translation === $key && $locale !== $this->getFallbackLocale()) {
            $translation = trans($key, $replace, $this->getFallbackLocale());
        }

        return $translation;
    }

    /**
     * Get localized date.
     */
    public function getLocalizedDate(\Carbon\Carbon $date, string $format = null, ?string $locale = null): string
    {
        $locale = $locale ?? $this->currentLocale;
        $format = $format ?? ($locale === 'ar' ? 'd/m/Y' : 'Y-m-d');
        
        return $date->locale($locale)->format($format);
    }

    /**
     * Get localized number.
     */
    public function getLocalizedNumber($number, int $decimals = 0, ?string $locale = null): string
    {
        $locale = $locale ?? $this->currentLocale;
        
        if ($locale === 'ar') {
            // Use Arabic numerals
            $westernArabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $easternArabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            
            $formatted = number_format($number, $decimals);
            return str_replace($westernArabic, $easternArabic, $formatted);
        }

        return number_format($number, $decimals);
    }

    /**
     * Get localized currency.
     */
    public function getLocalizedCurrency(float $amount, string $currency = 'SAR', ?string $locale = null): string
    {
        $locale = $locale ?? $this->currentLocale;
        
        if ($locale === 'ar') {
            return $this->getLocalizedNumber($amount, 2) . ' ر.س';
        }

        return $currency . ' ' . number_format($amount, 2);
    }
}