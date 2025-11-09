<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $affiliates = Affiliate::with(['user', 'company'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.affiliates.index', compact('affiliates'));
    }

    public function updateStatus(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,suspended',
        ]);

        $affiliate->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] === 'approved' ? now() : $affiliate->approved_at,
        ]);

        return redirect()->route('admin.affiliates.index')
            ->with('success', __('Affiliate status updated.'));
    }
}
