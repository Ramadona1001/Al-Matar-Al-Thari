<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\LoyaltyCard;
use App\Models\LoyaltyPoint;
use App\Models\Reward;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant|partner']);
    }

    private function findCompany(Request $request): Company
    {
        return Company::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    /**
     * Users > Members overview for the past year with loyalty card interactions.
     */
    public function index(Request $request)
    {
        $company = $this->findCompany($request);

        $oneYearAgo = now()->subYear();

        // Members engaged by staff (award or staff-validated reward) in the past year
        $memberIds = LoyaltyPoint::query()
            ->where('company_id', $company->id)
            ->where('created_at', '>=', $oneYearAgo)
            ->where(function ($q) {
                $q->where('source_type', Staff::class)
                  ->orWhere('source_type', Reward::class);
            })
            ->distinct()
            ->pluck('user_id');

        $members = User::query()->whereIn('id', $memberIds)->paginate(20);

        // Map: user_id => list of loyalty cards they've interacted with in the period
        $cardsPerMember = LoyaltyPoint::query()
            ->select(['user_id', 'loyalty_card_id'])
            ->where('company_id', $company->id)
            ->where('created_at', '>=', $oneYearAgo)
            ->where(function ($q) {
                $q->where('source_type', Staff::class)
                  ->orWhere('source_type', Reward::class);
            })
            ->whereNotNull('loyalty_card_id')
            ->groupBy('user_id', 'loyalty_card_id')
            ->get()
            ->groupBy('user_id');

        // Preload card titles to avoid N+1 lookups in view
        $cardIds = $cardsPerMember->flatten()->pluck('loyalty_card_id')->unique()->values();
        $cards = LoyaltyCard::query()->where('company_id', $company->id)->whereIn('id', $cardIds)->get()->keyBy('id');

        return view('merchant.members.index', [
            'company' => $company,
            'members' => $members,
            'cardsPerMember' => $cardsPerMember,
            'cards' => $cards,
        ]);
    }

    /**
     * Show detailed interactions for a member and a specific loyalty card.
     */
    public function showCard(Request $request, int $memberId, int $loyaltyCardId)
    {
        $company = $this->findCompany($request);
        $member = User::query()->findOrFail($memberId);
        $card = LoyaltyCard::query()->where('company_id', $company->id)->where('id', $loyaltyCardId)->firstOrFail();

        $interactions = LoyaltyPoint::query()
            ->where('company_id', $company->id)
            ->where('user_id', $member->id)
            ->where('loyalty_card_id', $card->id)
            ->latest()
            ->paginate(20);

        $last = LoyaltyPoint::query()
            ->where('company_id', $company->id)
            ->where('user_id', $member->id)
            ->where('loyalty_card_id', $card->id)
            ->latest()
            ->first();

        return view('merchant.members.card', compact('company', 'member', 'card', 'interactions', 'last'));
    }

    /**
     * Revert (delete) the latest transaction for a member and card. Irreversible.
     */
    public function revertLast(Request $request, int $memberId, int $loyaltyCardId)
    {
        $company = $this->findCompany($request);
        $member = User::query()->findOrFail($memberId);
        $card = LoyaltyCard::query()->where('company_id', $company->id)->where('id', $loyaltyCardId)->firstOrFail();

        $last = LoyaltyPoint::query()
            ->where('company_id', $company->id)
            ->where('user_id', $member->id)
            ->where('loyalty_card_id', $card->id)
            ->latest()
            ->firstOrFail();

        DB::beginTransaction();
        try {
            if ($last->type === 'redeemed') {
                // If redeemed from a Reward, restore stock
                if ($last->source_type === Reward::class && $last->source_id) {
                    $reward = Reward::find($last->source_id);
                    if ($reward && !is_null($reward->stock)) {
                        $reward->increment('stock');
                    }
                }
                // Adjust analytics counters safely
                $card->rewards_redeemed_count = max(0, (int)$card->rewards_redeemed_count - 1);
                $card->save();
            } elseif ($last->type === 'earned') {
                // Reduce accumulated points on card
                $card->points_accumulated = max(0, (int)$card->points_accumulated - (int)$last->points);
                $card->save();
            }

            // Delete the last record (irreversible)
            $last->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', __('Failed to revert the latest transaction.'));
        }

        return back()->with('success', __('Latest transaction reverted. This action cannot be undone.'));
    }
}

