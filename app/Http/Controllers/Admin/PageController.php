<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();
        
        // Filter by locale if provided
        if ($request->has('locale') && $request->locale !== '') {
            $query->where('locale', $request->locale);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_SEARCH(title, 'one', '%{$search}%') IS NOT NULL")
                  ->orWhereRaw("JSON_SEARCH(content, 'one', '%{$search}%') IS NOT NULL");
            });
        }
        
        // Filter by published status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }
        
        $pages = $query->orderBy('created_at', 'desc')->get();
        
        // Get available locales for filter
        $availableLocales = Page::select('locale')->distinct()->pluck('locale')->toArray();
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        return view('admin.pages.index', compact('pages', 'availableLocales', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.pages.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'slug' => 'required|string|max:100',
            'locale' => 'required|string|in:' . implode(',', $locales),
            'is_published' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'template' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'show_in_menu' => 'nullable|boolean',
        ]);
        
        // Validate multi-language fields
        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'required|string|max:255',
                "content.{$locale}" => 'nullable|string',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
                "excerpt.{$locale}" => 'nullable|string|max:500',
                "menu_label.{$locale}" => 'nullable|string|max:100',
            ]);
        }
        
        // Build multi-language arrays
        $validated['title'] = $request->input('title', []);
        $validated['content'] = $request->input('content', []);
        $validated['meta_title'] = $request->input('meta_title', []);
        $validated['meta_description'] = $request->input('meta_description', []);
        $validated['meta_keywords'] = $request->input('meta_keywords', []);
        $validated['excerpt'] = $request->input('excerpt', []);
        $validated['menu_label'] = $request->input('menu_label', []);
        
        $validated['is_published'] = (bool)($request->input('is_published', false));
        $validated['show_in_menu'] = (bool)($request->input('show_in_menu', false));
        $validated['order'] = $request->input('order', 0);
        $validated['template'] = $request->input('template', 'default');

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        // Unique slug per locale
        if (Page::where('slug', $validated['slug'])->where('locale', $validated['locale'])->exists()) {
            return back()->withErrors(['slug' => __('This slug already exists for the selected locale.')])->withInput();
        }

        Page::create($validated);
        return redirect()->route('admin.pages.index')->with('success', __('Page created successfully.'));
    }

    public function edit(Page $page)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.pages.edit', compact('page', 'locales'));
    }

    public function update(Request $request, Page $page)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'slug' => 'required|string|max:100',
            'locale' => 'required|string|in:' . implode(',', $locales),
            'is_published' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'template' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'show_in_menu' => 'nullable|boolean',
        ]);
        
        // Validate multi-language fields
        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'required|string|max:255',
                "content.{$locale}" => 'nullable|string',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
                "excerpt.{$locale}" => 'nullable|string|max:500',
                "menu_label.{$locale}" => 'nullable|string|max:100',
            ]);
        }
        
        // Build multi-language arrays
        $validated['title'] = $request->input('title', []);
        $validated['content'] = $request->input('content', []);
        $validated['meta_title'] = $request->input('meta_title', []);
        $validated['meta_description'] = $request->input('meta_description', []);
        $validated['meta_keywords'] = $request->input('meta_keywords', []);
        $validated['excerpt'] = $request->input('excerpt', []);
        $validated['menu_label'] = $request->input('menu_label', []);
        
        $validated['is_published'] = (bool)($request->input('is_published', false));
        $validated['show_in_menu'] = (bool)($request->input('show_in_menu', false));
        $validated['order'] = $request->input('order', $page->order ?? 0);
        $validated['template'] = $request->input('template', $page->template ?? 'default');

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            if ($page->featured_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        // Check slug uniqueness if changed
        if ($validated['slug'] !== $page->slug || $validated['locale'] !== $page->locale) {
            $exists = Page::where('slug', $validated['slug'])
                ->where('locale', $validated['locale'])
                ->where('id', '!=', $page->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['slug' => __('This slug already exists for the selected locale.')])->withInput();
            }
        }

        $page->update($validated);
        return redirect()->route('admin.pages.index')->with('success', __('Page updated successfully.'));
    }

    public function destroy(Page $page)
    {
        if ($page->featured_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($page->featured_image);
        }
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', __('Page deleted successfully.'));
    }
}
