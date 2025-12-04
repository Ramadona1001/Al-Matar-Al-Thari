<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyCard;
use App\Models\RedeemCode;
use App\Services\PointsService;
use Illuminate\Http\Request;

class RedeemCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index()
    {
        return view('customer.redeem-codes.index', [
            'cards' => LoyaltyCard::orderBy('title')->get(),
            'defaultExpiryMinutes' => (int) (config('app.CODE_TO_REDEEM_POINTS_VALID_MINUTES', env('CODE_TO_REDEEM_POINTS_VALID_MINUTES', 4320))),
        ]);
    }

    public function store(Request $request, PointsService $pointsService)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:32'],
            'card_id' => ['nullable', 'exists:loyalty_cards,id'],
        ]);

        $user = $request->user();
        $code = RedeemCode::where('code', $data['code'])->first();
        if (!$code) {
            return back()->withErrors(['code' => __('Invalid code.')]);
        }
        if (!$code->isValid()) {
            return back()->withErrors(['code' => __('Code expired or already used.')]);
        }

        $cardId = $data['card_id'] ?? $code->card_id;
        if (!$cardId) {
            return back()->withErrors(['card_id' => __('Please select a loyalty card.')]);
        }
        $card = LoyaltyCard::findOrFail($cardId);

        $pointsService->redeemCode($user, $code, $card);

        return redirect()->route('customer.redeem-codes.index', app()->getLocale())->with('status', __('Points code redeemed successfully.'));
    }
}

