<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for locale in session first
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } 
        // Then check for locale in cookie
        elseif ($request->hasCookie('locale')) {
            $locale = $request->cookie('locale');
        } 
        // Finally, use the default locale from config
        else {
            $locale = config('app.locale');
        }

        // Validate the locale is supported
        $supportedLocales = ['en', 'ar', 'fr', 'es'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Set the application locale
        App::setLocale($locale);

        // Set the direction based on locale (RTL for Arabic)
        $direction = ($locale === 'ar') ? 'rtl' : 'ltr';
        App::setLocale($locale);
        
        // Share locale and direction with all views
        view()->share('currentLocale', $locale);
        view()->share('currentDirection', $direction);

        return $next($request);
    }
}