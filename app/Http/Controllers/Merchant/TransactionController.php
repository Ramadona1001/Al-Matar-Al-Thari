<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant|partner']);
    }

    public function index(Request $request)
    {
        $company = auth()->user()->company;
        
        if (!$company) {
            return redirect()->route('merchant.company.create')->with('warning', 'Please create your company first.');
        }

        $transactions = Transaction::where('company_id', $company->id)
            ->with(['user', 'branch', 'coupon'])
            ->latest()
            ->paginate(50);

        return view('merchant.transactions.index', compact('transactions'));
    }
}

