<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $locale = session(config('localization.locale_session_key'))
            ?? $request->route('locale')
            ?? app()->getLocale()
            ?? config('localization.default_locale', 'en');

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice', ['locale' => $locale])
                ->with('status', __('Please verify your email address before accessing your account.'));
        }

        // Redirect to localized role-based dashboard based on user type
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard', ['locale' => $locale]);
        } elseif ($user->hasRole('merchant')) {
            return redirect()->route('merchant.dashboard', ['locale' => $locale]);
        } elseif ($user->hasRole('customer')) {
            return redirect()->route('customer.dashboard', ['locale' => $locale]);
        }

        return redirect()->intended(route('dashboard', ['locale' => $locale]));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
