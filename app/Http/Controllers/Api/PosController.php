<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosDevice;
use App\Models\PosTransaction;
use App\Services\PointsService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    protected $pointsService;
    protected $qrCodeService;

    public function __construct(PointsService $pointsService, QrCodeService $qrCodeService)
    {
        $this->pointsService = $pointsService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Authenticate POS device
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'api_key' => 'required|string',
            'serial_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $device = PosDevice::where('device_id', $request->device_id)
            ->where('api_key', $request->api_key)
            ->where('serial_number', $request->serial_number)
            ->where('status', 'active')
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid device credentials'
            ], 401);
        }

        // Update last active time and IP
        $device->update([
            'last_active_at' => now(),
            'last_ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device authenticated successfully',
            'data' => [
                'device' => $device->load(['company', 'branch']),
                'company' => $device->company,
                'branch' => $device->branch
            ]
        ]);
    }

    /**
     * Process POS transaction
     */
    public function processTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'api_key' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,qr_code,digital_card',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string',
            'digital_card_number' => 'nullable|string',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid transaction data',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Authenticate device
        $device = $this->authenticateDevice($request->device_id, $request->api_key);
        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device authentication failed',
            ], 401);
        }

        DB::beginTransaction();
        try {
            $originalAmount = $request->amount;
            $discountAmount = 0;
            $taxAmount = $request->tax_amount ?? 0;
            $user = null;
            $coupon = null;
            $digitalCard = null;

            // Process coupon if provided
            if ($request->coupon_code) {
                $couponResult = $this->processCoupon($request->coupon_code, $device);
                if (!$couponResult['success']) {
                    DB::rollBack();
                    return response()->json($couponResult, 400);
                }
                $coupon = $couponResult['coupon'];
                $discountAmount += $couponResult['discount_amount'];
            }

            // Process digital card if provided
            if ($request->digital_card_number) {
                $cardResult = $this->processDigitalCard($request->digital_card_number, $device);
                if (!$cardResult['success']) {
                    DB::rollBack();
                    return response()->json($cardResult, 400);
                }
                $digitalCard = $cardResult['digital_card'];
                $discountAmount += $cardResult['discount_amount'];
            }

            $finalAmount = max(0, $originalAmount + $taxAmount - $discountAmount);

            // Create POS transaction
            $posTransaction = PosTransaction::create([
                'pos_device_id' => $device->id,
                'company_id' => $device->company_id,
                'branch_id' => $device->branch_id,
                'user_id' => $user?->id,
                'coupon_id' => $coupon?->id,
                'digital_card_id' => $digitalCard?->id,
                'amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'items' => $request->items,
                'status' => 'pending',
            ]);

            // Create main transaction record
            $transaction = Transaction::create([
                'transaction_id' => $posTransaction->transaction_id,
                'amount' => $originalAmount,
                'original_price' => $originalAmount, // Store original price for points calculation
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'status' => 'pending', // Set as pending first
                'payment_method' => $request->payment_method,
                'user_id' => $user?->id,
                'company_id' => $device->company_id,
                'branch_id' => $device->branch_id,
                'coupon_id' => $coupon?->id,
                'digital_card_id' => $digitalCard?->id,
            ]);

            // Complete transaction to trigger events
            $transaction->complete();

            // Complete POS transaction
            $posTransaction->complete();

            // Award loyalty points if applicable
            if ($transaction->loyalty_points_earned > 0 && $user) {
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'company_id' => $device->company_id,
                    'points' => $transaction->loyalty_points_earned,
                    'type' => 'earned',
                    'source_type' => Transaction::class,
                    'source_id' => $transaction->id,
                    'description' => 'Points earned from POS transaction ' . $transaction->transaction_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction processed successfully',
                'data' => [
                    'transaction' => [
                        'id' => $posTransaction->id,
                        'transaction_id' => $posTransaction->transaction_id,
                        'pos_transaction_id' => $posTransaction->pos_transaction_id,
                        'amount' => $originalAmount,
                        'discount_amount' => $discountAmount,
                        'tax_amount' => $taxAmount,
                        'final_amount' => $finalAmount,
                        'payment_method' => $request->payment_method,
                        'status' => 'completed',
                    ],
                    'receipt' => [
                        'items' => $request->items,
                        'subtotal' => $originalAmount,
                        'discount' => $discountAmount,
                        'tax' => $taxAmount,
                        'total' => $finalAmount,
                    ],
                    'loyalty_points_earned' => $transaction->loyalty_points_earned ?? 0,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get POS transaction details
     */
    public function getTransaction(Request $request, $transactionId)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'api_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $device = $this->authenticateDevice($request->device_id, $request->api_key);
        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device authentication failed',
            ], 401);
        }

        $transaction = PosTransaction::where('pos_transaction_id', $transactionId)
                                     ->where('pos_device_id', $device->id)
                                     ->with(['coupon', 'digitalCard', 'user'])
                                     ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction' => $transaction,
                'receipt' => $transaction->receipt_data,
            ],
        ]);
    }

    /**
     * Process coupon validation
     */
    private function processCoupon(string $code, PosDevice $device): array
    {
        $coupon = Coupon::where('code', $code)
                       ->whereHas('offer', function ($query) use ($device) {
                           $query->where('company_id', $device->company_id);
                       })
                       ->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid coupon code',
            ];
        }

        if (!$coupon->isValid()) {
            return [
                'success' => false,
                'message' => 'Coupon is not valid',
            ];
        }

        $discountAmount = $coupon->calculateDiscount(100); // Base amount for calculation

        return [
            'success' => true,
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Process digital card validation
     */
    private function processDigitalCard(string $cardNumber, PosDevice $device): array
    {
        $digitalCard = DigitalCard::where('card_number', $cardNumber)
                                   ->where('company_id', $device->company_id)
                                   ->first();

        if (!$digitalCard) {
            return [
                'success' => false,
                'message' => 'Digital card not found',
            ];
        }

        if (!$digitalCard->isActive()) {
            return [
                'success' => false,
                'message' => 'Digital card is not active or has expired',
            ];
        }

        $discountAmount = $digitalCard->calculateDiscount(100); // Base amount for calculation

        return [
            'success' => true,
            'digital_card' => $digitalCard,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Authenticate POS device
     */
    private function authenticateDevice(string $deviceId, string $apiKey): ?PosDevice
    {
        return PosDevice::where('device_id', $deviceId)
                       ->where('api_key', $apiKey)
                       ->first();
    }
}