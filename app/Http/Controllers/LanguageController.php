<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LanguageController extends Controller
{
    /**
     * Supported languages
     *
     * @var array
     */
    protected array $supportedLanguages;

    public function __construct()
    {
        $locales = config('localization.supported_locales', ['en']);
        $names = config('localization.locale_names', []);

        $this->supportedLanguages = collect($locales)
            ->mapWithKeys(fn ($locale) => [$locale => $names[$locale] ?? ucfirst($locale)])
            ->toArray();
    }

    /**
     * Switch application language
     *
     * @param  string  $switchLocale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($switchLocale)
    {
        // Validate the locale
        if (!array_key_exists($switchLocale, $this->supportedLanguages)) {
            return redirect()->back()->with('error', __('Language not supported.'));
        }

        // Set the application locale
        App::setLocale($switchLocale);
        
        // Store the locale in session
        Session::put('locale', $switchLocale);
        
        // Get the current URL without locale prefix
        $currentUrl = request()->url();
        $currentPath = request()->path();
        
        // Remove current locale from path if exists
        $supportedLocales = config('localization.supported_locales', ['en']);
        foreach ($supportedLocales as $locale) {
            if (str_starts_with($currentPath, $locale . '/')) {
                $currentPath = substr($currentPath, strlen($locale) + 1);
                break;
            } elseif ($currentPath === $locale) {
                $currentPath = '';
                break;
            }
        }
        
        // Build new URL with selected locale
        $baseUrl = url('/');
        if (empty($currentPath)) {
            $redirectUrl = $baseUrl . '/' . $switchLocale;
        } else {
            $redirectUrl = $baseUrl . '/' . $switchLocale . '/' . $currentPath;
        }
        
        // Preserve query parameters
        if (request()->hasAny(request()->query())) {
            $redirectUrl .= '?' . http_build_query(request()->query());
        }
        
        // Store the locale in cookie for persistence
        return redirect($redirectUrl)->withCookie(cookie()->forever('locale', $switchLocale));
    }

    /**
     * Get available languages
     *
     * @return array
     */
    public function getAvailableLanguages()
    {
        return $this->supportedLanguages;
    }

    /**
     * Get current locale
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return App::getLocale();
    }

    /**
     * Set locale from session or cookie
     *
     * @return void
     */
    public static function setLocaleFromSession()
    {
        $locale = Session::get('locale', request()->cookie('locale', config('app.locale')));
        
        $supported = config('localization.supported_locales', ['en']);
        if (in_array($locale, $supported)) {
            App::setLocale($locale);
        }
    }
}