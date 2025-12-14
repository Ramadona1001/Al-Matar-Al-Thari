<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\DigitalCard;
use App\Models\CouponUsage;
use App\Models\Transaction;
use App\Models\LoyaltyPoint;
use App\Services\PointsService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    protected QrCodeService $qrCodeService;
    protected PointsService $pointsService;

    public function __construct(QrCodeService $qrCodeService, PointsService $pointsService)
    {
        $this->middleware(['auth', 'role:customer']);
        $this->qrCodeService = $qrCodeService;
        $this->pointsService = $pointsService;
    }

    /**
     * Show the QR code scanner page
     */
    public function index()
    {
        return view('customer.scan.index');
    }

    /**
     * Process scanned QR code
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = auth()->user();
        $qrData = $this->qrCodeService->validateQrCode($validated['qr_data']);

        if (!$qrData) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid or expired QR code.'),
            ], 400);
        }

        // Handle different QR code types
        switch ($qrData['type']) {
            case 'coupon':
                return $this->processCouponQrCode($qrData, $user, $validated);
            
            case 'card':
                return $this->processCardQrCode($qrData, $user, $validated);
            
            case 'referral':
                return $this->processReferralQrCode($qrData, $user);
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => __('Unknown QR code type.'),
                ], 400);
        }
    }

    /**
     * Process coupon QR code
     */
    private function processCouponQrCode(array $qrData, $user, array $validated)
    {
        $coupon = Coupon::where('code', $qrData['code'])->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => __('Coupon not found.'),
            ], 404);
        }

        // Validate coupon
        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => __('This coupon is not valid or has expired.'),
            ], 400);
        }

        // Check if user can use this coupon
        if (!$coupon->canBeUsedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => __('You have reached the usage limit for this coupon.'),
            ], 400);
        }

        // Check minimum purchase if amount provided
        if (isset($validated['amount']) && $coupon->minimum_purchase) {
            if ($validated['amount'] < $coupon->minimum_purchase) {
                return response()->json([
                    'success' => false,
                    'message' => __("Minimum purchase of :amount is required.", ['amount' => $coupon->minimum_purchase]),
                ], 400);
            }
        }

        // Calculate discount
        $originalAmount = $validated['amount'] ?? 0;
        $discountAmount = $this->calculateDiscount($coupon, $originalAmount);
        $finalAmount = max(0, $originalAmount - $discountAmount);

        // Record coupon usage
        DB::beginTransaction();
        try {
            $couponUsage = CouponUsage::recordUsage(
                $coupon,
                $user,
                $originalAmount,
                $discountAmount,
                $finalAmount,
                $validated['branch_id'] ?? null
            );

            // Create transaction if amount provided
            if (isset($validated['amount']) && $validated['amount'] > 0) {
                $transaction = Transaction::create([
                    'transaction_id' => Transaction::generateUniqueTransactionId(),
                    'amount' => $originalAmount,
                    'original_price' => $originalAmount, // Store original price for points calculation
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'status' => 'pending', // Set as pending first
                    'payment_method' => 'qr_code',
                    'user_id' => $user->id,
                    'company_id' => $coupon->company_id,
                    'branch_id' => $validated['branch_id'] ?? null,
                    'coupon_id' => $coupon->id,
                ]);

                // Complete transaction to trigger events
                $transaction->complete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Coupon applied successfully.'),
                'data' => [
                    'coupon' => $coupon,
                    'usage' => $couponUsage,
                    'original_amount' => $originalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while processing the coupon.'),
            ], 500);
        }
    }

    /**
     * Process digital card QR code
     */
    private function processCardQrCode(array $qrData, $user, array $validated)
    {
        $digitalCard = DigitalCard::where('card_number', $qrData['card_number'])->first();

        if (!$digitalCard) {
            return response()->json([
                'success' => false,
                'message' => __('Digital card not found.'),
            ], 404);
        }

        // Validate card
        if (!$digitalCard->isActive()) {
            return response()->json([
                'success' => false,
                'message' => __('This digital card is not active or has expired.'),
            ], 400);
        }

        // No discount - card only for earning points
        $originalAmount = $validated['amount'] ?? 0;
        $discountAmount = 0;
        $finalAmount = $originalAmount;

        // Create transaction if amount provided
        if (isset($validated['amount']) && $validated['amount'] > 0) {
            DB::beginTransaction();
            try {
                $transaction = Transaction::create([
                    'transaction_id' => Transaction::generateUniqueTransactionId(),
                    'amount' => $originalAmount,
                    'original_price' => $originalAmount, // Store original price for points calculation
                    'discount_amount' => 0, // No discount from card
                    'final_amount' => $finalAmount,
                    'status' => 'pending', // Set as pending first
                    'payment_method' => 'digital_card',
                    'user_id' => $user->id,
                    'company_id' => null, // Can be set based on branch
                    'branch_id' => $validated['branch_id'] ?? null,
                    'digital_card_id' => $digitalCard->id,
                ]);

                // Complete transaction to trigger events (points will be calculated based on admin settings)
                $transaction->complete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while processing the card.'),
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Digital card applied successfully. Points will be calculated based on purchase amount.'),
            'data' => [
                'card' => $digitalCard,
                'original_amount' => $originalAmount,
                'discount_amount' => 0,
                'final_amount' => $finalAmount,
            ],
        ]);
    }

    /**
     * Process referral QR code
     */
    private function processReferralQrCode(array $qrData, $user)
    {
        // Handle referral QR code
        // This would typically redirect to registration with referral code
        return response()->json([
            'success' => true,
            'message' => __('Referral code detected.'),
            'data' => [
                'referral_code' => $qrData['code'],
                'url' => $qrData['url'] ?? null,
            ],
        ]);
    }

    /**
     * Calculate discount for coupon
     */
    private function calculateDiscount(Coupon $coupon, float $amount): float
    {
        switch ($coupon->type) {
            case 'percentage':
                return ($amount * $coupon->value) / 100;
            
            case 'fixed':
                return min($coupon->value, $amount);
            
            case 'free_shipping':
                // Handle free shipping logic
                return 0; // Or shipping cost
            
            default:
                return 0;
        }
    }

    /**
     * Award loyalty points to user
     */
    private function awardLoyaltyPoints($user, $companyId, float $amount, Transaction $transaction)
    {
        $multiplier = 1.0;
        $digitalCard = $user->digitalCard;
        if ($digitalCard && $digitalCard->isActive()) {
            $benefits = $digitalCard->getBenefits();
            $multiplier = $benefits['loyalty_points_multiplier'] ?? 1.0;
        }

        $points = $this->pointsService->calculateEarnedPoints($amount, $multiplier);

        if ($points > 0) {
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'points' => $points,
                'type' => 'earned',
                'source_type' => Transaction::class,
                'source_id' => $transaction->id,
                'description' => 'Points earned from transaction ' . $transaction->transaction_id,
                'expiry_date' => now()->addYear(), // Points expire after 1 year
            ]);

            // Update transaction with points earned
            $transaction->update(['loyalty_points_earned' => $points]);
        }
    }

    /**
     * Manual QR code entry
     */
    public function manualEntry(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:coupon,card',
            'amount' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Construct QR data based on type
        if ($validated['type'] === 'coupon') {
            $coupon = Coupon::where('code', $validated['code'])->first();
            if (!$coupon) {
                return redirect()->route('customer.scan.index')
                    ->with('error', __('Coupon not found.'));
            }

            $qrData = [
                'type' => 'coupon',
                'code' => $coupon->code,
                'timestamp' => now()->timestamp,
            ];
        } else {
            $digitalCard = DigitalCard::where('card_number', $validated['code'])->first();
            if (!$digitalCard) {
                return redirect()->route('customer.scan.index')
                    ->with('error', __('Digital card not found.'));
            }

            $qrData = [
                'type' => 'card',
                'card_number' => $digitalCard->card_number,
                'timestamp' => now()->timestamp,
            ];
        }

        $request->merge(['qr_data' => json_encode($qrData)]);

        $response = $this->process($request);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            return redirect()->route('customer.scan.index')
                ->with('success', $data['message']);
        } else {
            $data = json_decode($response->getContent(), true);
            return redirect()->route('customer.scan.index')
                ->with('error', $data['message'] ?? __('An error occurred.'));
        }
    }
}

