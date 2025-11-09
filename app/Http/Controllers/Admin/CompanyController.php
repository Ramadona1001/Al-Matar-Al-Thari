<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Notifications\CompanyStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Display a listing of companies
     */
    public function index(Request $request)
    {
        $query = Company::with('user', 'branches');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $companies = $query->latest()->paginate(15);

        $stats = [
            'total' => Company::count(),
            'pending' => Company::where('status', 'pending')->count(),
            'approved' => Company::where('status', 'approved')->count(),
            'rejected' => Company::where('status', 'rejected')->count(),
        ];

        return view('admin.companies.index', compact('companies', 'stats'));
    }

    /**
     * Show the form for creating a new company
     */
    public function create()
    {
        $merchants = User::whereHas('roles', function($query) {
            $query->where('name', 'merchant');
        })->whereDoesntHave('company')->get();

        return view('admin.companies.create', compact('merchants'));
    }

    /**
     * Store a newly created company
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,approved,rejected',
            'affiliate_commission_rate' => 'nullable|numeric|min:0|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company = Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company created successfully.'));
    }

    /**
     * Display the specified company
     */
    public function show(Company $company)
    {
        $company->load('user', 'branches', 'offers', 'coupons');
        
        $stats = [
            'total_offers' => $company->offers()->count(),
            'active_offers' => $company->offers()->where('status', 'active')->count(),
            'total_coupons' => $company->coupons()->count(),
            'used_coupons' => $company->coupons()->whereHas('couponUsages')->count(),
            'total_transactions' => $company->transactions()->count(),
            'total_revenue' => $company->transactions()->where('status', 'completed')->sum('final_amount'),
        ];

        return view('admin.companies.show', compact('company', 'stats'));
    }

    /**
     * Show the form for editing the specified company
     */
    public function edit(Company $company)
    {
        $merchants = User::whereHas('roles', function($query) {
            $query->where('name', 'merchant');
        })->get();

        return view('admin.companies.edit', compact('company', 'merchants'));
    }

    /**
     * Update the specified company
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,approved,rejected',
            'affiliate_commission_rate' => 'nullable|numeric|min:0|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company updated successfully.'));
    }

    /**
     * Remove the specified company
     */
    public function destroy(Company $company)
    {
        // Delete logo if exists
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company deleted successfully.'));
    }

    /**
     * Approve a company
     */
    public function approve(Company $company)
    {
        if ($company->status !== 'pending') {
            return redirect()->route('admin.companies.index')
                ->with('error', __('Only pending companies can be approved.'));
        }

        $company->update([
            'status' => 'approved',
        ]);

        if ($company->relationLoaded('user') || $company->user) {
            $company->user->notify(
                new CompanyStatusChangedNotification(
                    $company->fresh('user'),
                    'approved',
                    null,
                    Auth::id()
                )
            );
        }

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company approved successfully.'));
    }

    /**
     * Reject a company
     */
    public function reject(Request $request, Company $company)
    {
        if ($company->status !== 'pending') {
            return redirect()->route('admin.companies.index')
                ->with('error', __('Only pending companies can be rejected.'));
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $company->update([
            'status' => 'rejected',
        ]);

        $reason = $validated['rejection_reason'] ?? null;

        if ($company->relationLoaded('user') || $company->user) {
            $company->user->notify(
                new CompanyStatusChangedNotification(
                    $company->fresh('user'),
                    'rejected',
                    $reason,
                    Auth::id()
                )
            );
        }

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company rejected successfully.'));
    }

    /**
     * Bulk approve companies
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $companies = Company::with('user')
            ->whereIn('id', $validated['company_ids'])
            ->where('status', 'pending')
            ->get();

        foreach ($companies as $company) {
            $company->update(['status' => 'approved']);

            if ($company->user) {
                $company->user->notify(
                    new CompanyStatusChangedNotification(
                        $company->fresh('user'),
                        'approved',
                        null,
                        Auth::id()
                    )
                );
            }
        }

        $count = $companies->count();

        return redirect()->route('admin.companies.index')
            ->with('success', __(":count companies approved successfully.", ['count' => $count]));
    }

    /**
     * Bulk reject companies
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $companies = Company::with('user')
            ->whereIn('id', $validated['company_ids'])
            ->where('status', 'pending')
            ->get();

        foreach ($companies as $company) {
            $company->update(['status' => 'rejected']);

            if ($company->user) {
                $company->user->notify(
                    new CompanyStatusChangedNotification(
                        $company->fresh('user'),
                        'rejected',
                        null,
                        Auth::id()
                    )
                );
            }
        }

        $count = $companies->count();

        return redirect()->route('admin.companies.index')
            ->with('success', __(":count companies rejected successfully.", ['count' => $count]));
    }
}

