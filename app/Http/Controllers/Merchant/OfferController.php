<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Company;
use App\Models\Category;
use App\Models\Branch;
use App\Models\User;
use App\Notifications\NewOfferNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant']);
    }

    /**
     * Display a listing of offers
     */
    public function index(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('warning', __('Please create your company first.'));
        }

        $query = Offer::where('company_id', $company->id)
            ->with(['category', 'branch']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$search}%"]);
            });
        }

        $offers = $query->latest()->paginate(15);
        $categories = Category::active()->get();
        $branches = $company->branches()->active()->get();

        $stats = [
            'total' => Offer::where('company_id', $company->id)->count(),
            'active' => Offer::where('company_id', $company->id)->where('status', 'active')->count(),
            'expired' => Offer::where('company_id', $company->id)->where('end_date', '<', now())->count(),
            'featured' => Offer::where('company_id', $company->id)->where('is_featured', true)->count(),
        ];

        return view('merchant.offers.index', compact('offers', 'categories', 'branches', 'stats'));
    }

    /**
     * Show the form for creating a new offer
     */
    public function create()
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating offers.'));
        }

        $categories = Category::active()->get();
        $branches = $company->branches()->active()->get();

        return view('merchant.offers.create', compact('categories', 'branches'));
    }

    /**
     * Store a newly created offer
     */
    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating offers.'));
        }

        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Generate slug first (before setting title to avoid conflicts)
        $validated['slug'] = Str::slug($validated['title_en']);

        // Prepare title and description for JSON storage
        $validated['title'] = [
            'en' => $validated['title_en'],
            'ar' => $validated['title_ar'] ?? $validated['title_en'],
        ];

        $validated['description'] = [
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? $validated['description_en'] ?? '',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('offers', 'public');
        }

        // Set company ID
        $validated['company_id'] = $company->id;

        // Remove temporary fields
        unset($validated['title_en'], $validated['title_ar'], $validated['description_en'], $validated['description_ar']);

        $offer = Offer::create($validated);

        $this->notifyInterestedCustomers($company, $offer);

        return redirect()->route('merchant.offers.index')
            ->with('success', __('Offer created successfully.'));
    }

    /**
     * Display the specified offer
     */
    public function show(Offer $offer)
    {
        // Ensure the offer belongs to the merchant's company
        if ($offer->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $offer->load(['category', 'branch', 'coupons', 'transactions']);

        $stats = [
            'total_coupons' => $offer->coupons()->count(),
            'used_coupons' => $offer->coupons()->whereHas('couponUsages')->count(),
            'total_transactions' => $offer->transactions()->count(),
            'total_revenue' => $offer->transactions()->where('transactions.status', 'completed')->sum('transactions.final_amount'),
        ];

        return view('merchant.offers.show', compact('offer', 'stats'));
    }

    /**
     * Show the form for editing the specified offer
     */
    public function edit(Offer $offer)
    {
        // Ensure the offer belongs to the merchant's company
        if ($offer->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $company = auth()->user()->company;
        $categories = Category::active()->get();
        $branches = $company->branches()->active()->get();

        return view('merchant.offers.edit', compact('offer', 'categories', 'branches'));
    }

    /**
     * Update the specified offer
     */
    public function update(Request $request, Offer $offer)
    {
        // Ensure the offer belongs to the merchant's company
        if ($offer->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Generate slug if title changed (before setting title to avoid conflicts)
        if ($offer->title['en'] !== $validated['title_en']) {
            $validated['slug'] = Str::slug($validated['title_en']);
        }

        // Prepare title and description for JSON storage
        $validated['title'] = [
            'en' => $validated['title_en'],
            'ar' => $validated['title_ar'] ?? $validated['title_en'],
        ];

        $validated['description'] = [
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? $validated['description_en'] ?? '',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($offer->image) {
                Storage::disk('public')->delete($offer->image);
            }
            $validated['image'] = $request->file('image')->store('offers', 'public');
        }

        // Remove temporary fields
        unset($validated['title_en'], $validated['title_ar'], $validated['description_en'], $validated['description_ar']);

        $offer->update($validated);

        return redirect()->route('merchant.offers.index')
            ->with('success', __('Offer updated successfully.'));
    }

    /**
     * Remove the specified offer
     */
    public function destroy(Offer $offer)
    {
        // Ensure the offer belongs to the merchant's company
        if ($offer->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        // Delete image if exists
        if ($offer->image) {
            Storage::disk('public')->delete($offer->image);
        }

        $offer->delete();

        return redirect()->route('merchant.offers.index')
            ->with('success', __('Offer deleted successfully.'));
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Offer $offer)
    {
        // Ensure the offer belongs to the merchant's company
        if ($offer->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $offer->update([
            'is_featured' => !$offer->is_featured,
        ]);

        return redirect()->route('merchant.offers.index')
            ->with('success', __('Offer featured status updated.'));
    }

    /**
     * Notify interested customers about a new offer.
     */
    protected function notifyInterestedCustomers(Company $company, Offer $offer): void
    {
        $customerIds = $company->transactions()
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        $customers = User::role('customer')
            ->where('is_active', true)
            ->whereIn('id', $customerIds)
            ->get();

        if ($customers->isEmpty()) {
            $customers = User::role('customer')
                ->where('is_active', true)
                ->limit(50)
                ->get();
        }

        if ($customers->isEmpty()) {
            return;
        }

        Notification::send($customers, new NewOfferNotification($offer->fresh(['company'])));
    }
}

