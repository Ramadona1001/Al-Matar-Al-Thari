<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"])
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Prepare multilingual fields
        $validated['name'] = [
            'en' => $validated['name_en'],
            'ar' => $validated['name_ar'] ?? $validated['name_en'],
        ];

        $validated['description'] = [
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? $validated['description_en'] ?? '',
        ];

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Set defaults
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Remove temporary input keys
        unset($validated['name_en'], $validated['name_ar'], $validated['description_en'], $validated['description_ar']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category created successfully.'));
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load('offers');
        
        $stats = [
            'total_offers' => $category->offers()->count(),
            'active_offers' => $category->offers()->where('status', 'active')->count(),
        ];

        return view('admin.categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Prepare multilingual fields
        $validated['name'] = [
            'en' => $validated['name_en'],
            'ar' => $validated['name_ar'] ?? $validated['name_en'],
        ];

        $validated['description'] = [
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? $validated['description_en'] ?? '',
        ];

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Remove temporary input keys
        unset($validated['name_en'], $validated['name_ar'], $validated['description_en'], $validated['description_ar']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has offers
        if ($category->offers()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', __('Cannot delete category with associated offers.'));
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category deleted successfully.'));
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category status updated.'));
    }
}

