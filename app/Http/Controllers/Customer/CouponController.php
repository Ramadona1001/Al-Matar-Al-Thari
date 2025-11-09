<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Company;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    /**
     * Display available coupons to the customer.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Coupon::with(['company', 'offer'])
            ->active()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) use ($user) {
                $q->where('is_public', true)
                  ->orWhere('user_id', $user->id);
            });

        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%'.$request->search.'%');
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $companies = Company::approved()->orderBy('name')->get();
        $types = ['percentage', 'fixed', 'free_shipping', 'buy_x_get_y'];

        return view('customer.coupons.index', compact('coupons', 'companies', 'types'));
    }

    /**
     * Display a coupon details page.
     */
    public function show(Coupon $coupon)
    {
        if (!$coupon->isValid() && $coupon->status !== 'active') {
            abort(404);
        }

        if (!$coupon->is_public && $coupon->user_id !== auth()->id()) {
            abort(403);
        }

        $coupon->load(['company', 'offer']);

        $similarCoupons = Coupon::active()
            ->where('company_id', $coupon->company_id)
            ->where('id', '!=', $coupon->id)
            ->limit(4)
            ->get();

        return view('customer.coupons.show', compact('coupon', 'similarCoupons'));
    }
}
