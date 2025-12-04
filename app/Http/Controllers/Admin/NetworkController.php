<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Network;
use App\Models\User;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|super-admin']);
    }

    public function index()
    {
        $networks = Network::query()->latest()->paginate(12);
        return view('admin.networks.index', compact('networks'));
    }

    public function create()
    {
        return view('admin.networks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $validated['created_by'] = $request->user()->id;
        Network::create($validated);
        return redirect()->route('admin.networks.index')->with('status', __('Network created'));
    }

    public function edit(Network $network)
    {
        return view('admin.networks.edit', compact('network'));
    }

    public function update(Request $request, Network $network)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $network->update($validated);
        return redirect()->route('admin.networks.index')->with('status', __('Network updated'));
    }

    public function destroy(Network $network)
    {
        $network->delete();
        return redirect()->route('admin.networks.index')->with('status', __('Network deleted'));
    }

    public function managersEdit(Network $network)
    {
        $managers = User::role('manager')->get();
        $assigned = $network->managers()->pluck('users.id')->toArray();
        return view('admin.networks.managers', compact('network', 'managers', 'assigned'));
    }

    public function managersUpdate(Request $request, Network $network)
    {
        $validated = $request->validate([
            'manager_ids' => 'array',
            'manager_ids.*' => 'exists:users,id',
        ]);

        $managerIds = collect($validated['manager_ids'] ?? [])
            ->unique()
            ->values()
            ->all();

        // only keep users who actually have manager role
        $validManagerIds = User::role('manager')->whereIn('id', $managerIds)->pluck('id')->all();
        $network->managers()->sync($validManagerIds);

        return redirect()->route('admin.networks.index')->with('status', __('Managers updated for network'));
    }
}
