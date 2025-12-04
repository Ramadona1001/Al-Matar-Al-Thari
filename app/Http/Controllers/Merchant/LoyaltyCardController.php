<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\LoyaltyCard;
use App\Models\Club;
use Illuminate\Http\Request;

class LoyaltyCardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant|partner']);
    }

    public function index(Request $request)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        $cards = LoyaltyCard::query()->where('company_id', $company->id)->latest()->paginate(15);
        return view('merchant.loyalty_cards.index', compact('company', 'cards'));
    }

    public function create(Request $request)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        $clubs = Club::query()->where('company_id', $company->id)->get();
        return view('merchant.loyalty_cards.create', compact('company', 'clubs'));
    }

    public function store(Request $request)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'required|string|max:150|unique:loyalty_cards,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'points' => 'nullable|integer|min:0',
            'balance' => 'nullable|numeric|min:0',
            'club_id' => 'nullable|exists:clubs,id',
            'visible_on_homepage' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);
        $validated['company_id'] = $company->id;
        // Ensure club belongs to company
        if (!empty($validated['club_id'])) {
            $club = Club::find($validated['club_id']);
            if (!$club || $club->company_id !== $company->id) {
                return back()->withErrors(['club_id' => __('Invalid club selection.')])->withInput();
            }
        }
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('loyalty_cards', 'public');
            $validated['image_path'] = $path;
        }
        // Normalize numeric fields
        $validated['points'] = (int)($validated['points'] ?? 0);
        $validated['balance'] = (float)($validated['balance'] ?? 0);
        LoyaltyCard::create($validated);
        return redirect()->route('merchant.loyalty-cards.index')->with('status', __('Loyalty card created'));
    }

    public function edit(Request $request, LoyaltyCard $loyaltyCard)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($loyaltyCard->company_id === $company->id, 403);
        $clubs = Club::query()->where('company_id', $company->id)->get();
        return view('merchant.loyalty_cards.edit', compact('company', 'loyaltyCard', 'clubs'));
    }

    public function update(Request $request, LoyaltyCard $loyaltyCard)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($loyaltyCard->company_id === $company->id, 403);
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'required|string|max:150|unique:loyalty_cards,slug,' . $loyaltyCard->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'points' => 'nullable|integer|min:0',
            'balance' => 'nullable|numeric|min:0',
            'club_id' => 'nullable|exists:clubs,id',
            'visible_on_homepage' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);
        if (!empty($validated['club_id'])) {
            $club = Club::find($validated['club_id']);
            if (!$club || $club->company_id !== $company->id) {
                return back()->withErrors(['club_id' => __('Invalid club selection.')])->withInput();
            }
        }
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('loyalty_cards', 'public');
            $validated['image_path'] = $path;
        }
        $validated['points'] = (int)($validated['points'] ?? $loyaltyCard->points ?? 0);
        $validated['balance'] = (float)($validated['balance'] ?? $loyaltyCard->balance ?? 0);
        $loyaltyCard->update($validated);
        return redirect()->route('merchant.loyalty-cards.index')->with('status', __('Loyalty card updated'));
    }

    public function destroy(Request $request, LoyaltyCard $loyaltyCard)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($loyaltyCard->company_id === $company->id, 403);
        $loyaltyCard->delete();
        return redirect()->route('merchant.loyalty-cards.index')->with('status', __('Loyalty card deleted'));
    }
}
