<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('localization', function ($app) {
            return new \App\Services\LocalizationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->setApplicationLocale();
    }

    /**
     * Set the application locale based on various factors.
     */
    protected function setApplicationLocale(): void
    {
        $locale = $this->determineLocale();
        
        App::setLocale($locale);
        
        // Set locale for Carbon dates
        if (class_exists('Carbon\Carbon')) {
            \Carbon\Carbon::setLocale($locale);
        }
    }

    /**
     * Determine the appropriate locale.
     */
    protected function determineLocale(): string
    {
        // 1. Check for locale in route parameter
        if (request()->route(config('localization.locale_route_parameter'))) {
            $locale = request()->route(config('localization.locale_route_parameter'));
            if ($this->isValidLocale($locale)) {
                Session::put(config('localization.locale_session_key'), $locale);
                return $locale;
            }
        }

        // 2. Check for locale in session
        if (Session::has(config('localization.locale_session_key'))) {
            $locale = Session::get(config('localization.locale_session_key'));
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // 3. Check for locale in user preferences (if authenticated)
        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
            if ($this->isValidLocale($locale)) {
                Session::put(config('localization.locale_session_key'), $locale);
                return $locale;
            }
        }

        // 4. Auto-detect from browser headers (if enabled)
        if (config('localization.detect_locale') && request()->server('HTTP_ACCEPT_LANGUAGE')) {
            $locale = $this->detectBrowserLocale();
            if ($locale && $this->isValidLocale($locale)) {
                Session::put(config('localization.locale_session_key'), $locale);
                return $locale;
            }
        }

        // 5. Return default locale
        return config('localization.default_locale', 'en');
    }

    /**
     * Check if locale is valid.
     */
    protected function isValidLocale(string $locale): bool
    {
        return in_array($locale, config('localization.supported_locales', ['en']));
    }

    /**
     * Detect browser locale from Accept-Language header.
     */
    protected function detectBrowserLocale(): ?string
    {
        $acceptLanguage = request()->server('HTTP_ACCEPT_LANGUAGE');
        
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        $languagePairs = explode(',', $acceptLanguage);
        
        foreach ($languagePairs as $pair) {
            $parts = explode(';', $pair);
            $language = trim($parts[0]);
            $quality = isset($parts[1]) ? (float) str_replace('q=', '', $parts[1]) : 1.0;
            $languages[$language] = $quality;
        }

        // Sort by quality
        arsort($languages);

        // Find first supported locale
        foreach ($languages as $locale => $quality) {
            // Check exact match
            if ($this->isValidLocale($locale)) {
                return $locale;
            }

            // Check language-only match (e.g., 'ar' for 'ar-SA')
            $languageCode = explode('-', $locale)[0];
            if ($this->isValidLocale($languageCode)) {
                return $languageCode;
            }
        }

        return null;
    }
}