<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\LoyaltyCard;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;

class LoyaltyCardMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant|partner']);
    }

    public function index(Request $request, int $loyaltyCard)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        $card = LoyaltyCard::query()->where('company_id', $company->id)->where('id', $loyaltyCard)->firstOrFail();

        // Aggregate points for members specific to this card
        $members = LoyaltyPoint::query()
            ->selectRaw('user_id, SUM(CASE WHEN type = "earned" THEN points ELSE 0 END) AS earned_points, SUM(CASE WHEN type = "redeemed" THEN points ELSE 0 END) AS redeemed_points')
            ->where('company_id', $company->id)
            ->where('card_id', $card->id)
            ->groupBy('user_id')
            ->with('user')
            ->paginate(20);

        return view('merchant.loyalty_cards.members', compact('company', 'card', 'members'));
    }
}

