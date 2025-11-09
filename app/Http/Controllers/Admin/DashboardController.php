<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Offer;
use App\Models\Coupon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentUsers = $this->getRecentUsers();
        $recentCompanies = $this->getRecentCompanies();
        $recentTransactions = $this->getRecentTransactions();
        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCompanies', 'recentTransactions', 'chartData'));
    }

    private function getDashboardStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_companies' => Company::count(),
            'approved_companies' => Company::where('status', 'approved')->count(),
            'total_offers' => Offer::count(),
            'active_offers' => Offer::where('status', 'active')->count(),
            'total_coupons' => Coupon::count(),
            'used_coupons' => Coupon::whereHas('couponUsages')->count(),
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
            'pending_companies' => Company::where('status', 'pending')->count(),
            'today_users' => User::whereDate('created_at', today())->count(),
        ];
    }

    private function getRecentUsers($limit = 5)
    {
        return User::with('roles')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentCompanies($limit = 5)
    {
        return Company::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentTransactions($limit = 5)
    {
        return Transaction::with(['user', 'company'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getChartData()
    {
        $userGrowth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('count', 'date');

        $transactionData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->where('status', 'completed')
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date');

        return [
            'user_growth' => $userGrowth,
            'transaction_data' => $transactionData,
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