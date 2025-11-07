<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LocalizationService;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $localization = app(LocalizationService::class);
        
        // Check for locale in route parameter
        $locale = $request->route(config('localization.locale_route_parameter'));
        
        if ($locale && $localization->isValidLocale($locale)) {
            $localization->setLocale($locale);
        }

        // Share localization data with views
        view()->share('localization', $localization);
        view()->share('currentLocale', $localization->getCurrentLocale());
        view()->share('currentDirection', $localization->getCurrentDirection());
        view()->share('alternativeLocales', $localization->getAlternativeLocales());

        return $next($request);
    }
}