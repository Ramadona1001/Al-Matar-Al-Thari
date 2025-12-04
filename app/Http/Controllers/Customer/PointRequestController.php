<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyCard;
use App\Models\PointRequestLink;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PointRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer'])->except(['showPublic', 'send']);
        $this->middleware(['auth'])->only(['send']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $links = $user->pointRequestLinks()->latest()->paginate(10);

        return view('customer.requests.index', [
            'links' => $links,
            'activeCount' => $user->pointRequestLinks()->active()->count(),
            'maxActive' => 5,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $activeCount = $user->pointRequestLinks()->active()->count();
        if ($activeCount >= 5) {
            return back()->withErrors(['limit' => __('You have reached the maximum of 5 active request links.')]);
        }

        $data = $request->validate([
            'card_id' => ['nullable', 'exists:loyalty_cards,id'],
            'expires_days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $expiresDays = $data['expires_days'] ?? 7;

        PointRequestLink::create([
            'uuid' => (string) Str::uuid(),
            'customer_id' => $user->id,
            'card_id' => $data['card_id'] ?? null,
            'expires_at' => now()->addDays($expiresDays),
            'active' => true,
        ]);

        return redirect()->route('customer.requests.index', app()->getLocale())->with('status', __('Point request link generated.'));
    }

    /**
     * Public page to send points to the link owner.
     */
    public function showPublic(string $locale, string $uuid)
    {
        app()->setLocale($locale);
        $link = PointRequestLink::where('uuid', $uuid)->firstOrFail();

        if (!$link->isActive()) {
            abort(410, __('This request link is no longer active.'));
        }

        $suggestedCards = LoyaltyCard::query()
            ->where('visible_on_homepage', true)
            ->orderBy('title')
            ->get();

        return view('customer.requests.show', [
            'link' => $link,
            'suggestedCards' => $suggestedCards,
        ]);
    }

    /**
     * Authenticated send action: transfer points FIFO from current user to link owner.
     */
    public function send(Request $request, string $locale, string $uuid, PointsService $pointsService)
    {
        app()->setLocale($locale);
        $request->validate([
            'card_id' => ['required', 'exists:loyalty_cards,id'],
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $link = PointRequestLink::where('uuid', $uuid)->firstOrFail();
        if (!$link->isActive()) {
            return back()->withErrors(['link' => __('This request link is no longer active.')]);
        }

        $sender = $request->user();
        $receiver = $link->customer;
        $card = LoyaltyCard::findOrFail($request->input('card_id'));
        $points = (int) $request->input('points');

        try {
            $pointsService->transferFIFO($sender, $receiver, $card, $points, $sender);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['points' => $e->getMessage()]);
        }

        return redirect()->route('public.requests.show', [$locale, $uuid])->with('status', __('Points sent successfully.'));
    }
}

