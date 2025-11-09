<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use App\Models\PointRedemption;
use App\Services\PointsService;
use Illuminate\Http\Request;

class LoyaltyPointController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index(PointsService $pointsService)
    {
        $user = auth()->user();
        $points = $user->loyaltyPoints()->latest()->paginate(10);
        $availablePoints = $user->loyalty_points_balance;
        $redemptions = PointRedemption::where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'redemptions');

        return view('customer.loyalty.index', [
            'points' => $points,
            'availablePoints' => $availablePoints,
            'redemptions' => $redemptions,
            'redeemRate' => $pointsService->redeemRate(),
        ]);
    }

    public function store(Request $request, PointsService $pointsService)
    {
        $user = auth()->user();
        $availablePoints = $user->loyalty_points_balance;

        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['points'] > $availablePoints) {
            return redirect()->route('customer.loyalty.index')
                ->with('error', __('Insufficient points for redemption.'));
        }

        $amount = $pointsService->calculateRedeemAmount($validated['points']);

        $redemption = PointRedemption::create([
            'user_id' => $user->id,
            'points' => $validated['points'],
            'amount' => $amount,
            'notes' => $validated['notes'] ?? null,
        ]);

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => $validated['points'],
            'type' => 'redeemed',
            'description' => __('Redemption request #:id', ['id' => $redemption->id]),
            'source_type' => PointRedemption::class,
            'source_id' => $redemption->id,
        ]);

        if ($pointsService->autoApproveRedemptions()) {
            $redemption->update([
                'status' => 'approved',
                'processed_by' => $user->id,
                'processed_at' => now(),
            ]);
        }

        return redirect()->route('customer.loyalty.index')
            ->with('success', __('Redemption request submitted successfully.'));
    }
}
