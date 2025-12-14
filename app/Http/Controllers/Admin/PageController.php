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
        
        // Search functionality (search in translations)
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereHas('translations', function($tq) use ($search) {
                      $tq->where('title', 'like', "%{$search}%")
                         ->orWhere('content', 'like', "%{$search}%")
                         ->orWhere('excerpt', 'like', "%{$search}%");
                  });
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
        
        $pages = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        return view('admin.pages.index', compact('pages', 'locales'));
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
        
        $validated['is_published'] = (bool)($request->input('is_published', false));
        $validated['show_in_menu'] = (bool)($request->input('show_in_menu', false));
        $validated['order'] = $request->input('order', 0);
        $validated['template'] = $request->input('template', 'default');

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        // Create page (non-translatable fields only)
        $page = Page::create($validated);

        // Save translations for each locale
        foreach ($locales as $locale) {
            $translation = $page->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}") ?? '';
            $translation->content = $request->input("content.{$locale}") ?? '';
            $translation->meta_title = $request->input("meta_title.{$locale}") ?? '';
            $translation->meta_description = $request->input("meta_description.{$locale}") ?? '';
            $translation->meta_keywords = $request->input("meta_keywords.{$locale}") ?? '';
            $translation->excerpt = $request->input("excerpt.{$locale}") ?? '';
            $translation->menu_label = $request->input("menu_label.{$locale}") ?? '';
            $translation->save();
        }
        
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

        // Update page (non-translatable fields only)
        $page->update($validated);

        // Save translations for each locale
        foreach ($locales as $locale) {
            $translation = $page->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}") ?? '';
            $translation->content = $request->input("content.{$locale}") ?? '';
            $translation->meta_title = $request->input("meta_title.{$locale}") ?? '';
            $translation->meta_description = $request->input("meta_description.{$locale}") ?? '';
            $translation->meta_keywords = $request->input("meta_keywords.{$locale}") ?? '';
            $translation->excerpt = $request->input("excerpt.{$locale}") ?? '';
            $translation->menu_label = $request->input("menu_label.{$locale}") ?? '';
            $translation->save();
        }
        
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
