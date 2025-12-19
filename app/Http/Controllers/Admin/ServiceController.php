<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($tq) use ($search) {
                $tq->where('title', 'like', "%{$search}%")
                   ->orWhere('short_description', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.services.index', compact('services', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.services.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'icon' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'features' => 'nullable|array',
            'pricing' => 'nullable|array',
        ]);

        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'required|string|max:255',
                "short_description.{$locale}" => 'nullable|string|max:500',
                "description.{$locale}" => 'nullable|string',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
            ]);
        }

        // Generate slug from title if not provided
        if (empty($validated['slug'])) {
            $title = $request->input('title.en') ?? $request->input('title.ar') ?? '';
            $validated['slug'] = Str::slug($title);
        }

        // Handle image_path upload
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('services', 'public');
        } else {
            // Don't include image_path in validated if no file is uploaded
            unset($validated['image_path']);
        }

        // Handle og_image upload
        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')->store('services', 'public');
        } else {
            // Don't include og_image in validated if no file is uploaded
            unset($validated['og_image']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['order'] = $validated['order'] ?? 0;

        $service = Service::create($validated);

        foreach ($locales as $locale) {
            $translation = $service->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->short_description = $request->input("short_description.{$locale}");
            $translation->description = $request->input("description.{$locale}");
            $translation->meta_title = $request->input("meta_title.{$locale}");
            $translation->meta_description = $request->input("meta_description.{$locale}");
            $translation->meta_keywords = $request->input("meta_keywords.{$locale}");
            $translation->save();
        }
        
        // Refresh the service to ensure translations are loaded
        $service->refresh();

        return redirect()->route('admin.services.index')
            ->with('success', __('Service created successfully.'));
    }

    public function edit(Service $service)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.services.edit', compact('service', 'locales'));
    }

    public function update(Request $request, Service $service)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'icon' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'features' => 'nullable|array',
            'pricing' => 'nullable|array',
        ]);

        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'required|string|max:255',
                "short_description.{$locale}" => 'nullable|string|max:500',
                "description.{$locale}" => 'nullable|string',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
            ]);
        }

        // Handle image_path update
        if ($request->hasFile('image_path')) {
            // Get the raw image_path value to avoid array conversion issues
            $oldImagePath = $service->getRawOriginal('image_path');
            if ($oldImagePath && is_string($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
            $validated['image_path'] = $request->file('image_path')->store('services', 'public');
        } else {
            // Keep existing image_path if no new file is uploaded
            unset($validated['image_path']);
        }

        // Handle og_image update
        if ($request->hasFile('og_image')) {
            // Get the raw og_image value to avoid array conversion issues
            $oldOgImage = $service->getRawOriginal('og_image');
            if ($oldOgImage && is_string($oldOgImage)) {
                Storage::disk('public')->delete($oldOgImage);
            }
            $validated['og_image'] = $request->file('og_image')->store('services', 'public');
        } else {
            // Keep existing og_image if no new file is uploaded
            unset($validated['og_image']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['order'] = $validated['order'] ?? $service->order;

        $service->update($validated);

        foreach ($locales as $locale) {
            $translation = $service->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->short_description = $request->input("short_description.{$locale}");
            $translation->description = $request->input("description.{$locale}");
            $translation->meta_title = $request->input("meta_title.{$locale}");
            $translation->meta_description = $request->input("meta_description.{$locale}");
            $translation->meta_keywords = $request->input("meta_keywords.{$locale}");
            $translation->save();
        }
        
        // Refresh the service to ensure translations are loaded
        $service->refresh();

        return redirect()->route('admin.services.index')
            ->with('success', __('Service updated successfully.'));
    }

    public function destroy(Service $service)
    {
        // Get the raw image_path value to avoid array conversion issues
        $imagePath = $service->getRawOriginal('image_path');
        if ($imagePath && is_string($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        
        // Get the raw og_image value to avoid array conversion issues
        $ogImage = $service->getRawOriginal('og_image');
        if ($ogImage && is_string($ogImage)) {
            Storage::disk('public')->delete($ogImage);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', __('Service deleted successfully.'));
    }
}
