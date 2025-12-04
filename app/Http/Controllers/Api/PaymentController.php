<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Affiliate;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getPaymentMethods(Request $request)
    {
        try {
            $gateways = PaymentGateway::where('status', true)
                ->where('test_mode', $request->test_mode ?? false)
                ->orderBy('name')
                ->get();

            $methods = [];
            foreach ($gateways as $gateway) {
                $methods[] = [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'type' => $gateway->type,
                    'supported_currencies' => $gateway->supported_currencies,
                    'processing_fees' => $gateway->processing_fees,
                    'minimum_amount' => $gateway->minimum_amount,
                    'maximum_amount' => $gateway->maximum_amount,
                    'config' => $gateway->getConfig()
                ];
            }

            return response()->json([
                'success' => true,
                'methods' => $methods
            ]);
        } catch (Exception $e) {
            Log::error('Failed to get payment methods', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment methods'
            ], 500);
        }
    }

    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_id' => 'required|exists:payment_gateways,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'type' => 'required|string|in:payment,subscription,affiliate_payout',
            'description' => 'required|string|max:500',
            'user_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'affiliate_id' => 'nullable|exists:affiliates,id',
            'payment_method' => 'nullable|string',
            'payer_info' => 'nullable|array',
            'recipient_info' => 'nullable|array',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $gateway = PaymentGateway::findOrFail($request->gateway_id);
            
            if (!$gateway->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway is not active'
                ], 400);
            }

            if (!$gateway->supportsCurrency($request->currency)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Currency not supported by this gateway'
                ], 400);
            }

            // Validate minimum and maximum amounts
            if ($request->amount < $gateway->minimum_amount || $request->amount > $gateway->maximum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Amount must be between {$gateway->minimum_amount} and {$gateway->maximum_amount}"
                ], 400);
            }

            // Additional validation based on transaction type
            if ($request->type === 'affiliate_payout') {
                if (!$request->affiliate_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Affiliate ID is required for affiliate payouts'
                    ], 400);
                }

                $affiliate = Affiliate::findOrFail($request->affiliate_id);
                if ($affiliate->pending_balance < $request->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient affiliate balance'
                    ], 400);
                }
            }

            $transaction = $this->paymentService
                ->setGateway($gateway)
                ->createTransaction($request->all());

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'status' => $transaction->status,
                    'gateway' => $gateway->name,
                    'type' => $transaction->type
                ],
                'message' => 'Payment transaction created successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create payment transaction', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|exists:payment_transactions,transaction_id',
            'payment_data' => 'nullable|array',
            'payment_method_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = PaymentTransaction::where('transaction_id', $request->transaction_id)
                ->with('paymentGateway')
                ->firstOrFail();

            if (!$transaction->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction is not in pending status'
                ], 400);
            }

            $paymentData = array_merge($request->payment_data ?? [], [
                'payment_method_id' => $request->payment_method_id ?? null,
                'return_url' => $request->return_url ?? route('payment.success')
            ]);

            $result = $this->paymentService
                ->setGateway($transaction->paymentGateway)
                ->processPayment($transaction, $paymentData);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Failed to process payment', [
                'transaction_id' => $request->transaction_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionStatus(Request $request, $transactionId)
    {
        try {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                ->with(['paymentGateway', 'user', 'company', 'affiliate'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'status' => $transaction->status,
                    'status_label' => $transaction->getStatusLabel(),
                    'gateway_transaction_id' => $transaction->gateway_transaction_id,
                    'gateway' => $transaction->paymentGateway->name ?? 'N/A',
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at->toISOString(),
                    'processed_at' => $transaction->processed_at ? $transaction->processed_at->toISOString() : null,
                    'user' => $transaction->user ? [
                        'id' => $transaction->user->id,
                        'name' => $transaction->user->name,
                        'email' => $transaction->user->email
                    ] : null,
                    'company' => $transaction->company ? [
                        'id' => $transaction->company->id,
                        'name' => $transaction->company->name
                    ] : null,
                    'affiliate' => $transaction->affiliate ? [
                        'id' => $transaction->affiliate->id,
                        'name' => $transaction->affiliate->name
                    ] : null
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to get transaction status', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
    }

    public function processRefund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|exists:payment_transactions,transaction_id',
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = PaymentTransaction::where('transaction_id', $request->transaction_id)
                ->with('paymentGateway')
                ->firstOrFail();

            if (!$transaction->isCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only completed transactions can be refunded'
                ], 400);
            }

            $refundAmount = $request->amount ?? $transaction->amount;

            if ($refundAmount > $transaction->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund amount cannot exceed transaction amount'
                ], 400);
            }

            $result = $this->paymentService
                ->setGateway($transaction->paymentGateway)
                ->refundTransaction($transaction, $refundAmount, $request->reason);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Failed to process refund', [
                'transaction_id' => $request->transaction_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request, $gatewayType)
    {
        try {
            Log::info('Payment webhook received', [
                'gateway' => $gatewayType,
                'payload' => $request->all()
            ]);

            $gateway = PaymentGateway::where('type', $gatewayType)
                ->where('status', true)
                ->first();

            if (!$gateway) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gateway not found or inactive'
                ], 404);
            }

            // Process webhook based on gateway type
            switch ($gatewayType) {
                case 'stripe':
                    return $this->processStripeWebhook($request, $gateway);
                case 'paypal':
                    return $this->processPayPalWebhook($request, $gateway);
                case 'razorpay':
                    return $this->processRazorpayWebhook($request, $gateway);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Webhook not implemented for this gateway'
                    ], 501);
            }
        } catch (Exception $e) {
            Log::error('Webhook processing failed', [
                'gateway' => $gatewayType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    private function processStripeWebhook(Request $request, PaymentGateway $gateway)
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? null;

        switch ($eventType) {
            case 'payment_intent.succeeded':
                $this->handleSuccessfulPayment($payload['data']['object'], $gateway);
                break;
            case 'payment_intent.payment_failed':
                $this->handleFailedPayment($payload['data']['object'], $gateway);
                break;
            case 'charge.refunded':
                $this->handleRefund($payload['data']['object'], $gateway);
                break;
        }

        return response()->json(['success' => true]);
    }

    private function processPayPalWebhook(Request $request, PaymentGateway $gateway)
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;

        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                $this->handleSuccessfulPayment($payload['resource'], $gateway);
                break;
            case 'CHECKOUT.ORDER.CANCELLED':
                $this->handleFailedPayment($payload['resource'], $gateway);
                break;
        }

        return response()->json(['success' => true]);
    }

    private function processRazorpayWebhook(Request $request, PaymentGateway $gateway)
    {
        $payload = $request->all();
        $eventType = $payload['event'] ?? null;

        switch ($eventType) {
            case 'payment.captured':
                $this->handleSuccessfulPayment($payload['payload']['payment']['entity'], $gateway);
                break;
            case 'payment.failed':
                $this->handleFailedPayment($payload['payload']['payment']['entity'], $gateway);
                break;
        }

        return response()->json(['success' => true]);
    }

    private function handleSuccessfulPayment($paymentData, PaymentGateway $gateway)
    {
        $transactionId = $this->extractTransactionId($paymentData, $gateway->type);
        
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
            
            if ($transaction && $transaction->isPending()) {
                $transaction->markAsCompleted(
                    $this->extractGatewayTransactionId($paymentData, $gateway->type),
                    $paymentData
                );
                
                Log::info('Payment marked as completed via webhook', [
                    'transaction_id' => $transaction->id,
                    'gateway_transaction_id' => $transaction->gateway_transaction_id
                ]);
            }
        }
    }

    private function handleFailedPayment($paymentData, PaymentGateway $gateway)
    {
        $transactionId = $this->extractTransactionId($paymentData, $gateway->type);
        
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
            
            if ($transaction && $transaction->isPending()) {
                $errorMessage = $this->extractErrorMessage($paymentData, $gateway->type);
                $transaction->markAsFailed($errorMessage, $paymentData);
                
                Log::info('Payment marked as failed via webhook', [
                    'transaction_id' => $transaction->id,
                    'error' => $errorMessage
                ]);
            }
        }
    }

    private function handleRefund($paymentData, PaymentGateway $gateway)
    {
        $transactionId = $this->extractTransactionId($paymentData, $gateway->type);
        
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
            
            if ($transaction && $transaction->isCompleted()) {
                $refundAmount = $this->extractRefundAmount($paymentData, $gateway->type);
                $transaction->markAsRefunded($refundAmount, 'Refunded via webhook');
                
                Log::info('Payment marked as refunded via webhook', [
                    'transaction_id' => $transaction->id,
                    'refund_amount' => $refundAmount
                ]);
            }
        }
    }

    private function extractTransactionId($paymentData, $gatewayType)
    {
        switch ($gatewayType) {
            case 'stripe':
                return $paymentData['metadata']['transaction_id'] ?? null;
            case 'paypal':
                return $paymentData['reference_id'] ?? null;
            case 'razorpay':
                return $paymentData['notes']['transaction_id'] ?? null;
            default:
                return null;
        }
    }

    private function extractGatewayTransactionId($paymentData, $gatewayType)
    {
        switch ($gatewayType) {
            case 'stripe':
                return $paymentData['id'] ?? null;
            case 'paypal':
                return $paymentData['id'] ?? null;
            case 'razorpay':
                return $paymentData['id'] ?? null;
            default:
                return null;
        }
    }

    private function extractErrorMessage($paymentData, $gatewayType)
    {
        switch ($gatewayType) {
            case 'stripe':
                return $paymentData['last_payment_error']['message'] ?? 'Payment failed';
            case 'paypal':
                return 'Payment cancelled';
            case 'razorpay':
                return $paymentData['error_description'] ?? 'Payment failed';
            default:
                return 'Payment failed';
        }
    }

    private function extractRefundAmount($paymentData, $gatewayType)
    {
        switch ($gatewayType) {
            case 'stripe':
                return ($paymentData['amount_refunded'] ?? 0) / 100; // Convert from cents
            case 'paypal':
                return $paymentData['amount']['value'] ?? 0;
            case 'razorpay':
                return ($paymentData['amount'] ?? 0) / 100; // Convert from paise
            default:
                return 0;
        }
    }
}