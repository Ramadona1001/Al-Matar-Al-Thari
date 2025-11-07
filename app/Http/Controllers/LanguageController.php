<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Supported languages
     *
     * @var array
     */
    protected $supportedLanguages = [
        'en' => 'English',
        'ar' => 'العربية',
        'fr' => 'Français',
        'es' => 'Español',
    ];

    /**
     * Switch application language
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($locale)
    {
        // Validate the locale
        if (!array_key_exists($locale, $this->supportedLanguages)) {
            return redirect()->back()->with('error', __('Language not supported.'));
        }

        // Set the application locale
        App::setLocale($locale);
        
        // Store the locale in session
        Session::put('locale', $locale);
        
        // Store the locale in cookie for persistence
        return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
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
        
        if (in_array($locale, ['en', 'ar', 'fr', 'es'])) {
            App::setLocale($locale);
        }
    }
}