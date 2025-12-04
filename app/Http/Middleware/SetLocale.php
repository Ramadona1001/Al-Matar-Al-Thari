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
        $supportedLocales = config('localization.supported_locales', ['en']);
        $fallbackLocale = config('localization.fallback_locale', config('app.fallback_locale', 'en'));
        $sessionKey = config('localization.locale_session_key', 'locale');

        $locale = Session::get($sessionKey)
            ?? $request->cookie('locale')
            ?? null;

        if (!$locale && config('localization.detect_locale', false)) {
            $locale = $request->getPreferredLanguage($supportedLocales);
        }

        if (!$locale || !in_array($locale, $supportedLocales)) {
            $locale = config('localization.default_locale', config('app.locale'));
        }

        if (!in_array($locale, $supportedLocales)) {
            $locale = $fallbackLocale;
        }

        App::setLocale($locale);
        Session::put($sessionKey, $locale);

        $rtlLocales = ['ar', 'he', 'fa', 'ur'];
        $direction = in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';

        view()->share('currentLocale', $locale);
        view()->share('currentDirection', $direction);

        return $next($request);
    }
}