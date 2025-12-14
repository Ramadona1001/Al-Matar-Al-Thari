<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\DigitalCard;
use App\Models\Branch;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->middleware(['auth', 'role:merchant']);
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display the sales page
     */
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('warning', __('Please create your company first.'));
        }

        $products = Product::where('company_id', $company->id)
            ->where('status', 'active')
            ->where('in_stock', true)
            ->with(['category'])
            ->get();

        $branches = Branch::where('company_id', $company->id)
            ->where('is_active', true)
            ->get();

        return view('merchant.sales.index', compact('products', 'branches'));
    }

    /**
     * Process a sale - scan customer card and create transaction
     */
    public function processSale(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => __('Company not found.'),
            ], 404);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'qr_data' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Validate product belongs to company
        $product = Product::where('id', $validated['product_id'])
            ->where('company_id', $company->id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => __('Product not found or does not belong to your company.'),
            ], 404);
        }

        // Validate product availability
        if (!$product->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => __('Product is not available for sale.'),
            ], 400);
        }

        // Validate quantity
        if ($product->track_stock && $validated['quantity'] > $product->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => __('Insufficient stock. Available quantity: :qty', ['qty' => $product->stock_quantity]),
            ], 400);
        }

        // Validate and decode QR code
        $qrData = $this->qrCodeService->validateQrCode($validated['qr_data']);

        if (!$qrData || $qrData['type'] !== 'card') {
            return response()->json([
                'success' => false,
                'message' => __('Invalid QR code. Please scan a valid customer digital card.'),
            ], 400);
        }

        // Get customer's digital card
        $digitalCard = DigitalCard::where('card_number', $qrData['card_number'])->first();

        if (!$digitalCard) {
            return response()->json([
                'success' => false,
                'message' => __('Customer digital card not found.'),
            ], 404);
        }

        // Validate card is active
        if (!$digitalCard->isActive()) {
            return response()->json([
                'success' => false,
                'message' => __('Customer digital card is not active or has expired.'),
            ], 400);
        }

        $customer = $digitalCard->user;

        // Calculate amounts
        $quantity = $validated['quantity'];
        $originalAmount = $product->price * $quantity;
        $discountAmount = 0; // No discount from card
        $finalAmount = $originalAmount;

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = Transaction::create([
                'transaction_id' => Transaction::generateUniqueTransactionId(),
                'amount' => $originalAmount,
                'original_price' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'payment_method' => 'digital_card',
                'user_id' => $customer->id,
                'company_id' => $company->id,
                'branch_id' => $validated['branch_id'] ?? null,
                'digital_card_id' => $digitalCard->id,
                'product_id' => $product->id,
                'notes' => "Sale: " . $product->localized_name . " x {$quantity}",
            ]);

            // Update product stock if tracking
            if ($product->track_stock) {
                $product->decrement('stock_quantity', $quantity);
                
                // Update in_stock status
                if ($product->stock_quantity <= 0) {
                    $product->update(['in_stock' => false]);
                }
            }

            // Complete transaction to trigger events (points will be calculated automatically)
            $transaction->complete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Sale completed successfully! Loyalty points have been added to customer account.'),
                'data' => [
                    'transaction' => $transaction->load(['user', 'product']),
                    'customer' => $customer,
                    'product' => $product,
                    'amount' => $finalAmount,
                    'points_earned' => $transaction->loyalty_points_earned,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while processing the sale.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process sale with manual card number entry
     */
    public function processSaleManual(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'card_number' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Construct QR data manually
        $qrData = [
            'type' => 'card',
            'card_number' => $validated['card_number'],
            'timestamp' => now()->timestamp,
        ];

        $request->merge(['qr_data' => json_encode($qrData)]);

        return $this->processSale($request);
    }
}

