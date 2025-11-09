<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateSale;
use App\Models\Company;
use App\Models\Offer;
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
        $affiliate = $user->affiliate()->with(['company', 'offer'])->first();
        $sales = collect();

        if ($affiliate) {
            $sales = AffiliateSale::with(['company', 'offer'])
                ->where('affiliate_id', $affiliate->id)
                ->latest()
                ->paginate(10);
        }

        $companies = Company::approved()->select('id', 'name')->get();
        $offers = Offer::active()->select('id', 'title', 'company_id')->get();

        return view('customer.affiliate.index', compact('affiliate', 'sales', 'companies', 'offers'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'offer_id' => 'nullable|exists:offers,id',
            'commission_type' => 'nullable|in:percentage,fixed',
        ]);

        if ($user->affiliate) {
            return redirect()->route('customer.affiliate.index')
                ->with('error', __('You already have an affiliate account.'));
        }

        $company = Company::approved()->findOrFail($validated['company_id']);

        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'offer_id' => $validated['offer_id'] ?? null,
            'referral_code' => Affiliate::generateUniqueReferralCode(),
            'referral_link' => config('app.url') . '/offers?ref=' . $user->id,
            'commission_rate' => $company->affiliate_commission_rate,
            'commission_type' => $validated['commission_type'] ?? 'percentage',
            'status' => 'pending',
        ]);

        return redirect()->route('customer.affiliate.index')
            ->with('success', __('Affiliate application submitted.')); 
    }
}
