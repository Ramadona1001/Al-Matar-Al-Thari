<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFrozenStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Check if user account is frozen
        if ($user->is_frozen) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Your account has been frozen. Reason: :reason', ['reason' => $user->frozen_reason ?? 'N/A']),
                ], 403);
            }

            return redirect()->route('home')
                ->with('error', __('Your account has been frozen. Reason: :reason', ['reason' => $user->frozen_reason ?? 'N/A']));
        }

        // Check if digital card is frozen (for customers)
        if ($user->hasRole('customer') && $user->digitalCard && $user->digitalCard->is_frozen) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Your digital card has been frozen. Reason: :reason', ['reason' => $user->digitalCard->frozen_reason ?? 'N/A']),
                ], 403);
            }

            return redirect()->route('customer.digital-card.index')
                ->with('error', __('Your digital card has been frozen. Reason: :reason', ['reason' => $user->digitalCard->frozen_reason ?? 'N/A']));
        }

        // Check if company is frozen (for merchants)
        if ($user->hasRole('merchant') && $user->company && $user->company->is_frozen) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Your company account has been frozen. Reason: :reason', ['reason' => $user->company->frozen_reason ?? 'N/A']),
                ], 403);
            }

            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company account has been frozen. Reason: :reason', ['reason' => $user->company->frozen_reason ?? 'N/A']));
        }

        return $next($request);
    }
}
