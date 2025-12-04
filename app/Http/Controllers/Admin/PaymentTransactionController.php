<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\PaymentGateway;
use App\Models\Affiliate;
use App\Models\Company;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentTransactionController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['paymentGateway', 'user', 'company', 'affiliate']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('gateway')) {
            $query->where('payment_gateway_id', $request->gateway);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('affiliate', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        $gateways = PaymentGateway::where('status', true)->orderBy('name')->get();

        return view('admin.payment-transactions.index', compact('transactions', 'gateways'));
    }

    public function show(PaymentTransaction $transaction)
    {
        $transaction->load(['paymentGateway', 'user', 'company', 'affiliate']);

        return view('admin.payment-transactions.show', compact('transaction'));
    }

    public function processRefund(Request $request, PaymentTransaction $transaction)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0|max:' . $transaction->amount,
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!$transaction->isCompleted()) {
            return redirect()->back()
                ->with('error', 'Only completed transactions can be refunded.');
        }

        try {
            $refundAmount = $request->amount ?? $transaction->amount;
            
            $result = $this->paymentService
                ->setGateway($transaction->paymentGateway)
                ->refundTransaction($transaction, $refundAmount, $request->reason);

            if ($result['success']) {
                Log::info('Refund processed successfully', [
                    'transaction_id' => $transaction->id,
                    'refund_amount' => $refundAmount,
                    'reason' => $request->reason
                ]);

                return redirect()->route('admin.payment-transactions.show', $transaction)
                    ->with('success', 'Refund processed successfully.');
            } else {
                return redirect()->back()
                    ->with('error', 'Refund failed: ' . $result['message']);
            }
        } catch (Exception $e) {
            Log::error('Refund processing failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Refund processing failed: ' . $e->getMessage());
        }
    }

    public function processAffiliatePayout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'affiliate_id' => 'required|exists:affiliates,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $affiliate = Affiliate::findOrFail($request->affiliate_id);
            
            // Check if affiliate has sufficient balance
            if ($affiliate->pending_balance < $request->amount) {
                return redirect()->back()
                    ->with('error', 'Insufficient affiliate balance for payout.');
            }

            // Get active payment gateway for affiliate payouts
            $gateway = PaymentGateway::where('status', true)
                ->where('type', 'stripe') // Default to Stripe for payouts
                ->first();

            if (!$gateway) {
                return redirect()->back()
                    ->with('error', 'No active payment gateway found for payouts.');
            }

            // Create transaction
            $transaction = $this->paymentService
                ->setGateway($gateway)
                ->createTransaction([
                    'affiliate_id' => $affiliate->id,
                    'type' => 'affiliate_payout',
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'description' => $request->description,
                    'recipient_info' => [
                        'name' => $affiliate->name,
                        'email' => $affiliate->email,
                        'account_id' => $affiliate->stripe_account_id ?? null
                    ]
                ]);

            // Process payout
            $result = $this->paymentService->processPayment($transaction, [
                'payment_method' => 'stripe_connect',
                'stripe_account' => $affiliate->stripe_account_id ?? null
            ]);

            if ($result['success']) {
                Log::info('Affiliate payout processed successfully', [
                    'affiliate_id' => $affiliate->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $request->amount
                ]);

                return redirect()->route('admin.payment-transactions.show', $transaction)
                    ->with('success', 'Affiliate payout processed successfully.');
            } else {
                return redirect()->back()
                    ->with('error', 'Affiliate payout failed: ' . $result['message']);
            }
        } catch (Exception $e) {
            Log::error('Affiliate payout processing failed', [
                'affiliate_id' => $request->affiliate_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Affiliate payout processing failed: ' . $e->getMessage());
        }
    }

    public function processSubscriptionPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $company = Company::findOrFail($request->company_id);
            
            // Get active payment gateway for subscription payments
            $gateway = PaymentGateway::where('status', true)
                ->whereIn('type', ['stripe', 'paypal'])
                ->first();

            if (!$gateway) {
                return redirect()->back()
                    ->with('error', 'No active payment gateway found for subscription payments.');
            }

            // Create transaction
            $transaction = $this->paymentService
                ->setGateway($gateway)
                ->createTransaction([
                    'company_id' => $company->id,
                    'type' => 'subscription_payment',
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'description' => $request->description,
                    'payer_info' => [
                        'name' => $company->name,
                        'email' => $company->email
                    ]
                ]);

            return redirect()->route('admin.payment-transactions.show', $transaction)
                ->with('info', 'Subscription payment transaction created. Please complete the payment process.');
        } catch (Exception $e) {
            Log::error('Subscription payment creation failed', [
                'company_id' => $request->company_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Subscription payment creation failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = PaymentTransaction::with(['paymentGateway', 'user', 'company', 'affiliate']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('gateway')) {
            $query->where('payment_gateway_id', $request->gateway);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payment-transactions-' . date('Y-m-d') . '.csv"'
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Transaction ID',
                'Gateway',
                'Type',
                'Status',
                'Amount',
                'Currency',
                'Fee',
                'Net Amount',
                'User',
                'Company',
                'Affiliate',
                'Gateway Transaction ID',
                'Payment Method',
                'Description',
                'Processed At',
                'Created At'
            ]);

            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_id,
                    $transaction->paymentGateway->name ?? 'N/A',
                    $transaction->type,
                    $transaction->status,
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->fee_amount,
                    $transaction->net_amount,
                    $transaction->user->name ?? 'N/A',
                    $transaction->company->name ?? 'N/A',
                    $transaction->affiliate->name ?? 'N/A',
                    $transaction->gateway_transaction_id ?? 'N/A',
                    $transaction->payment_method ?? 'N/A',
                    $transaction->description ?? 'N/A',
                    $transaction->processed_at ? $transaction->processed_at->format('Y-m-d H:i:s') : 'N/A',
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}