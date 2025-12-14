<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Generate and download invoice PDF for a transaction.
     */
    public function download(Request $request, $transaction)
    {
        // Resolve transaction model
        $transaction = Transaction::findOrFail($transaction);
        
        // Check authorization based on user role
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? 'customer';

        // Admin can view all transactions
        if ($userRole === 'super-admin' || $userRole === 'admin') {
            // Admin can view any transaction
        }
        // Merchant can only view their company's transactions
        elseif ($userRole === 'merchant' || $userRole === 'partner') {
            if ($transaction->company_id !== $user->company?->id) {
                abort(403, 'Unauthorized access to this transaction.');
            }
        }
        // Customer can only view their own transactions
        else {
            if ($transaction->user_id !== $user->id) {
                abort(403, 'Unauthorized access to this transaction.');
            }
        }

        // Load relationships
        $transaction->load(['user', 'company', 'branch', 'coupon', 'product']);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.transaction', compact('transaction'));
        
        // Set filename
        $filename = 'invoice-' . $transaction->transaction_id . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * View invoice PDF in browser.
     */
    public function view(Request $request, $transaction)
    {
        // Resolve transaction model
        $transaction = Transaction::findOrFail($transaction);
        
        // Check authorization based on user role
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? 'customer';

        // Admin can view all transactions
        if ($userRole === 'super-admin' || $userRole === 'admin') {
            // Admin can view any transaction
        }
        // Merchant can only view their company's transactions
        elseif ($userRole === 'merchant' || $userRole === 'partner') {
            if ($transaction->company_id !== $user->company?->id) {
                abort(403, 'Unauthorized access to this transaction.');
            }
        }
        // Customer can only view their own transactions
        else {
            if ($transaction->user_id !== $user->id) {
                abort(403, 'Unauthorized access to this transaction.');
            }
        }

        // Load relationships
        $transaction->load(['user', 'company', 'branch', 'coupon', 'product']);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.transaction', compact('transaction'));
        
        return $pdf->stream('invoice-' . $transaction->transaction_id . '.pdf');
    }
}

