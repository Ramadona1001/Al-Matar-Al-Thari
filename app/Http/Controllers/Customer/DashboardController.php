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

    public function transactions(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['company']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15);

        return view('customer.transactions.index', compact('transactions'));
    }

    public function favorites(Request $request)
    {
        $userId = auth()->id();
        
        // Get company IDs with transaction counts
        $companyStats = Transaction::select('company_id', DB::raw('COUNT(*) as transaction_count'), DB::raw('SUM(amount) as total_spent'))
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->groupBy('company_id')
            ->orderByDesc('transaction_count')
            ->get();
        
        // Get companies and add transaction data
        $companyIds = $companyStats->pluck('company_id');
        $companyData = $companyStats->keyBy('company_id');
        
        $query = Company::whereIn('id', $companyIds);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }
        
        $companies = $query->get()
            ->map(function($company) use ($companyData) {
                $data = $companyData[$company->id] ?? null;
                $company->transaction_count = $data->transaction_count ?? 0;
                $company->total_spent = $data->total_spent ?? 0;
                return $company;
            })
            ->sortByDesc('transaction_count')
            ->values();
        
        // Paginate manually
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $items = $companies->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $companies->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('customer.favorites.index', compact('paginated'));
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
        $userId = auth()->id();
        
        // Get company IDs with transaction counts using subquery
        $companyStats = Transaction::select('company_id', DB::raw('COUNT(*) as transaction_count'))
            ->where('user_id', $userId)
            ->groupBy('company_id')
            ->orderByDesc('transaction_count')
            ->limit($limit)
            ->get();
        
        // Get companies and add transaction_count
        $companyIds = $companyStats->pluck('company_id');
        $transactionCounts = $companyStats->pluck('transaction_count', 'company_id');
        
        return Company::whereIn('id', $companyIds)
            ->get()
            ->map(function($company) use ($transactionCounts) {
                $company->transaction_count = $transactionCounts[$company->id] ?? 0;
                return $company;
            })
            ->sortByDesc('transaction_count')
            ->values();
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

    public function getStatistics()
    {
        return response()->json($this->getDashboardStats());
    }

    public function getChartDataPublic()
    {
        $chartData = $this->getChartData();
        return response()->json($chartData);
    }

    public function getAvailableOffersPublic()
    {
        $offers = $this->getAvailableOffers();
        return response()->json($offers);
    }

    public function getMyCouponsPublic()
    {
        $coupons = $this->getMyCoupons();
        return response()->json($coupons);
    }

    public function getRecentTransactionsPublic()
    {
        $transactions = $this->getRecentTransactions();
        return response()->json($transactions);
    }

    public function getLoyaltyPointsPublic()
    {
        $loyaltyPoints = $this->getLoyaltyPoints();
        return response()->json($loyaltyPoints);
    }

    public function getFavoriteCompaniesPublic()
    {
        $companies = $this->getFavoriteCompanies();
        return response()->json($companies);
    }

    public function getNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->limit(5)->get();
        return response()->json($notifications);
    }

    public function markNotificationsAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
}