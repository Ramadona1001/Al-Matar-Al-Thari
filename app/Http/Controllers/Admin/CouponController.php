<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\Company;
use App\Models\Product;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display a listing of all coupons
     */
    public function index(Request $request)
    {
        $query = Coupon::with(['company', 'offer', 'product', 'user']);

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by offer
        if ($request->filled('offer_id')) {
            $query->where('offer_id', $request->offer_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('company', function($companyQuery) use ($search) {
                      $companyQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"]);
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $coupons = $query->latest()->paginate(15);
        $companies = Company::where('status', 'approved')->get();
        $offers = Offer::all();

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 'active')->count(),
            'used' => Coupon::where('status', 'used')->count(),
            'expired' => Coupon::where('status', 'expired')->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'companies', 'offers', 'stats'));
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['company', 'offer', 'product', 'user', 'couponUsages']);

        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        $companies = Company::where('status', 'approved')->get();
        $offers = Offer::where('company_id', $coupon->company_id)->get();
        $products = Product::where('company_id', $coupon->company_id)->active()->get();

        return view('admin.coupons.edit', compact('coupon', 'companies', 'offers', 'products'));
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,used,expired,disabled',
            'is_public' => 'nullable|boolean',
            'company_id' => 'required|exists:companies,id',
            'offer_id' => 'nullable|exists:offers,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        // Generate QR code if code changed
        if ($coupon->code !== $validated['code']) {
            $qrCodePath = $this->qrCodeService->generateForCoupon($validated['code']);
            $validated['qr_code'] = $qrCodePath;
        }

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', __('Coupon updated successfully.'));
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Coupon $coupon)
    {
        // Delete QR code
        if ($coupon->qr_code) {
            Storage::disk('public')->delete($coupon->qr_code);
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', __('Coupon deleted successfully.'));
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Coupon $coupon)
    {
        $newStatus = $coupon->status === 'active' ? 'disabled' : 'active';
        $coupon->update(['status' => $newStatus]);

        return redirect()->route('admin.coupons.index')
            ->with('success', __('Coupon status updated.'));
    }
}
