<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.analytics.index');
    }

    public function metrics(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->startOfDay());
        $dateTo = $request->input('date_to', now()->endOfDay());

        $payments = DB::table('payment_transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount, COALESCE(SUM(net_amount), 0) as total_net')
            ->first();

        $affiliates = DB::table('affiliate_sales')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(commission_amount), 0) as total_commission')
            ->first();

        $coupons = DB::table('coupon_usage')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(discount_amount), 0) as total_discount')
            ->first();

        return response()->json([
            'success' => true,
            'metrics' => [
                'payments' => $payments,
                'affiliates' => $affiliates,
                'coupons' => $coupons,
            ],
        ]);
    }

    public function transactionsChartData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->startOfDay());
        $dateTo = $request->input('date_to', now()->endOfDay());
        $status = $request->input('status');

        $query = DB::table('payment_transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($status) {
            $query->where('status', $status);
        }

        $rows = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount, COALESCE(SUM(net_amount), 0) as total_net')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $rows->pluck('date');
        $count = $rows->pluck('count');
        $gross = $rows->pluck('total_amount');
        $net = $rows->pluck('total_net');

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Transactions', 'data' => $count],
                ['label' => 'Gross Amount', 'data' => $gross],
                ['label' => 'Net Amount', 'data' => $net],
            ],
        ]);
    }

    public function affiliatePerformanceData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->startOfDay());
        $dateTo = $request->input('date_to', now()->endOfDay());

        $rows = DB::table('affiliate_sales')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, COALESCE(SUM(commission_amount), 0) as total_commission')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $rows->pluck('date'),
            'datasets' => [
                ['label' => 'Affiliate Conversions', 'data' => $rows->pluck('count')],
                ['label' => 'Commission', 'data' => $rows->pluck('total_commission')],
            ],
        ]);
    }

    public function couponUsageData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->startOfDay());
        $dateTo = $request->input('date_to', now()->endOfDay());

        $rows = DB::table('coupon_usage')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, COALESCE(SUM(discount_amount), 0) as total_discount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $rows->pluck('date'),
            'datasets' => [
                ['label' => 'Coupon Uses', 'data' => $rows->pluck('count')],
                ['label' => 'Discount Given', 'data' => $rows->pluck('total_discount')],
            ],
        ]);
    }

    public function loyaltyPointsData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->startOfDay());
        $dateTo = $request->input('date_to', now()->endOfDay());

        $rows = DB::table('loyalty_points')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, COALESCE(SUM(points_awarded), 0) as total_points')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $rows->pluck('date'),
            'datasets' => [
                ['label' => 'Points Events', 'data' => $rows->pluck('count')],
                ['label' => 'Points Awarded', 'data' => $rows->pluck('total_points')],
            ],
        ]);
    }
}