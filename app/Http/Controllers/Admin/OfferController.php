<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Company;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Display a listing of all offers
     */
    public function index(Request $request)
    {
        $query = Offer::with(['company', 'category', 'branch', 'product']);

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

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
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$search}%"])
                  ->orWhereHas('company', function($companyQuery) use ($search) {
                      $companyQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        $offers = $query->latest()->paginate(15);
        $companies = Company::where('status', 'approved')->get();
        $categories = Category::active()->get();

        $stats = [
            'total' => Offer::count(),
            'active' => Offer::where('status', 'active')->count(),
            'expired' => Offer::where('end_date', '<', now())->count(),
            'featured' => Offer::where('is_featured', true)->count(),
        ];

        return view('admin.offers.index', compact('offers', 'companies', 'categories', 'stats'));
    }

    /**
     * Display the specified offer
     */
    public function show(Offer $offer)
    {
        $offer->load(['company', 'category', 'branch', 'product', 'coupons']);

        $stats = [
            'total_coupons' => $offer->coupons()->count(),
            'used_coupons' => $offer->coupons()->whereHas('couponUsages')->count(),
        ];

        return view('admin.offers.show', compact('offer', 'stats'));
    }

    /**
     * Show the form for editing the specified offer
     */
    public function edit(Offer $offer)
    {
        $companies = Company::where('status', 'approved')->get();
        $categories = Category::active()->get();
        $products = Product::where('company_id', $offer->company_id)->active()->get();

        return view('admin.offers.edit', compact('offer', 'companies', 'categories', 'products'));
    }

    /**
     * Update the specified offer
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,inactive,expired,paused',
            'is_featured' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'company_id' => 'required|exists:companies,id',
            'product_id' => 'nullable|exists:products,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Prepare multilingual fields
        $validated['title'] = [
            'en' => $validated['title_en'],
            'ar' => $validated['title_ar'] ?? $validated['title_en'],
        ];

        $validated['description'] = [
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? $validated['description_en'] ?? '',
        ];

        // Generate slug if title changed
        if ($offer->title['en'] !== $validated['title_en']) {
            $validated['slug'] = Str::slug($validated['title_en']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($offer->image) {
                Storage::disk('public')->delete($offer->image);
            }
            $validated['image'] = $request->file('image')->store('offers', 'public');
        }

        // Remove temporary input keys
        unset($validated['title_en'], $validated['title_ar'], $validated['description_en'], $validated['description_ar']);

        $offer->update($validated);

        return redirect()->route('admin.offers.index')
            ->with('success', __('Offer updated successfully.'));
    }

    /**
     * Remove the specified offer
     */
    public function destroy(Offer $offer)
    {
        // Delete image
        if ($offer->image) {
            Storage::disk('public')->delete($offer->image);
        }

        $offer->delete();

        return redirect()->route('admin.offers.index')
            ->with('success', __('Offer deleted successfully.'));
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Offer $offer)
    {
        $offer->update([
            'is_featured' => !$offer->is_featured,
        ]);

        return redirect()->route('admin.offers.index')
            ->with('success', __('Offer featured status updated.'));
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Offer $offer)
    {
        $newStatus = $offer->status === 'active' ? 'inactive' : 'active';
        $offer->update(['status' => $newStatus]);

        return redirect()->route('admin.offers.index')
            ->with('success', __('Offer status updated.'));
    }
}
