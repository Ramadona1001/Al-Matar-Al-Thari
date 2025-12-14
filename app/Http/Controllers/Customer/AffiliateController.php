<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateSale;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Create affiliate account automatically if doesn't exist
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            $referralCode = Affiliate::generateUniqueReferralCode();
            $affiliate = Affiliate::create([
                'user_id' => $user->id,
                'company_id' => null, // General affiliate, not tied to specific company
                'offer_id' => null, // General affiliate link
                'referral_code' => $referralCode,
                'referral_link' => url('/register?ref=' . $referralCode), // Store initial link (accessor will override on read)
                'commission_rate' => 0, // Will be calculated based on admin settings
                'commission_type' => 'percentage',
                'status' => 'active', // Auto-approved for general affiliate
            ]);
        }
        
        // Ensure referral_link is updated if code exists but link is old (for existing affiliates)
        if ($affiliate->referral_code && str_contains($affiliate->getRawOriginal('referral_link') ?? '', 'localhost')) {
            $affiliate->update([
                'referral_link' => url('/register?ref=' . $affiliate->referral_code)
            ]);
        }
        
        // Load affiliate with sales
        $affiliate->load(['affiliateSales.transaction', 'affiliateSales.user']);
        $sales = AffiliateSale::where('affiliate_id', $affiliate->id)
            ->with(['transaction', 'user'])
            ->latest()
            ->paginate(10);

        return view('customer.affiliate.index', compact('affiliate', 'sales'));
    }
}
