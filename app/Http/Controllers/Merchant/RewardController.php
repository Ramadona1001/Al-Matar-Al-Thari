<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\LoyaltyCard;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant|partner']);
    }

    private function findCompany(Request $request): Company
    {
        return Company::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    private function findCardForCompany(Request $request, int $cardId): LoyaltyCard
    {
        $company = $this->findCompany($request);
        $card = LoyaltyCard::query()->where('company_id', $company->id)->where('id', $cardId)->firstOrFail();
        return $card;
    }

    public function index(Request $request, int $loyaltyCard)
    {
        $company = $this->findCompany($request);
        $card = $this->findCardForCompany($request, $loyaltyCard);
        $rewards = Reward::query()->where('card_id', $card->id)->latest()->paginate(15);
        return view('merchant.rewards.index', compact('company', 'card', 'rewards'));
    }

    public function create(Request $request, int $loyaltyCard)
    {
        $company = $this->findCompany($request);
        $card = $this->findCardForCompany($request, $loyaltyCard);
        return view('merchant.rewards.create', compact('company', 'card'));
    }

    public function store(Request $request, int $loyaltyCard)
    {
        $card = $this->findCardForCompany($request, $loyaltyCard);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'stock' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
        $validated['card_id'] = $card->id;
        Reward::create($validated);
        return redirect()->route('merchant.rewards.index', [$card])->with('success', __('Reward created.'));
    }

    public function edit(Request $request, int $loyaltyCard, Reward $reward)
    {
        $card = $this->findCardForCompany($request, $loyaltyCard);
        abort_unless($reward->card_id === $card->id, 403);
        $company = $this->findCompany($request);
        return view('merchant.rewards.edit', compact('company', 'card', 'reward'));
    }

    public function update(Request $request, int $loyaltyCard, Reward $reward)
    {
        $card = $this->findCardForCompany($request, $loyaltyCard);
        abort_unless($reward->card_id === $card->id, 403);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'stock' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
        $reward->update($validated);
        return redirect()->route('merchant.rewards.index', [$card])->with('success', __('Reward updated.'));
    }

    public function destroy(Request $request, int $loyaltyCard, Reward $reward)
    {
        $card = $this->findCardForCompany($request, $loyaltyCard);
        abort_unless($reward->card_id === $card->id, 403);
        $reward->delete();
        return redirect()->route('merchant.rewards.index', [$card])->with('success', __('Reward deleted.'));
    }
}

