<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($tq) use ($search) {
                $tq->where('title', 'like', "%{$search}%")
                   ->orWhere('subtitle', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $banners = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.banners.index', compact('banners', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.banners.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'button_link' => 'nullable|string|max:500',
            'button_style' => 'nullable|in:primary,secondary,outline',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|array',
        ]);

        // Validate multilingual fields
        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'nullable|string|max:255',
                "subtitle.{$locale}" => 'nullable|string|max:500',
                "description.{$locale}" => 'nullable|string',
                "button_text.{$locale}" => 'nullable|string|max:100',
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('banners', 'public');
        }

        if ($request->hasFile('mobile_image_path')) {
            $validated['mobile_image_path'] = $request->file('mobile_image_path')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        // Create banner
        $banner = Banner::create($validated);

        // Save translations for each locale
        foreach ($locales as $locale) {
            $translation = $banner->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}") ?? '';
            $translation->subtitle = $request->input("subtitle.{$locale}") ?? '';
            $translation->description = $request->input("description.{$locale}") ?? '';
            $translation->button_text = $request->input("button_text.{$locale}") ?? '';
            $translation->save();
        }
        
        // Refresh the banner to ensure translations are loaded
        $banner->refresh();
        
        // Verify translations were saved
        \Log::info('Banner created', [
            'banner_id' => $banner->id,
            'translations' => $banner->translations->pluck('locale', 'title')->toArray()
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', __('Banner created successfully.'));
    }

    public function edit(Banner $banner)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.banners.edit', compact('banner', 'locales'));
    }

    public function update(Request $request, Banner $banner)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'button_link' => 'nullable|string|max:500',
            'button_style' => 'nullable|in:primary,secondary,outline',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|array',
        ]);

        // Validate multilingual fields
        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'nullable|string|max:255',
                "subtitle.{$locale}" => 'nullable|string|max:500',
                "description.{$locale}" => 'nullable|string',
                "button_text.{$locale}" => 'nullable|string|max:100',
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('image_path')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('banners', 'public');
        }

        if ($request->hasFile('mobile_image_path')) {
            if ($banner->mobile_image_path) {
                Storage::disk('public')->delete($banner->mobile_image_path);
            }
            $validated['mobile_image_path'] = $request->file('mobile_image_path')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? $banner->order;

        // Update banner
        $banner->update($validated);

        // Update translations for each locale
        foreach ($locales as $locale) {
            $translation = $banner->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}") ?? '';
            $translation->subtitle = $request->input("subtitle.{$locale}") ?? '';
            $translation->description = $request->input("description.{$locale}") ?? '';
            $translation->button_text = $request->input("button_text.{$locale}") ?? '';
            $translation->save();
        }
        
        // Refresh the banner to ensure translations are loaded
        $banner->refresh();
        
        // Verify translations were updated
        \Log::info('Banner updated', [
            'banner_id' => $banner->id,
            'translations' => $banner->translations->pluck('locale', 'title')->toArray()
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', __('Banner updated successfully.'));
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        if ($banner->mobile_image_path) {
            Storage::disk('public')->delete($banner->mobile_image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', __('Banner deleted successfully.'));
    }
}
