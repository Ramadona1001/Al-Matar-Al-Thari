<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Offer;
use App\Models\Coupon;
use App\Models\Transaction;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:merchant']);
    }

    public function index()
    {
        $company = auth()->user()->company;
        
        if (!$company) {
            return redirect()->route('merchant.company.create')->with('warning', 'Please create your company first.');
        }

        $stats = $this->getDashboardStats($company);
        $recentOffers = $this->getRecentOffers($company);
        $recentCoupons = $this->getRecentCoupons($company);
        $recentTransactions = $this->getRecentTransactions($company);
        $chartData = $this->getChartData($company);
        $topCustomers = $this->getTopCustomers($company);

        return view('merchant.dashboard', compact('stats', 'recentOffers', 'recentCoupons', 'recentTransactions', 'chartData', 'topCustomers'));
    }

    private function getDashboardStats($company)
    {
        return [
            'total_offers' => Offer::where('company_id', $company->id)->count(),
            'active_offers' => Offer::where('company_id', $company->id)->where('status', 'active')->count(),
            'total_coupons' => Coupon::whereHas('offer', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count(),
            'used_coupons' => Coupon::whereHas('offer', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->whereHas('couponUsages')->count(),
            'total_transactions' => Transaction::where('company_id', $company->id)->count(),
            'total_revenue' => Transaction::where('company_id', $company->id)
                ->where('status', 'completed')
                ->sum('amount'),
            'monthly_revenue' => Transaction::where('company_id', $company->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'total_customers' => Transaction::where('company_id', $company->id)
                ->distinct('user_id')
                ->count('user_id'),
            'loyalty_points_issued' => LoyaltyPoint::where('company_id', $company->id)
                ->where('type', 'earned')
                ->sum('points'),
            'loyalty_points_redeemed' => LoyaltyPoint::where('company_id', $company->id)
                ->where('type', 'redeemed')
                ->sum('points'),
        ];
    }

    private function getRecentOffers($company, $limit = 5)
    {
        return Offer::where('company_id', $company->id)
            ->with(['category', 'coupons'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentCoupons($company, $limit = 5)
    {
        return Coupon::whereHas('offer', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['offer', 'usage'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentTransactions($company, $limit = 5)
    {
        return Transaction::where('company_id', $company->id)
            ->with(['user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getChartData($company)
    {
        $revenueData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total')
        )
        ->where('company_id', $company->id)
        ->where('status', 'completed')
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date');

        $couponUsageData = Coupon::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->whereHas('offer', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('count', 'date');

        return [
            'revenue_data' => $revenueData,
            'coupon_usage_data' => $couponUsageData,
        ];
    }

    private function getTopCustomers($company, $limit = 5)
    {
        return Transaction::select('user_id', DB::raw('COUNT(*) as transaction_count'), DB::raw('SUM(amount) as total_spent'))
            ->where('company_id', $company->id)
            ->where('status', 'completed')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->with(['user'])
            ->get();
    }

    public function getNotifications()
    {
        return auth()->user()->notifications()->latest()->limit(5)->get();
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
}