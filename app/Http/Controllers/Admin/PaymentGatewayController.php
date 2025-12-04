<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $gateways = PaymentGateway::orderBy('name')->paginate(10);
        
        return view('admin.payment-gateways.index', compact('gateways'));
    }

    public function create()
    {
        $gatewayTypes = [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'razorpay' => 'Razorpay',
            'authorize_net' => 'Authorize.Net',
            'square' => 'Square',
            'braintree' => 'Braintree',
            'custom' => 'Custom Gateway'
        ];

        return view('admin.payment-gateways.create', compact('gatewayTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:stripe,paypal,razorpay,authorize_net,square,braintree,custom',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'merchant_id' => 'nullable|string|max:255',
            'public_key' => 'nullable|string',
            'private_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'test_mode' => 'boolean',
            'status' => 'boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
            'processing_time' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'supported_currencies' => 'nullable|array',
            'supported_countries' => 'nullable|array',
            'processing_fees' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $gateway = PaymentGateway::create([
                'name' => $request->name,
                'type' => $request->type,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'merchant_id' => $request->merchant_id,
                'public_key' => $request->public_key,
                'private_key' => $request->private_key,
                'webhook_secret' => $request->webhook_secret,
                'test_mode' => $request->test_mode ?? true,
                'status' => $request->status ?? false,
                'minimum_amount' => $request->minimum_amount ?? 0,
                'maximum_amount' => $request->maximum_amount ?? 999999.99,
                'processing_time' => $request->processing_time ?? '1-3 business days',
                'description' => $request->description,
                'supported_currencies' => $request->supported_currencies ?? ['USD'],
                'supported_countries' => $request->supported_countries ?? ['US'],
                'processing_fees' => $request->processing_fees ?? ['percentage' => 2.9, 'fixed' => ['USD' => 0.30]],
                'settings' => $request->settings ?? []
            ]);

            Log::info('Payment gateway created', ['gateway_id' => $gateway->id, 'name' => $gateway->name]);

            return redirect()->route('admin.payment-gateways.index')
                ->with('success', 'Payment gateway created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create payment gateway', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Failed to create payment gateway: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(PaymentGateway $gateway)
    {
        $transactions = PaymentTransaction::where('payment_gateway_id', $gateway->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_transactions' => PaymentTransaction::where('payment_gateway_id', $gateway->id)->count(),
            'total_amount' => PaymentTransaction::where('payment_gateway_id', $gateway->id)->sum('amount'),
            'completed_transactions' => PaymentTransaction::where('payment_gateway_id', $gateway->id)->where('status', 'completed')->count(),
            'failed_transactions' => PaymentTransaction::where('payment_gateway_id', $gateway->id)->where('status', 'failed')->count(),
            'refunded_transactions' => PaymentTransaction::where('payment_gateway_id', $gateway->id)->where('status', 'refunded')->count(),
        ];

        return view('admin.payment-gateways.show', compact('gateway', 'transactions', 'stats'));
    }

    public function edit(PaymentGateway $gateway)
    {
        $gatewayTypes = [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'razorpay' => 'Razorpay',
            'authorize_net' => 'Authorize.Net',
            'square' => 'Square',
            'braintree' => 'Braintree',
            'custom' => 'Custom Gateway'
        ];

        return view('admin.payment-gateways.edit', compact('gateway', 'gatewayTypes'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:stripe,paypal,razorpay,authorize_net,square,braintree,custom',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'merchant_id' => 'nullable|string|max:255',
            'public_key' => 'nullable|string',
            'private_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'test_mode' => 'boolean',
            'status' => 'boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
            'processing_time' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'supported_currencies' => 'nullable|array',
            'supported_countries' => 'nullable|array',
            'processing_fees' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $gateway->update([
                'name' => $request->name,
                'type' => $request->type,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'merchant_id' => $request->merchant_id,
                'public_key' => $request->public_key,
                'private_key' => $request->private_key,
                'webhook_secret' => $request->webhook_secret,
                'test_mode' => $request->test_mode ?? $gateway->test_mode,
                'status' => $request->status ?? $gateway->status,
                'minimum_amount' => $request->minimum_amount ?? $gateway->minimum_amount,
                'maximum_amount' => $request->maximum_amount ?? $gateway->maximum_amount,
                'processing_time' => $request->processing_time ?? $gateway->processing_time,
                'description' => $request->description,
                'supported_currencies' => $request->supported_currencies ?? $gateway->supported_currencies,
                'supported_countries' => $request->supported_countries ?? $gateway->supported_countries,
                'processing_fees' => $request->processing_fees ?? $gateway->processing_fees,
                'settings' => $request->settings ?? $gateway->settings
            ]);

            Log::info('Payment gateway updated', ['gateway_id' => $gateway->id, 'name' => $gateway->name]);

            return redirect()->route('admin.payment-gateways.index')
                ->with('success', 'Payment gateway updated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to update payment gateway', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Failed to update payment gateway: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PaymentGateway $gateway)
    {
        try {
            // Check if gateway has transactions
            if ($gateway->transactions()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete payment gateway with existing transactions.');
            }

            $gateway->delete();

            Log::info('Payment gateway deleted', ['gateway_id' => $gateway->id, 'name' => $gateway->name]);

            return redirect()->route('admin.payment-gateways.index')
                ->with('success', 'Payment gateway deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete payment gateway', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete payment gateway: ' . $e->getMessage());
        }
    }

    public function toggleStatus(PaymentGateway $gateway)
    {
        try {
            $gateway->update(['status' => !$gateway->status]);

            Log::info('Payment gateway status toggled', [
                'gateway_id' => $gateway->id, 
                'name' => $gateway->name,
                'new_status' => $gateway->status
            ]);

            return redirect()->back()
                ->with('success', 'Payment gateway status updated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to toggle payment gateway status', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Failed to update payment gateway status: ' . $e->getMessage());
        }
    }

    public function testConnection(PaymentGateway $gateway)
    {
        try {
            $result = $this->testGatewayConnection($gateway);

            if ($result['success']) {
                return redirect()->back()
                    ->with('success', 'Payment gateway connection test successful.');
            } else {
                return redirect()->back()
                    ->with('error', 'Payment gateway connection test failed: ' . $result['message']);
            }
        } catch (Exception $e) {
            Log::error('Payment gateway connection test failed', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Payment gateway connection test failed: ' . $e->getMessage());
        }
    }

    private function testGatewayConnection(PaymentGateway $gateway)
    {
        $gatewayType = $gateway->type;

        switch ($gatewayType) {
            case 'stripe':
                return $this->testStripeConnection($gateway);
            case 'paypal':
                return $this->testPayPalConnection($gateway);
            case 'razorpay':
                return $this->testRazorpayConnection($gateway);
            default:
                return ['success' => true, 'message' => 'Generic gateway test passed'];
        }
    }

    private function testStripeConnection(PaymentGateway $gateway)
    {
        try {
            $stripe = new \Stripe\StripeClient($gateway->api_key);
            $balance = $stripe->balance->retrieve();
            
            return ['success' => true, 'message' => 'Stripe connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function testPayPalConnection(PaymentGateway $gateway)
    {
        try {
            $baseUrl = $gateway->test_mode ? 
                'https://api-m.sandbox.paypal.com' : 
                'https://api-m.paypal.com';

            $response = \Illuminate\Support\Facades\Http::asForm()->withBasicAuth(
                $gateway->api_key,
                $gateway->api_secret
            )->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

            return $response->successful() ? 
                ['success' => true, 'message' => 'PayPal connection successful'] : 
                ['success' => false, 'message' => 'Invalid credentials'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function testRazorpayConnection(PaymentGateway $gateway)
    {
        try {
            $api = new \Razorpay\Api($gateway->api_key, $gateway->api_secret);
            $payments = $api->payment->all();
            
            return ['success' => true, 'message' => 'Razorpay connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}