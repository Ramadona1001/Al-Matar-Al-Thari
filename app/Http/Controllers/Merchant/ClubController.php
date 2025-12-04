<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant']);
    }

    public function index(Request $request)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        $clubs = Club::query()->where('company_id', $company->id)->latest()->paginate(15);
        return view('merchant.clubs.index', compact('company', 'clubs'));
    }

    public function create(Request $request)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        return view('merchant.clubs.create', compact('company'));
    }

    public function store(Request $request)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        // Ensure unique per company
        if (Club::where('company_id', $company->id)->where('slug', $slug)->exists()) {
            return back()->withErrors(['slug' => __('Slug already exists for this company.')])->withInput();
        }

        Club::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('merchant.clubs.index')->with('success', __('Club created successfully.'));
    }

    public function edit(Request $request, Club $club)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($club->company_id === $company->id, 403);
        return view('merchant.clubs.edit', compact('company', 'club'));
    }

    public function update(Request $request, Club $club)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($club->company_id === $company->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        if (Club::where('company_id', $company->id)->where('slug', $slug)->where('id', '!=', $club->id)->exists()) {
            return back()->withErrors(['slug' => __('Slug already exists for this company.')])->withInput();
        }

        $club->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? $club->status,
        ]);

        return redirect()->route('merchant.clubs.index')->with('success', __('Club updated successfully.'));
    }

    public function destroy(Request $request, Club $club)
    {
        $company = Company::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($club->company_id === $company->id, 403);
        $club->delete();
        return back()->with('success', __('Club deleted successfully.'));
    }
}

