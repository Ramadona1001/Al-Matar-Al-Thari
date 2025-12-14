<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\DigitalCard;
use App\Models\Category;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->middleware(['auth', 'role:customer']);
        $this->pointsService = $pointsService;
    }

    /**
     * Display a listing of available products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['company', 'category'])
            ->where('status', 'active')
            ->where('in_stock', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by company
        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        // Filter featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $products = $query->ordered()->latest()->paginate(12)->withQueryString();
        $categories = Category::active()->ordered()->get();
        $companies = \App\Models\Company::where('status', 'approved')
            ->whereHas('products', function($q) {
                $q->where('status', 'active')->where('in_stock', true);
            })
            ->get();

        return view('customer.products.index', compact('products', 'categories', 'companies'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        if (!$product->isAvailable()) {
            abort(404);
        }

        $product->load(['company', 'category', 'offers' => function ($query) {
            $query->active()->limit(5);
        }]);

        $relatedProducts = Product::where('company_id', $product->company_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->where('in_stock', true)
            ->limit(4)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Purchase a product using digital card.
     */
    public function purchase(Request $request, Product $product)
    {
        $user = auth()->user();

        // Validate product availability
        if (!$product->isAvailable()) {
            return back()->with('error', __('Product is not available for purchase.'));
        }

        // Get user's digital card
        $digitalCard = $user->digitalCard;
        if (!$digitalCard || !$digitalCard->isActive()) {
            return back()->with('error', __('Please activate your digital card first.'));
        }

        // Determine max quantity
        $maxQuantity = 999;
        if ($product->track_stock) {
            $maxQuantity = max(1, $product->stock_quantity ?? 1);
        }
        
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $maxQuantity,
            'branch_id' => 'nullable|exists:branches,id',
            'referral_code' => 'nullable|string',
        ]);
        
        // Validate referral code if provided
        if (!empty($validated['referral_code'])) {
            $affiliate = \App\Models\Affiliate::where('referral_code', $validated['referral_code'])
                ->where('status', 'active')
                ->first();
                
            if (!$affiliate) {
                return back()->with('error', __('Invalid referral code.'));
            }
            
            // Prevent self-referral
            if ($affiliate->user_id === $user->id) {
                return back()->with('error', __('You cannot use your own referral code.'));
            }
        }

        $quantity = $validated['quantity'];
        
        // Validate product price
        if (!$product->price || $product->price <= 0) {
            return back()->with('error', __('Invalid product price.'));
        }
        
        $originalAmount = $product->price * $quantity;
        $discountAmount = 0; // No discount from card
        $finalAmount = $originalAmount;

        DB::beginTransaction();
        try {
            // Refresh product to get latest stock
            $product->refresh();
            
            // Validate stock availability before transaction
            if ($product->track_stock && $product->stock_quantity < $quantity) {
                throw new \Exception(__('Insufficient stock. Only :qty available.', ['qty' => $product->stock_quantity]));
            }
            
            // Handle referral code if provided
            $referralCode = $validated['referral_code'] ?? session('referral_code') ?? request()->cookie('referral_code');
            
            // If referral code provided, store it in session for affiliate tracking
            if ($referralCode) {
                $affiliate = \App\Models\Affiliate::where('referral_code', $referralCode)
                    ->where('status', 'active')
                    ->first();
                    
                if ($affiliate && $affiliate->user_id !== $user->id) {
                    // Store in session for affiliate tracking
                    session(['referral_code' => $referralCode]);
                }
            }
            
            // Get product name safely
            $productName = $product->localized_name ?? $product->name ?? 'Product';
            
            // Create transaction
            $transaction = Transaction::create([
                'transaction_id' => Transaction::generateUniqueTransactionId(),
                'amount' => $originalAmount,
                'original_price' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'payment_method' => 'digital_card',
                'user_id' => $user->id,
                'company_id' => $product->company_id,
                'branch_id' => $validated['branch_id'] ?? null,
                'digital_card_id' => $digitalCard->id,
                'product_id' => $product->id,
                'notes' => "Purchase: {$productName} x {$quantity}",
            ]);

            // Update product stock if tracking
            if ($product->track_stock) {
                $newQuantity = $product->stock_quantity - $quantity;
                $product->update([
                    'stock_quantity' => max(0, $newQuantity),
                    'in_stock' => $newQuantity > 0
                ]);
            }

            // Complete transaction to trigger events (points will be calculated automatically)
            // AffiliateRewardJob will be triggered by OrderCompleted event and will check session/cookie for referral code
            $transaction->complete();

            DB::commit();

            return redirect()->route('customer.products.show', $product)
                ->with('success', __('Product purchased successfully! Loyalty points will be added to your account.'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Product purchase error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show user-friendly error message
            $errorMessage = $e->getMessage();
            if (config('app.debug')) {
                $errorMessage .= ' (' . $e->getFile() . ':' . $e->getLine() . ')';
            }
            
            return back()->with('error', $errorMessage ?: __('An error occurred while processing your purchase. Please try again.'));
        }
    }
}

