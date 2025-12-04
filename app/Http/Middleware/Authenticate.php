<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Ensure locale is provided for localized auth routes
        $locale = session(config('localization.locale_session_key'))
            ?? app()->getLocale()
            ?? config('localization.default_locale', 'en');

        return route('login', ['locale' => $locale]);
    }
}
