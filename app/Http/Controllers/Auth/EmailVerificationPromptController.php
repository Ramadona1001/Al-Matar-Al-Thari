<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            $locale = session(config('localization.locale_session_key'))
                ?? $request->route('locale')
                ?? app()->getLocale()
                ?? config('localization.default_locale', 'en');

            return redirect()->intended(route('dashboard', ['locale' => $locale]));
        }

        return view('auth.verify-email');
    }
}
