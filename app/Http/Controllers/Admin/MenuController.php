<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Menu::with('parent', 'children');

        if ($request->has('name') && $request->name !== '') {
            $query->where('name', $request->name);
        }


        $menus = $query->rootItems()->ordered()->get();
        $menuNames = Menu::distinct()->pluck('name');
        $locales = config('localization.supported_locales', ['en']);

        return view('admin.cms.menus.index', compact('menus', 'menuNames', 'locales'));
    }

    public function create()
    {
        $parents = Menu::rootItems()->get();
        $menuNames = ['header', 'footer', 'sidebar'];
        $locales = config('localization.supported_locales', ['en']);
        
        // Load Footer Menu Groups for footer menus
        $footerMenuGroups = class_exists(\App\Models\FooterMenuGroup::class)
            ? \App\Models\FooterMenuGroup::active()->ordered()->get()
            : collect();
        
        return view('admin.cms.menus.create', compact('parents', 'menuNames', 'locales', 'footerMenuGroups'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en']);
        $rules = [
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'open_in_new_tab' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["label_{$locale}"] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        $menu = Menu::create([
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
            'route' => $validated['route'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'footer_menu_group_id' => ($validated['name'] === 'footer' && isset($validated['footer_menu_group_id'])) ? $validated['footer_menu_group_id'] : null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'open_in_new_tab' => $request->has('open_in_new_tab'),
        ]);

        foreach ($locales as $locale) {
            $menu->translateOrNew($locale)->label = $validated["label_{$locale}"];
        }
        $menu->save();

        return redirect()->route('admin.menus.index')
            ->with('success', __('Menu item created successfully.'));
    }

    public function edit(Menu $menu)
    {
        $parents = Menu::rootItems()->where('id', '!=', $menu->id)->get();
        $menuNames = ['header', 'footer', 'sidebar'];
        $locales = config('localization.supported_locales', ['en']);
        
        // Load Footer Menu Groups for footer menus
        $footerMenuGroups = class_exists(\App\Models\FooterMenuGroup::class)
            ? \App\Models\FooterMenuGroup::active()->ordered()->get()
            : collect();
        
        return view('admin.cms.menus.edit', compact('menu', 'parents', 'menuNames', 'locales', 'footerMenuGroups'));
    }

    public function update(Request $request, Menu $menu)
    {
        $locales = config('localization.supported_locales', ['en']);
        $rules = [
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'open_in_new_tab' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["label_{$locale}"] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Prevent setting itself as parent
        if ($validated['parent_id'] == $menu->id) {
            return back()->withErrors(['parent_id' => __('A menu item cannot be its own parent.')]);
        }

        $menu->update([
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
            'route' => $validated['route'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'footer_menu_group_id' => ($validated['name'] === 'footer' && isset($validated['footer_menu_group_id'])) ? $validated['footer_menu_group_id'] : null,
            'order' => $validated['order'] ?? $menu->order,
            'is_active' => $request->has('is_active'),
            'open_in_new_tab' => $request->has('open_in_new_tab'),
        ]);

        foreach ($locales as $locale) {
            $menu->translateOrNew($locale)->label = $validated["label_{$locale}"];
        }
        $menu->save();

        return redirect()->route('admin.menus.index')
            ->with('success', __('Menu item updated successfully.'));
    }

    public function destroy(Menu $menu)
    {
        // Delete children first
        $menu->children()->delete();
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', __('Menu item deleted successfully.'));
    }
}
