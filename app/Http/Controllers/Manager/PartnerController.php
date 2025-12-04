<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Network;
use App\Models\User;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:manager']);
    }

    public function index(Request $request)
    {
        $managerNetworks = Network::query()
            ->whereHas('managers', function ($q) use ($request) {
                $q->where('users.id', $request->user()->id);
            })
            ->pluck('id');

        $companies = Company::query()
            ->whereIn('network_id', $managerNetworks)
            ->latest()
            ->paginate(15);

        return view('manager.partners.index', compact('companies'));
    }

    public function create(Request $request)
    {
        $networks = Network::query()
            ->whereHas('managers', function ($q) use ($request) {
                $q->where('users.id', $request->user()->id);
            })
            ->get();
        $merchantUsers = User::role('merchant')->get();
        return view('manager.partners.create', compact('networks', 'merchantUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:150',
            'user_id' => 'required|exists:users,id',
            'network_id' => 'required|exists:networks,id',
            'status' => 'required|in:pending,approved,rejected',
            'can_display_cards_on_homepage' => 'boolean',
        ]);

        // ensure chosen network is managed by this manager
        $isManaged = Network::where('id', $validated['network_id'])
            ->whereHas('managers', function ($q) use ($request) {
                $q->where('users.id', $request->user()->id);
            })
            ->exists();
        abort_unless($isManaged, 403);

        Company::create($validated);
        return redirect()->route('manager.partners.index')->with('status', __('Partner created'));
    }
}

