<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Testimonial::query();

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
                $tq->where('name', 'like', "%{$search}%")
                   ->orWhere('company', 'like', "%{$search}%")
                   ->orWhere('testimonial', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en']);

        return view('admin.cms.testimonials.index', compact('testimonials', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en']);
        return view('admin.cms.testimonials.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en']);
        $rules = [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["name_{$locale}"] = 'required|string|max:255';
            $rules["position_{$locale}"] = 'nullable|string|max:255';
            $rules["company_{$locale}"] = 'nullable|string|max:255';
            $rules["testimonial_{$locale}"] = 'required|string';
        }

        $validated = $request->validate($rules);

        $testimonial = Testimonial::create([
            'avatar' => $request->hasFile('avatar') ? $request->file('avatar')->store('testimonials', 'public') : null,
            'rating' => $validated['rating'] ?? 5,
            'is_featured' => $request->has('is_featured'),
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $translation = $testimonial->translateOrNew($locale);
            $translation->name = $validated["name_{$locale}"];
            $translation->position = $validated["position_{$locale}"] ?? null;
            $translation->company = $validated["company_{$locale}"] ?? null;
            $translation->testimonial = $validated["testimonial_{$locale}"];
            $translation->save();
        }
        
        // Refresh the testimonial to ensure translations are loaded
        $testimonial->refresh();

        return redirect()->route('admin.testimonials.index')
            ->with('success', __('Testimonial created successfully.'));
    }

    public function edit(Testimonial $testimonial)
    {
        $locales = config('localization.supported_locales', ['en']);
        return view('admin.cms.testimonials.edit', compact('testimonial', 'locales'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $locales = config('localization.supported_locales', ['en']);
        $rules = [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["name_{$locale}"] = 'required|string|max:255';
            $rules["position_{$locale}"] = 'nullable|string|max:255';
            $rules["company_{$locale}"] = 'nullable|string|max:255';
            $rules["testimonial_{$locale}"] = 'required|string';
        }

        $validated = $request->validate($rules);

        $updateData = [
            'rating' => $validated['rating'] ?? $testimonial->rating,
            'is_featured' => $request->has('is_featured'),
            'order' => $validated['order'] ?? $testimonial->order,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            $updateData['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($updateData);

        foreach ($locales as $locale) {
            $translation = $testimonial->translateOrNew($locale);
            $translation->name = $validated["name_{$locale}"];
            $translation->position = $validated["position_{$locale}"] ?? null;
            $translation->company = $validated["company_{$locale}"] ?? null;
            $translation->testimonial = $validated["testimonial_{$locale}"];
            $translation->save();
        }
        
        // Refresh the testimonial to ensure translations are loaded
        $testimonial->refresh();

        return redirect()->route('admin.testimonials.index')
            ->with('success', __('Testimonial updated successfully.'));
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->avatar) {
            Storage::disk('public')->delete($testimonial->avatar);
        }

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', __('Testimonial deleted successfully.'));
    }
}
