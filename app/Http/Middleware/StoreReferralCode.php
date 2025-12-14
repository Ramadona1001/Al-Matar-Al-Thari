<?php

namespace App\Http\Middleware;

use App\Models\Affiliate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class StoreReferralCode
{
    /**
     * Handle an incoming request.
     * 
     * Stores referral code from ?ref= parameter in session and cookie
     * so it persists across all pages and can be used for affiliate tracking.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if 'ref' parameter exists in the request
        $referralCode = $request->query('ref') ?? $request->input('ref');

        if ($referralCode) {
            // Validate that the referral code exists and is active
            $affiliate = Affiliate::where('referral_code', $referralCode)
                ->where('status', 'active')
                ->first();

            if ($affiliate) {
                // Store in session (for current session)
                session(['referral_code' => $referralCode]);

                // Store in cookie (for persistence across sessions, 30 days)
                // Cookie will be set in the response
                // Note: referral_code cookie should NOT be encrypted so it can be read by JavaScript if needed
            }
        }

        $response = $next($request);

        // If we have a valid referral code, set cookie in response
        if ($referralCode && isset($affiliate) && $affiliate) {
            $existingCookie = $request->cookie('referral_code');
            if ($existingCookie !== $referralCode) {
                // Set cookie (30 days expiration)
                // Cookie is in $except list so it won't be encrypted
                $response->cookie('referral_code', $referralCode, 30 * 24 * 60); // 30 days
            }
        }

        return $response;
    }
}
