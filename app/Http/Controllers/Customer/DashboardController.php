<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\LoyaltyPoint;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index()
    {
        $stats = $this->getDashboardStats();
        $availableOffers = $this->getAvailableOffers();
        $myCoupons = $this->getMyCoupons();
        $recentTransactions = $this->getRecentTransactions();
        $loyaltyPoints = $this->getLoyaltyPoints();
        $favoriteCompanies = $this->getFavoriteCompanies();
        $chartData = $this->getChartData();

        return view('customer.dashboard', compact('stats', 'availableOffers', 'myCoupons', 'recentTransactions', 'loyaltyPoints', 'favoriteCompanies', 'chartData'));
    }

    private function getDashboardStats()
    {
        $user = auth()->user();
        
        return [
            'total_coupons' => Coupon::whereHas('couponUsages', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'active_coupons' => Coupon::whereHas('couponUsages', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'active')->count(),
            'total_transactions' => Transaction::where('user_id', $user->id)->count(),
            'total_spent' => Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('amount'),
            'loyalty_points_balance' => $user->loyalty_points_balance,
            'loyalty_points_earned' => LoyaltyPoint::where('user_id', $user->id)
                ->where('type', 'earned')
                ->sum('points'),
            'loyalty_points_redeemed' => LoyaltyPoint::where('user_id', $user->id)
                ->where('type', 'redeemed')
                ->sum('points'),
            'favorite_companies' => Transaction::where('user_id', $user->id)
                ->distinct('company_id')
                ->count('company_id'),
        ];
    }

    private function getAvailableOffers($limit = 6)
    {
        return Offer::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['company', 'category', 'coupons'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getMyCoupons($limit = 5)
    {
        return Coupon::whereHas('couponUsages', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['offer.company', 'couponUsages'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentTransactions($limit = 5)
    {
        return Transaction::where('user_id', auth()->id())
            ->with(['company'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getLoyaltyPoints()
    {
        return LoyaltyPoint::where('user_id', auth()->id())
            ->with(['company'])
            ->latest()
            ->limit(5)
            ->get();
    }

    private function getFavoriteCompanies($limit = 5)
    {
        return Company::select('companies.*', DB::raw('COUNT(transactions.id) as transaction_count'))
            ->join('transactions', 'companies.id', '=', 'transactions.company_id')
            ->where('transactions.user_id', auth()->id())
            ->groupBy('companies.id')
            ->orderByDesc('transaction_count')
            ->limit($limit)
            ->get();
    }

    private function getChartData()
    {
        $user = auth()->user();
        
        $spendingData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total')
        )
        ->where('user_id', $user->id)
        ->where('status', 'completed')
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date');

        $loyaltyPointsData = LoyaltyPoint::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(points) as points')
        )
        ->where('user_id', $user->id)
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('points', 'date');

        return [
            'spending_data' => $spendingData,
            'loyalty_points_data' => $loyaltyPointsData,
        ];
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