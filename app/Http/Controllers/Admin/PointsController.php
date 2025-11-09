<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointRedemption;
use App\Models\PointsSetting;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function edit(PointsService $pointsService)
    {
        $settings = $pointsService->settings();
        $pendingRedemptions = PointRedemption::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'completed', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('admin.points.edit', compact('settings', 'pendingRedemptions'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'earn_rate' => 'required|numeric|min:0.01',
            'redeem_rate' => 'required|numeric|min:0.0001',
            'referral_bonus_points' => 'required|integer|min:0',
            'auto_approve_redemptions' => 'nullable|boolean',
        ]);

        $validated['auto_approve_redemptions'] = $request->boolean('auto_approve_redemptions');
        $validated['updated_by'] = Auth::id();

        PointsSetting::create($validated);

        return redirect()->route('admin.points.edit')
            ->with('success', __('Points settings updated successfully.'));
    }

    public function updateRedemption(Request $request, PointRedemption $redemption)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        $redemption->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $redemption->notes,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.points.edit')
            ->with('success', __('Redemption status updated.'));
    }
}
