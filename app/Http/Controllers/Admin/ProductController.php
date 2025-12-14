<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Display a listing of all products
     */
    public function index(Request $request)
    {
        $query = Product::with(['company', 'category', 'branch']);

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

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
                    $q->where('in_stock', false)->orWhere('stock_quantity', '<=', 0);
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
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('company', function($companyQuery) use ($search) {
                      $companyQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        $products = $query->ordered()->paginate(15);
        $companies = Company::where('status', 'approved')->get();
        $categories = Category::active()->get();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'in_stock' => Product::where('in_stock', true)->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::where(function($q) {
                $q->where('in_stock', false)->orWhere('stock_quantity', '<=', 0);
            })->count(),
        ];

        return view('admin.products.index', compact('products', 'companies', 'categories', 'stats'));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['company', 'category', 'branch', 'offers', 'coupons']);

        $stats = [
            'total_offers' => $product->offers()->count(),
            'active_offers' => $product->offers()->where('status', 'active')->count(),
            'total_coupons' => $product->coupons()->count(),
            'used_coupons' => $product->coupons()->whereHas('couponUsages')->count(),
        ];

        return view('admin.products.show', compact('product', 'stats'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $companies = Company::where('status', 'approved')->get();
        $categories = Category::active()->get();

        return view('admin.products.edit', compact('product', 'companies', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
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
            'company_id' => 'required|exists:companies,id',
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

        // Handle main image upload
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle additional images upload
        if ($request->hasFile('images')) {
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

        return redirect()->route('admin.products.index')
            ->with('success', __('Product updated successfully.'));
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Check if product has offers or coupons
        if ($product->offers()->count() > 0 || $product->coupons()->count() > 0) {
            return redirect()->route('admin.products.index')
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

        return redirect()->route('admin.products.index')
            ->with('success', __('Product deleted successfully.'));
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Product $product)
    {
        $product->update([
            'is_featured' => !$product->is_featured,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', __('Product featured status updated.'));
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Product $product)
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return redirect()->route('admin.products.index')
            ->with('success', __('Product status updated.'));
    }
}
