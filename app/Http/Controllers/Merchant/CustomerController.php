<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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

        $customers = Transaction::select('user_id', DB::raw('COUNT(*) as transaction_count'), DB::raw('SUM(amount) as total_spent'))
            ->where('company_id', $company->id)
            ->where('status', 'completed')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->with(['user'])
            ->paginate(20);

        return view('merchant.customers.index', compact('customers'));
    }
}

