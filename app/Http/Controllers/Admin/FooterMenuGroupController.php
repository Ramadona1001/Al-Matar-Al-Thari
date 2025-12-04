<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterMenuGroupRequest;
use App\Models\FooterMenuGroup;
use Illuminate\Http\Request;

class FooterMenuGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = FooterMenuGroup::with('menuItems');

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($tq) use ($search) {
                $tq->where('name', 'like', "%{$search}%");
            });
        }

        $groups = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.footer-menu-groups.index', compact('groups', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.footer-menu-groups.create', compact('locales'));
    }

    public function store(FooterMenuGroupRequest $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        $group = FooterMenuGroup::create([
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $group->translateOrNew($locale)->name = $validated["name_{$locale}"];
        }
        $group->save();

        return redirect()->route('admin.footer-menu-groups.index')
            ->with('success', __('Footer menu group created successfully.'));
    }

    public function edit(FooterMenuGroup $footerMenuGroup)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.footer-menu-groups.edit', compact('footerMenuGroup', 'locales'));
    }

    public function update(FooterMenuGroupRequest $request, FooterMenuGroup $footerMenuGroup)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        $footerMenuGroup->update([
            'order' => $validated['order'] ?? $footerMenuGroup->order,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $footerMenuGroup->translateOrNew($locale)->name = $validated["name_{$locale}"];
        }
        $footerMenuGroup->save();

        return redirect()->route('admin.footer-menu-groups.index')
            ->with('success', __('Footer menu group updated successfully.'));
    }

    public function destroy(FooterMenuGroup $footerMenuGroup)
    {
        $footerMenuGroup->delete();

        return redirect()->route('admin.footer-menu-groups.index')
            ->with('success', __('Footer menu group deleted successfully.'));
    }
}
