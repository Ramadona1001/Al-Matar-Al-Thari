<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Category;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    /**
     * Display a listing of available offers.
     */
    public function index(Request $request)
    {
        $query = Offer::with(['company', 'category'])
            ->active()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(title, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $offers = $query->orderBy('is_featured', 'desc')->latest()->paginate(12)->withQueryString();
        $categories = Category::active()->ordered()->get();
        $types = ['percentage', 'fixed', 'free_shipping', 'buy_x_get_y'];

        return view('customer.offers.index', compact('offers', 'categories', 'types'));
    }

    /**
     * Display the specified offer.
     */
    public function show(Offer $offer)
    {
        if (!$offer->isActive()) {
            abort(404);
        }

        $offer->load(['company', 'category', 'coupons' => function ($query) {
            $query->active()->limit(5);
        }]);

        $relatedOffers = Offer::where('company_id', $offer->company_id)
            ->where('id', '!=', $offer->id)
            ->active()
            ->limit(4)
            ->get();

        return view('customer.offers.show', compact('offer', 'relatedOffers'));
    }
}
