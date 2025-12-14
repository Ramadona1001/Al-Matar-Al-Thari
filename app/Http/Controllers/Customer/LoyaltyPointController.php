<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use App\Models\PointRedemption;
use App\Models\Wallet;
use App\Models\WalletTransaction;
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
        
        // Get or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'loyalty_points_balance' => 0,
                'affiliate_points_balance' => 0,
                'loyalty_points_pending' => 0,
                'affiliate_points_pending' => 0,
            ]
        );
        
        // Get wallet transactions for loyalty points
        $points = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'loyalty')
            ->latest()
            ->paginate(10);
        
        // Get available points from wallet balance
        $availablePoints = $wallet->loyalty_points_balance ?? 0;
        
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
        
        // Get or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'loyalty_points_balance' => 0,
                'affiliate_points_balance' => 0,
                'loyalty_points_pending' => 0,
                'affiliate_points_pending' => 0,
            ]
        );
        
        // Get available points from wallet balance
        $availablePoints = $wallet->loyalty_points_balance ?? 0;

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

        // Create wallet transaction for redemption
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'loyalty',
            'transaction_type' => 'redeemed',
            'points' => -$validated['points'],
            'status' => 'pending',
            'source_type' => PointRedemption::class,
            'source_id' => $redemption->id,
            'description' => __('Redemption request #:id', ['id' => $redemption->id]),
        ]);

        if ($pointsService->autoApproveRedemptions()) {
            $redemption->update([
                'status' => 'approved',
                'processed_by' => $user->id,
                'processed_at' => now(),
            ]);
            
            // Deduct points from wallet if auto-approved
            $wallet->redeemLoyaltyPoints($validated['points']);
        }

        return redirect()->route('customer.loyalty.index')
            ->with('success', __('Redemption request submitted successfully.'));
    }
}
