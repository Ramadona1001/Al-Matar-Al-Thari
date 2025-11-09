<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\Company;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CouponController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->middleware(['auth', 'role:merchant']);
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('warning', __('Please create your company first.'));
        }

        $query = Coupon::where('company_id', $company->id)
            ->with(['offer', 'user']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by offer
        if ($request->has('offer_id') && $request->offer_id !== '') {
            $query->where('offer_id', $request->offer_id);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%");
            });
        }

        $coupons = $query->latest()->paginate(15);
        $offers = Offer::where('company_id', $company->id)->get();

        $stats = [
            'total' => Coupon::where('company_id', $company->id)->count(),
            'active' => Coupon::where('company_id', $company->id)->where('status', 'active')->count(),
            'used' => Coupon::where('company_id', $company->id)->where('status', 'used')->count(),
            'expired' => Coupon::where('company_id', $company->id)->where('status', 'expired')->count(),
        ];

        return view('merchant.coupons.index', compact('coupons', 'offers', 'stats'));
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create()
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating coupons.'));
        }

        $offers = Offer::where('company_id', $company->id)->get();

        return view('merchant.coupons.create', compact('offers'));
    }

    /**
     * Store a newly created coupon
     */
    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating coupons.'));
        }

        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,disabled',
            'is_public' => 'nullable|boolean',
            'offer_id' => 'nullable|exists:offers,id',
        ]);

        // Generate unique code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = Coupon::generateUniqueCode();
        }

        // Set company ID
        $validated['company_id'] = $company->id;

        // Create coupon
        $coupon = Coupon::create($validated);

        // Generate QR code
        $qrCodePath = $this->qrCodeService->generateCouponQrCode($coupon->code);
        $coupon->update(['qr_code' => $qrCodePath]);

        return redirect()->route('merchant.coupons.index')
            ->with('success', __('Coupon created successfully.'));
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        // Ensure the coupon belongs to the merchant's company
        if ($coupon->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $coupon->load(['offer', 'user', 'couponUsages.user', 'transactions']);

        $stats = [
            'total_usage' => $coupon->couponUsages()->count(),
            'successful_usage' => $coupon->couponUsages()->where('status', 'used')->count(),
            'failed_usage' => $coupon->couponUsages()->where('status', 'failed')->count(),
            'remaining_usage' => $coupon->getRemainingUsageCount(),
        ];

        return view('merchant.coupons.show', compact('coupon', 'stats'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        // Ensure the coupon belongs to the merchant's company
        if ($coupon->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $company = auth()->user()->company;
        $offers = Offer::where('company_id', $company->id)->get();

        return view('merchant.coupons.edit', compact('coupon', 'offers'));
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        // Ensure the coupon belongs to the merchant's company
        if ($coupon->company_id !== auth()->user()->company->id) {
            abort(403);
        }

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
            'offer_id' => 'nullable|exists:offers,id',
        ]);

        $coupon->update($validated);

        // Regenerate QR code if code changed
        if ($coupon->wasChanged('code') && $coupon->qr_code) {
            // Delete old QR code
            $this->qrCodeService->deleteQrCode($coupon->qr_code);
            
            // Generate new QR code
            $qrCodePath = $this->qrCodeService->generateCouponQrCode($coupon->code);
            $coupon->update(['qr_code' => $qrCodePath]);
        }

        return redirect()->route('merchant.coupons.index')
            ->with('success', __('Coupon updated successfully.'));
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Coupon $coupon)
    {
        // Ensure the coupon belongs to the merchant's company
        if ($coupon->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        // Delete QR code if exists
        if ($coupon->qr_code) {
            $this->qrCodeService->deleteQrCode($coupon->qr_code);
        }

        $coupon->delete();

        return redirect()->route('merchant.coupons.index')
            ->with('success', __('Coupon deleted successfully.'));
    }

    /**
     * Generate/download QR code for coupon
     */
    public function downloadQrCode(Coupon $coupon)
    {
        // Ensure the coupon belongs to the merchant's company
        if ($coupon->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        // Generate QR code if doesn't exist
        if (!$coupon->qr_code) {
            $qrCodePath = $this->qrCodeService->generateCouponQrCode($coupon->code);
            $coupon->update(['qr_code' => $qrCodePath]);
        }

        // Get the file path
        $filePath = str_replace(Storage::url(''), '', $coupon->qr_code);
        $fullPath = Storage::disk('public')->path($filePath);

        if (!file_exists($fullPath)) {
            // Regenerate if file doesn't exist
            $qrCodePath = $this->qrCodeService->generateCouponQrCode($coupon->code);
            $coupon->update(['qr_code' => $qrCodePath]);
            $filePath = str_replace(Storage::url(''), '', $qrCodePath);
            $fullPath = Storage::disk('public')->path($filePath);
        }

        return response()->download($fullPath, 'coupon-qr-' . $coupon->code . '.png');
    }

    /**
     * Display QR code image
     */
    public function showQrCode(Coupon $coupon)
    {
        // Ensure the coupon belongs to the merchant's company
        if ($coupon->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        // Generate QR code if doesn't exist
        if (!$coupon->qr_code) {
            $qrCodePath = $this->qrCodeService->generateCouponQrCode($coupon->code);
            $coupon->update(['qr_code' => $qrCodePath]);
        }

        return view('merchant.coupons.qr-code', compact('coupon'));
    }

    /**
     * Bulk generate coupons
     */
    public function bulkGenerate(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating coupons.'));
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,disabled',
            'is_public' => 'nullable|boolean',
            'offer_id' => 'nullable|exists:offers,id',
        ]);

        $coupons = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $validated['code'] = Coupon::generateUniqueCode();
            $validated['company_id'] = $company->id;
            
            $coupon = Coupon::create($validated);
            
            // Generate QR code
            $qrCodePath = $this->qrCodeService->generateCouponQrCode($coupon->code);
            $coupon->update(['qr_code' => $qrCodePath]);
            
            $coupons[] = $coupon;
        }

        return redirect()->route('merchant.coupons.index')
            ->with('success', __(":count coupons generated successfully.", ['count' => count($coupons)]));
    }
}

