<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant']);
    }

    /**
     * Display a listing of the branches.
     */
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')->with('warning', __('Please create your company first.'));
        }

        $branches = $company->branches()->orderBy('created_at', 'desc')->paginate(10);

        return view('merchant.branches.index', compact('branches', 'company'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')->with('warning', __('Please create your company first.'));
        }

        return view('merchant.branches.create', compact('company'));
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')->with('warning', __('Please create your company first.'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = $company->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        Branch::create($validated);

        return redirect()->route('merchant.branches.index')->with('success', __('Branch created successfully.'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        $company = auth()->user()->company;

        if ($branch->company_id !== optional($company)->id) {
            abort(403);
        }

        return view('merchant.branches.edit', compact('branch', 'company'));
    }

    /**
     * Update the specified branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $company = auth()->user()->company;

        if ($branch->company_id !== optional($company)->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $branch->update($validated);

        return redirect()->route('merchant.branches.index')->with('success', __('Branch updated successfully.'));
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(Branch $branch)
    {
        $company = auth()->user()->company;

        if ($branch->company_id !== optional($company)->id) {
            abort(403);
        }

        $branch->delete();

        return redirect()->route('merchant.branches.index')->with('success', __('Branch deleted successfully.'));
    }
}
