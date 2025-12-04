<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;

class CustomerLoyaltyController extends Controller
{
    /**
     * Show the customer's wallet homepage.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $walletCards = $user->walletCards()->with('company')->get();

        $discoverCards = LoyaltyCard::query()
            ->where('status', 'published')
            ->where('visible_on_homepage', true)
            ->whereHas('company', function ($q) {
                $q->where('status', 'approved')->where('can_display_cards_on_homepage', true);
            })
            ->with('company')
            ->latest()
            ->limit(12)
            ->get();

        return view('customer.wallet.index', compact('walletCards', 'discoverCards'));
    }

    /**
     * Follow/add a loyalty card to the customer's wallet.
     */
    public function follow(Request $request, LoyaltyCard $loyaltyCard)
    {
        $user = $request->user();

        // Prevent duplicates by checking existing pivot
        if (!$user->walletCards()->where('card_id', $loyaltyCard->id)->exists()) {
            $user->walletCards()->attach($loyaltyCard->id, [
                'points_balance' => 0,
                'last_transaction_at' => null,
            ]);
        }

        return redirect()->route('customer.wallet.index')->with('status', __('Card added to your wallet.'));
    }

    /**
     * Unfollow/remove a loyalty card from the customer's wallet.
     */
    public function unfollow(Request $request, LoyaltyCard $loyaltyCard)
    {
        $user = $request->user();
        $user->walletCards()->detach($loyaltyCard->id);

        return back()->with('status', __('Card removed from your wallet.'));
    }
}

