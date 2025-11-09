<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Company;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant']);
    }

    public function index(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('warning', __('Please create your company first.'));
        }

        $affiliates = Affiliate::with('user')
            ->where('company_id', $company->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'suspended', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('merchant.affiliates.index', compact('affiliates', 'company'));
    }

    public function updateSettings(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('warning', __('Please create your company first.'));
        }

        $validated = $request->validate([
            'affiliate_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $company->update($validated);

        return redirect()->route('merchant.affiliates.index')
            ->with('success', __('Affiliate settings updated successfully.'));
    }

    public function updateStatus(Request $request, Affiliate $affiliate)
    {
        $company = $request->user()->company;

        if (!$company || $affiliate->company_id !== $company->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,suspended',
        ]);

        $affiliate->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] === 'approved' ? now() : $affiliate->approved_at,
        ]);

        return redirect()->route('merchant.affiliates.index')
            ->with('success', __('Affiliate status updated.'));
    }
}
