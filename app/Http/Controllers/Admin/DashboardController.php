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
        $cmsStats = $this->getCmsStats();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCompanies', 'recentTransactions', 'chartData', 'cmsStats'));
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

    public function getStatistics()
    {
        return response()->json($this->getDashboardStats());
    }

    public function getChartDataPublic()
    {
        $chartData = $this->getChartData();
        return response()->json($chartData);
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

    private function getCmsStats()
    {
        $stats = [];
        
        // Sections
        if (class_exists(\App\Models\Section::class)) {
            $stats['total_sections'] = \App\Models\Section::count();
            $stats['visible_sections'] = \App\Models\Section::where('is_visible', true)->count();
        } else {
            $stats['total_sections'] = 0;
            $stats['visible_sections'] = 0;
        }
        
        // Banners
        if (class_exists(\App\Models\Banner::class)) {
            $stats['total_banners'] = \App\Models\Banner::count();
            $stats['active_banners'] = \App\Models\Banner::where('is_active', true)->count();
        } else {
            $stats['total_banners'] = 0;
            $stats['active_banners'] = 0;
        }
        
        // Menus
        if (class_exists(\App\Models\Menu::class)) {
            $stats['total_menus'] = \App\Models\Menu::count();
            $stats['active_menus'] = \App\Models\Menu::where('is_active', true)->count();
        } else {
            $stats['total_menus'] = 0;
            $stats['active_menus'] = 0;
        }
        
        // Services
        if (class_exists(\App\Models\Service::class)) {
            $stats['total_services'] = \App\Models\Service::count();
            $stats['active_services'] = \App\Models\Service::where('is_active', true)->count();
        } else {
            $stats['total_services'] = 0;
            $stats['active_services'] = 0;
        }
        
        // Blogs
        if (class_exists(\App\Models\Blog::class)) {
            $stats['total_blogs'] = \App\Models\Blog::count();
            $stats['published_blogs'] = \App\Models\Blog::where('is_published', true)->count();
        } else {
            $stats['total_blogs'] = 0;
            $stats['published_blogs'] = 0;
        }
        
        // Testimonials
        if (class_exists(\App\Models\Testimonial::class)) {
            $stats['total_testimonials'] = \App\Models\Testimonial::count();
            $stats['active_testimonials'] = \App\Models\Testimonial::where('is_active', true)->count();
        } else {
            $stats['total_testimonials'] = 0;
            $stats['active_testimonials'] = 0;
        }
        
        // Statistics
        if (class_exists(\App\Models\Statistic::class)) {
            $stats['total_statistics'] = \App\Models\Statistic::count();
            $stats['active_statistics'] = \App\Models\Statistic::where('is_active', true)->count();
        } else {
            $stats['total_statistics'] = 0;
            $stats['active_statistics'] = 0;
        }
        
        // Pages
        if (class_exists(\App\Models\Page::class)) {
            $stats['total_pages'] = \App\Models\Page::count();
            $stats['published_pages'] = \App\Models\Page::where('is_published', true)->count();
        } else {
            $stats['total_pages'] = 0;
            $stats['published_pages'] = 0;
        }
        
        return $stats;
    }
}