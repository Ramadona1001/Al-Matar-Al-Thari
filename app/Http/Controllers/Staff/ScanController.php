<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\LoyaltyCard;
use App\Models\LoyaltyPoint;
use App\Models\Reward;
use Illuminate\Http\Request;
use App\Services\PointsService;

class ScanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    private function getVerifiedStaff(Request $request): Staff
    {
        $staff = Staff::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($staff->is_verified, 403);
        return $staff;
    }

    public function index(Request $request)
    {
        $staff = $this->getVerifiedStaff($request);
        $cards = LoyaltyCard::query()->where('company_id', $staff->company_id)->when($staff->club_id, function ($q) use ($staff) {
            $q->where('club_id', $staff->club_id);
        })->get();
        return view('staff.scan.index', compact('staff', 'cards'));
    }

    public function award(Request $request)
    {
        $staff = $this->getVerifiedStaff($request);
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'card_id' => 'required|exists:loyalty_cards,id',
            'amount' => 'required|numeric|min:0.01',
        ]);
        $card = LoyaltyCard::findOrFail($validated['card_id']);
        abort_unless($card->company_id === $staff->company_id, 403);
        if ($staff->club_id && $card->club_id && $staff->club_id !== $card->club_id) {
            return back()->withErrors(['card_id' => __('You are not assigned to this club.')]);
        }
        $user = User::findOrFail($validated['member_id']);
        $pointsService = app(PointsService::class);
        $points = $pointsService->calculateEarnedPoints((float)$validated['amount']);
        if ($points <= 0) {
            return back()->withErrors(['amount' => __('Amount too small to award points.')]);
        }

        // Award via service to update ledger, wallet pivot, and analytics
        $pointsService->addPoints($user, $card, $points, $staff);

        return back()->with('success', __('Points awarded successfully.'));
    }

    public function validateReward(Request $request)
    {
        $staff = $this->getVerifiedStaff($request);
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'card_id' => 'required|exists:loyalty_cards,id',
            'reward_id' => 'required|exists:rewards,id',
        ]);
        $card = LoyaltyCard::findOrFail($validated['card_id']);
        abort_unless($card->company_id === $staff->company_id, 403);
        if ($staff->club_id && $card->club_id && $staff->club_id !== $card->club_id) {
            return back()->withErrors(['card_id' => __('You are not assigned to this club.')]);
        }

        $reward = Reward::findOrFail($validated['reward_id']);
        abort_unless($reward->card_id === $card->id, 403);

        $user = User::findOrFail($validated['member_id']);

        $pointsService = app(PointsService::class);
        if (!$pointsService->canRedeem($user, $card, (int)$reward->points_required)) {
            return back()->withErrors(['reward_id' => __('Insufficient points for this reward.')]);
        }

        // Redeem via FIFO using service (creates ledger and updates wallet pivot)
        $pointsService->redeemFIFO($user, $card, (int)$reward->points_required, $reward, $staff);

        // Decrement stock if applicable
        if (!is_null($reward->stock) && $reward->stock > 0) {
            $reward->decrement('stock');
        }

        // Update analytics
        $card->increment('rewards_redeemed_count');
        $card->increment('staff_actions_count');

        return back()->with('success', __('Reward validated successfully.'));
    }
}
