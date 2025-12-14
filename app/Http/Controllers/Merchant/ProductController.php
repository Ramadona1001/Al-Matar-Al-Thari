<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Company;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant']);
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('warning', __('Please create your company first.'));
        }

        $query = Product::where('company_id', $company->id)
            ->with(['category', 'branch']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('in_stock', true)->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where(function($q) {
                    $q->where('in_stock', false)
                      ->orWhere('stock_quantity', '<=', 0);
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$search}%"])
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->ordered()->paginate(15);
        $categories = Category::active()->get();
        $branches = $company->branches()->active()->get();

        $stats = [
            'total' => Product::where('company_id', $company->id)->count(),
            'active' => Product::where('company_id', $company->id)->where('status', 'active')->count(),
            'in_stock' => Product::where('company_id', $company->id)->where('in_stock', true)->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::where('company_id', $company->id)->where(function($q) {
                $q->where('in_stock', false)->orWhere('stock_quantity', '<=', 0);
            })->count(),
        ];

        return view('merchant.products.index', compact('products', 'categories', 'branches', 'stats'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating products.'));
        }

        $categories = Category::active()->get();
        $branches = $company->branches()->active()->get();

        return view('merchant.products.create', compact('categories', 'branches'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company || $company->status !== 'approved') {
            return redirect()->route('merchant.dashboard')
                ->with('error', __('Your company must be approved before creating products.'));
        }

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'track_stock' => 'nullable|boolean',
            'in_stock' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,active,inactive,out_of_stock',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
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

        // Handle main image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle additional images upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products', 'public');
            }
            $validated['images'] = $images;
        }

        // Set defaults
        $validated['track_stock'] = $validated['track_stock'] ?? true;
        $validated['in_stock'] = $validated['in_stock'] ?? true;
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_featured'] = $validated['is_featured'] ?? false;

        // Set company ID
        $validated['company_id'] = $company->id;

        // Remove temporary input keys
        unset($validated['name_en'], $validated['name_ar'], $validated['description_en'], $validated['description_ar']);

        Product::create($validated);

        return redirect()->route('merchant.products.index')
            ->with('success', __('Product created successfully.'));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        // Ensure the product belongs to the merchant's company
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $product->load(['category', 'branch', 'offers', 'coupons']);

        $stats = [
            'total_offers' => $product->offers()->count(),
            'active_offers' => $product->offers()->where('status', 'active')->count(),
            'total_coupons' => $product->coupons()->count(),
            'used_coupons' => $product->coupons()->whereHas('couponUsages')->count(),
        ];

        return view('merchant.products.show', compact('product', 'stats'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        // Ensure the product belongs to the merchant's company
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $company = auth()->user()->company;
        $categories = Category::active()->get();
        $branches = $company->branches()->active()->get();

        return view('merchant.products.edit', compact('product', 'categories', 'branches'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        // Ensure the product belongs to the merchant's company
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'track_stock' => 'nullable|boolean',
            'in_stock' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,active,inactive,out_of_stock',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
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

        // Generate slug if name changed
        if ($product->name['en'] !== $validated['name_en']) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        // Handle main image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle additional images upload
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products', 'public');
            }
            $validated['images'] = $images;
        }

        // Remove temporary input keys
        unset($validated['name_en'], $validated['name_ar'], $validated['description_en'], $validated['description_ar']);

        $product->update($validated);

        return redirect()->route('merchant.products.index')
            ->with('success', __('Product updated successfully.'));
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Ensure the product belongs to the merchant's company
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        // Check if product has offers or coupons
        if ($product->offers()->count() > 0 || $product->coupons()->count() > 0) {
            return redirect()->route('merchant.products.index')
                ->with('error', __('Cannot delete product with associated offers or coupons.'));
        }

        // Delete images
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('merchant.products.index')
            ->with('success', __('Product deleted successfully.'));
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Product $product)
    {
        // Ensure the product belongs to the merchant's company
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $product->update([
            'is_featured' => !$product->is_featured,
        ]);

        return redirect()->route('merchant.products.index')
            ->with('success', __('Product featured status updated.'));
    }
}

