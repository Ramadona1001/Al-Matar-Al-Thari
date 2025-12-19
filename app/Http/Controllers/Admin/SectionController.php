<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Section::query();

        if ($request->has('page') && $request->page !== '') {
            $query->where('page', $request->page);
        }

        $sections = $query->ordered()->get();
        $pages = ['home', 'about', 'contact', 'services', 'blog'];
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.sections.index', compact('sections', 'pages', 'locales'));
    }

    public function create()
    {
        $pages = ['home', 'about', 'contact', 'services', 'blog'];
        $types = ['content', 'hero', 'about', 'features', 'testimonials', 'cta', 'gallery', 'stats', 'services', 'how-it-works', 'companies', 'blogs', 'newsletter', 'faq', 'system-explanation', 'offers'];
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.sections.create', compact('pages', 'types', 'locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sections,name,NULL,id,page,' . ($request->page ?? ''),
            'type' => 'required|string|in:content,hero,about,features,testimonials,cta,gallery,stats,services,how-it-works,companies,blogs,newsletter,faq,system-explanation,offers',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images' => 'nullable|array',
            'data' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'columns_per_row' => 'nullable|integer|min:1|max:4',
            'is_visible' => 'nullable|boolean',
            'page' => 'nullable|string',
        ]);

        // Validate translatable fields for each locale
        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'nullable|string|max:255',
                "subtitle.{$locale}" => 'nullable|string',
                "content.{$locale}" => 'nullable|string',
            ]);
        }

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('sections', 'public');
        }

        $validated['is_visible'] = $request->has('is_visible');
        $validated['order'] = $validated['order'] ?? 0;

        $section = Section::create($validated);

        // Save translations
        foreach ($locales as $locale) {
            $translation = $section->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->subtitle = $request->input("subtitle.{$locale}");
            $translation->content = $request->input("content.{$locale}");
            $translation->save();
        }
        
        // Refresh the section to ensure translations are loaded
        $section->refresh();

        return redirect()->route('admin.sections.index')
            ->with('success', __('Section created successfully.'));
    }

    public function edit(Section $section)
    {
        // Get the section ID from route parameter and find the model
        // This works correctly with locale prefix

        $pages = ['home', 'about', 'contact', 'services', 'blog'];
        $types = ['content', 'hero', 'about', 'features', 'testimonials', 'cta', 'gallery', 'stats', 'services', 'how-it-works', 'companies', 'blogs', 'newsletter', 'faq', 'system-explanation', 'offers'];
        $locales = config('localization.supported_locales', ['en']);
        return view('admin.cms.sections.edit', compact('section', 'pages', 'types', 'locales'));
    }

    public function update(Section $section, Request $request)
    {
        // Get the section ID from route parameter and find the model
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sections,name,' . $section->id . ',id,page,' . ($request->page ?? ''),
            'type' => 'required|string|in:content,hero,about,features,testimonials,cta,gallery,stats,services,how-it-works,companies,blogs,newsletter,faq,system-explanation,offers',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images' => 'nullable|array',
            'data' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'columns_per_row' => 'nullable|integer|min:1|max:4',
            'is_visible' => 'nullable|boolean',
            'page' => 'nullable|string',
        ]);

        // Validate translatable fields for each locale
        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'nullable|string|max:255',
                "subtitle.{$locale}" => 'nullable|string',
                "content.{$locale}" => 'nullable|string',
            ]);
        }

        if ($request->hasFile('image_path')) {
            // Get the raw image_path value to avoid array conversion issues
            $oldImagePath = $section->getRawOriginal('image_path');
            if ($oldImagePath && is_string($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
            $validated['image_path'] = $request->file('image_path')->store('sections', 'public');
        }

        $validated['is_visible'] = $request->has('is_visible');
        $validated['order'] = $validated['order'] ?? $section->order;

        $section->update($validated);

        // Update translations
        foreach ($locales as $locale) {
            $translation = $section->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->subtitle = $request->input("subtitle.{$locale}");
            $translation->content = $request->input("content.{$locale}");
            $translation->save();
        }
        
        // Refresh the section to ensure translations are loaded
        $section->refresh();

        return redirect()->route('admin.sections.index')
            ->with('success', __('Section updated successfully.'));
    }

    public function destroy(Section $section)
    {
        
        // Get the raw image_path value to avoid array conversion issues
        $imagePath = $section->getRawOriginal('image_path');
        
        // Delete image if it exists and is a string
        if ($imagePath && is_string($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        $section->delete();

        return redirect()->route('admin.sections.index')
            ->with('success', __('Section deleted successfully.'));
    }
}
