<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Affiliate;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    protected $gateway;
    protected $transaction;

    public function __construct(PaymentGateway $gateway = null)
    {
        $this->gateway = $gateway;
    }

    public function setGateway(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function createTransaction($data)
    {
        if (!$this->gateway) {
            throw new Exception('Payment gateway not set');
        }

        if (!$this->gateway->isActive()) {
            throw new Exception('Payment gateway is not active');
        }

        if (!$this->gateway->supportsCurrency($data['currency'] ?? 'USD')) {
            throw new Exception('Currency not supported by this gateway');
        }

        $feeAmount = $this->gateway->getProcessingFee($data['amount'], $data['currency'] ?? 'USD');
        $netAmount = $data['amount'] - $feeAmount;

        return DB::transaction(function () use ($data, $feeAmount, $netAmount) {
            $transaction = PaymentTransaction::create([
                'transaction_id' => $this->generateTransactionId(),
                'payment_gateway_id' => $this->gateway->id,
                'user_id' => $data['user_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'affiliate_id' => $data['affiliate_id'] ?? null,
                'type' => $data['type'] ?? 'payment',
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'card',
                'payer_info' => $data['payer_info'] ?? null,
                'recipient_info' => $data['recipient_info'] ?? null,
            ]);

            $this->transaction = $transaction;
            return $transaction;
        });
    }

    public function processPayment(PaymentTransaction $transaction, $paymentData = [])
    {
        try {
            if (!$this->gateway) {
                $this->gateway = $transaction->paymentGateway;
            }

            $result = $this->executePayment($transaction, $paymentData);

            if ($result['success']) {
                $transaction->markAsCompleted(
                    $result['gateway_transaction_id'] ?? null,
                    $result['gateway_response'] ?? null
                );

                // Handle affiliate payout
                if ($transaction->type === 'affiliate_payout' && $transaction->affiliate_id) {
                    $this->processAffiliatePayout($transaction);
                }

                // Handle company subscription
                if ($transaction->type === 'subscription_payment' && $transaction->company_id) {
                    $this->processSubscriptionPayment($transaction);
                }

                return [
                    'success' => true,
                    'transaction' => $transaction,
                    'message' => 'Payment processed successfully'
                ];
            } else {
                $transaction->markAsFailed(
                    $result['error'] ?? 'Payment failed',
                    $result['gateway_response'] ?? null
                );

                return [
                    'success' => false,
                    'transaction' => $transaction,
                    'message' => $result['error'] ?? 'Payment failed'
                ];
            }
        } catch (Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'gateway' => $this->gateway->name,
                'error' => $e->getMessage()
            ]);

            $transaction->markAsFailed($e->getMessage());

            return [
                'success' => false,
                'transaction' => $transaction,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    protected function executePayment(PaymentTransaction $transaction, $paymentData)
    {
        $gatewayType = $this->gateway->type;

        switch ($gatewayType) {
            case 'stripe':
                return $this->executeStripePayment($transaction, $paymentData);
            case 'paypal':
                return $this->executePayPalPayment($transaction, $paymentData);
            case 'razorpay':
                return $this->executeRazorpayPayment($transaction, $paymentData);
            default:
                return $this->executeGenericPayment($transaction, $paymentData);
        }
    }

    protected function executeStripePayment(PaymentTransaction $transaction, $paymentData)
    {
        try {
            // Stripe payment implementation
            $stripe = new \Stripe\StripeClient($this->gateway->api_key);
            
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $transaction->amount * 100, // Convert to cents
                'currency' => strtolower($transaction->currency),
                'payment_method' => $paymentData['payment_method_id'] ?? null,
                'confirm' => true,
                'return_url' => $paymentData['return_url'] ?? route('payment.return'),
                'metadata' => [
                    'transaction_id' => $transaction->transaction_id,
                    'user_id' => $transaction->user_id,
                ]
            ]);

            return [
                'success' => $paymentIntent->status === 'succeeded',
                'gateway_transaction_id' => $paymentIntent->id,
                'gateway_response' => $paymentIntent->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function executePayPalPayment(PaymentTransaction $transaction, $paymentData)
    {
        try {
            // PayPal payment implementation
            $baseUrl = $this->gateway->test_mode ? 
                'https://api-m.sandbox.paypal.com' : 
                'https://api-m.paypal.com';

            // Get access token
            $authResponse = Http::asForm()->withBasicAuth(
                $this->gateway->api_key,
                $this->gateway->api_secret
            )->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

            if (!$authResponse->successful()) {
                throw new Exception('Failed to get PayPal access token');
            }

            $accessToken = $authResponse->json()['access_token'];

            // Create payment
            $paymentResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $transaction->transaction_id,
                    'amount' => [
                        'currency_code' => $transaction->currency,
                        'value' => number_format($transaction->amount, 2, '.', '')
                    ],
                    'description' => $transaction->description
                ]]
            ]);

            if (!$paymentResponse->successful()) {
                throw new Exception('Failed to create PayPal order');
            }

            $orderData = $paymentResponse->json();

            return [
                'success' => true,
                'gateway_transaction_id' => $orderData['id'],
                'gateway_response' => $orderData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function executeRazorpayPayment(PaymentTransaction $transaction, $paymentData)
    {
        try {
            // Razorpay payment implementation
            $api = new \Razorpay\Api($this->gateway->api_key, $this->gateway->api_secret);

            $order = $api->order->create([
                'amount' => $transaction->amount * 100, // Convert to paise
                'currency' => $transaction->currency,
                'receipt' => $transaction->transaction_id,
                'payment_capture' => 1
            ]);

            return [
                'success' => true,
                'gateway_transaction_id' => $order->id,
                'gateway_response' => $order->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function executeGenericPayment(PaymentTransaction $transaction, $paymentData)
    {
        // Generic payment gateway implementation
        // This would be customized based on specific gateway requirements
        
        Log::info('Processing generic payment', [
            'transaction_id' => $transaction->id,
            'gateway' => $this->gateway->name,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency
        ]);

        // Simulate successful payment for demonstration
        return [
            'success' => true,
            'gateway_transaction_id' => 'GENERIC_' . uniqid(),
            'gateway_response' => [
                'message' => 'Payment processed successfully',
                'timestamp' => now()->toISOString()
            ]
        ];
    }

    public function refundTransaction(PaymentTransaction $transaction, $amount = null, $reason = null)
    {
        try {
            if (!$transaction->isCompleted()) {
                throw new Exception('Only completed transactions can be refunded');
            }

            $refundAmount = $amount ?? $transaction->amount;

            if ($refundAmount > $transaction->amount) {
                throw new Exception('Refund amount cannot exceed transaction amount');
            }

            $result = $this->executeRefund($transaction, $refundAmount, $reason);

            if ($result['success']) {
                $transaction->markAsRefunded($refundAmount, $reason);

                return [
                    'success' => true,
                    'transaction' => $transaction,
                    'message' => 'Refund processed successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['error'] ?? 'Refund failed'
                ];
            }
        } catch (Exception $e) {
            Log::error('Refund processing error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'amount' => $refundAmount,
                'reason' => $reason
            ]);

            return [
                'success' => false,
                'message' => 'Refund processing failed: ' . $e->getMessage()
            ];
        }
    }

    protected function executeRefund(PaymentTransaction $transaction, $amount, $reason)
    {
        $gatewayType = $this->gateway->type;

        switch ($gatewayType) {
            case 'stripe':
                return $this->executeStripeRefund($transaction, $amount, $reason);
            case 'paypal':
                return $this->executePayPalRefund($transaction, $amount, $reason);
            default:
                return $this->executeGenericRefund($transaction, $amount, $reason);
        }
    }

    protected function executeStripeRefund(PaymentTransaction $transaction, $amount, $reason)
    {
        try {
            $stripe = new \Stripe\StripeClient($this->gateway->api_key);
            
            $refund = $stripe->refunds->create([
                'payment_intent' => $transaction->gateway_transaction_id,
                'amount' => $amount * 100, // Convert to cents
                'reason' => $reason ? 'requested_by_customer' : null,
                'metadata' => [
                    'original_transaction_id' => $transaction->transaction_id,
                    'refund_reason' => $reason
                ]
            ]);

            return [
                'success' => $refund->status === 'succeeded',
                'gateway_response' => $refund->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function executePayPalRefund(PaymentTransaction $transaction, $amount, $reason)
    {
        try {
            $baseUrl = $this->gateway->test_mode ? 
                'https://api-m.sandbox.paypal.com' : 
                'https://api-m.paypal.com';

            // Get access token
            $authResponse = Http::asForm()->withBasicAuth(
                $this->gateway->api_key,
                $this->gateway->api_secret
            )->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

            if (!$authResponse->successful()) {
                throw new Exception('Failed to get PayPal access token');
            }

            $accessToken = $authResponse->json()['access_token'];

            // Create refund
            $refundResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($baseUrl . '/v2/payments/captures/' . $transaction->gateway_transaction_id . '/refund', [
                'amount' => [
                    'currency_code' => $transaction->currency,
                    'value' => number_format($amount, 2, '.', '')
                ],
                'note_to_payer' => $reason
            ]);

            return [
                'success' => $refundResponse->successful(),
                'gateway_response' => $refundResponse->json()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function executeGenericRefund(PaymentTransaction $transaction, $amount, $reason)
    {
        // Generic refund implementation
        Log::info('Processing generic refund', [
            'transaction_id' => $transaction->id,
            'amount' => $amount,
            'reason' => $reason
        ]);

        return [
            'success' => true,
            'gateway_response' => [
                'message' => 'Refund processed successfully',
                'amount' => $amount,
                'reason' => $reason
            ]
        ];
    }

    protected function processAffiliatePayout(PaymentTransaction $transaction)
    {
        $affiliate = $transaction->affiliate;
        if ($affiliate) {
            // Update affiliate balance
            $affiliate->decrement('pending_balance', $transaction->amount);
            $affiliate->increment('total_paid', $transaction->amount);
            $affiliate->increment('total_payouts', 1);

            // Log affiliate payout
            Log::info('Affiliate payout processed', [
                'affiliate_id' => $affiliate->id,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency
            ]);
        }
    }

    protected function processSubscriptionPayment(PaymentTransaction $transaction)
    {
        $company = $transaction->company;
        if ($company) {
            // Update company subscription status
            $company->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addMonth()
            ]);

            // Log subscription payment
            Log::info('Company subscription payment processed', [
                'company_id' => $company->id,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency
            ]);
        }
    }

    protected function generateTransactionId()
    {
        return 'TXN_' . strtoupper(uniqid() . bin2hex(random_bytes(4)));
    }
}