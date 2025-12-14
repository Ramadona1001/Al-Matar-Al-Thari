@extends('layouts.dashboard')

@section('title', 'Merchant Dashboard')

@section('content')
@php($metricCards = [
    [
        'label' => __('total_offers'),
        'value' => number_format($stats['total_offers']),
        'icon' => 'fas fa-tags',
        'gradient' => 'indigo',
        'trend' => __(':count_active', ['count' => number_format($stats['active_offers'])]),
    ],
    [
        'label' => __('total_coupons'),
        'value' => number_format($stats['total_coupons']),
        'icon' => 'fas fa-ticket-alt',
        'gradient' => 'pink',
        'trend' => __(':count_used', ['count' => number_format($stats['used_coupons'])]),
    ],
    [
        'label' => __('total_revenue'),
        'value' => '﷼' . number_format($stats['total_revenue'], 2),
        'icon' => 'fas fa-sack-dollar',
        'gradient' => 'emerald',
        'trend' => __('last_30_days_amount', ['amount' => number_format($stats['revenue_30_days'] ?? 0, 2)]),
    ],
    [
        'label' => __('total_customers'),
        'value' => number_format($stats['total_customers']),
        'icon' => 'fas fa-users',
        'gradient' => 'teal',
        'trend' => __('new_this_month_count', ['count' => number_format($stats['new_customers_month'] ?? 0)]),
    ],
])
@php($performanceMetrics = [
    [
        'title' => __('coupon_usage_rate'),
        'value' => $stats['total_coupons'] > 0 ? round(($stats['used_coupons'] / max($stats['total_coupons'], 1)) * 100, 1) : 0,
        'percent' => $stats['total_coupons'] > 0 ? round(($stats['used_coupons'] / max($stats['total_coupons'], 1)) * 100) : 0,
        'color' => 'bg-primary',
        'hint' => __('redemptions_vs_generated'),
    ],
    [
        'title' => __('offer_activation'),
        'value' => $stats['total_offers'] > 0 ? round(($stats['active_offers'] / max($stats['total_offers'], 1)) * 100, 1) : 0,
        'percent' => $stats['total_offers'] > 0 ? round(($stats['active_offers'] / max($stats['total_offers'], 1)) * 100) : 0,
        'color' => 'bg-success',
        'hint' => __('live_offers_today'),
    ],
    [
        'title' => __('monthly_revenue_target'),
        'value' => 75,
        'percent' => 75,
        'color' => 'bg-warning',
        'hint' => __(':amount_achieved', ['amount' => '﷼' . number_format($stats['monthly_revenue'] ?? 0, 2)]),
    ],
])

<section class="section-intro fade-in-up mb-5">
    <div class="row g-4 align-items-center">
        <div class="col-xl-8">
            <div class="text-white">
                <span class="badge rounded-pill bg-white text-primary bg-opacity-15 mb-3">{{ __('merchant_snapshot') }}</span>
                <h2 class="display-6 fw-semibold mb-3">{{ __('track_performance_launch_new_offers_and_celebrate_loyal_customers') }}</h2>
                <p class="text-white-50 mb-0">{{ __('see_revenue_trends_coupon_activity_and_loyalty_highlights') }}</p>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="glass-card p-4 h-100 text-white fade-in-up delay-1">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="text-white-50 text-uppercase small fw-semibold mb-1">{{ __('todays_pulse') }}</p>
                        <h4 class="fw-semibold mb-0">{{ __('live_insights') }}</h4>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25 text-white">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
                <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                    <li class="d-flex justify-content-between"><span>{{ __('offers_expiring_soon') }}</span><span class="fw-semibold text-white">{{ number_format($stats['offers_expiring'] ?? 0) }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('coupons_redeemed_today') }}</span><span class="fw-semibold text-white">{{ number_format($stats['coupons_redeemed_today'] ?? 0) }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('loyalty_points_issued') }}</span><span class="fw-semibold text-white">{{ number_format($stats['loyalty_points_issued_today'] ?? 0) }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="metrics-grid fade-in-up">
        @foreach($metricCards as $metric)
            <div class="metric-card" data-gradient="{{ $metric['gradient'] }}">
                <div class="metric-top">
                    <div>
                        <div class="metric-label">{{ $metric['label'] }}</div>
                        <div class="metric-value">{{ $metric['value'] }}</div>
                    </div>
                    <div class="metric-icon">
                        <i class="{{ $metric['icon'] }}"></i>
                    </div>
                </div>
                <span class="metric-trend">
                    <i class="fas fa-arrow-trend-up"></i>
                    {{ $metric['trend'] }}
                </span>
            </div>
        @endforeach
    </div>
</section>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Revenue Chart Card -->
    <div class="col-xxl-8">
        <div class="card modern-card h-100 shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="chart-icon-wrapper bg-primary-subtle">
                                <i class="fas fa-chart-line text-primary"></i>
                            </div>
                            <div>
                                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('revenue_trend') }}</p>
                                <h5 class="fw-bold mb-0 text-gray-900">{{ __('revenue_performance') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active period-btn" data-period="7d">7D</button>
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="30d">30D</button>
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="90d">90D</button>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="chart-container-modern" style="height: 320px; position: relative;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Performance Metrics Card -->
    <div class="col-xxl-4">
        <div class="card modern-card h-100 shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-success-subtle">
                        <i class="fas fa-chart-pie text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('performance') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('metrics') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex flex-column gap-4">
                    @foreach($performanceMetrics as $metric)
                        <div class="modern-metric-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="metric-title fw-semibold text-gray-700">{{ $metric['title'] }}</span>
                                <span class="badge rounded-pill {{ $metric['color'] }} text-white px-3 py-2 fw-semibold">{{ $metric['value'] }}%</span>
                            </div>
                            <div class="progress-modern" style="height: 10px; border-radius: 10px; background: #f1f5f9; overflow: hidden;">
                                <div class="progress-bar-modern {{ $metric['color'] }}" 
                                     role="progressbar" 
                                     style="width: {{ $metric['percent'] }}%; height: 100%; border-radius: 10px; transition: width 0.6s ease;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted small">
                                    <i class="fas fa-signal me-1"></i>
                                    {{ $metric['hint'] }}
                                </span>
                                <span class="badge bg-light text-dark small">{{ $metric['percent'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr class="my-4">
                <div class="quick-actions d-grid gap-2">
                    <a href="{{ route('merchant.offers.create') }}" class="btn btn-primary btn-animated shadow-sm">
                        <i class="fas fa-plus me-2"></i>{{ __('create_new_offer') }}
                    </a>
                    <a href="{{ route('merchant.coupons.create') }}" class="btn btn-outline-primary btn-animated shadow-sm">
                        <i class="fas fa-ticket me-2"></i>{{ __('generate_coupons') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="row g-4 mb-4">
    <!-- Recent Offers Table Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-info-subtle">
                            <i class="fas fa-tags text-info"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('offer_pipeline') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('recent_offers') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('merchant.offers.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0" data-dt-init="false">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('title') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('category') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('status') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('coupons') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOffers as $offer)
                                <tr class="table-row-modern">
                                    <td>
                                        <div>
                                            <div class="fw-semibold text-gray-900">{{ is_array($offer->title) ? ($offer->title[app()->getLocale()] ?? $offer->title['en'] ?? '') : $offer->title }}</div>
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="fas fa-info-circle" style="font-size: 0.7rem;"></i>
                                                {{ Str::limit(is_array($offer->description) ? ($offer->description[app()->getLocale()] ?? $offer->description['en'] ?? '') : ($offer->description ?? ''), 50) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ $offer->category->localized_name ?? __('n/a') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $offer->status === 'active' ? 'bg-success-subtle text-success' : ($offer->status === 'inactive' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} rounded-pill px-3 py-2 fw-semibold">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                            {{ ucfirst($offer->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">{{ $offer->coupons->count() }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-tags"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_offers_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- All Customers Table Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-warning-subtle">
                            <i class="fas fa-users text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('customers') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('all_customers') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0" data-dt-init="false">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('customer') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('transactions') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('total_spent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allCustomers as $customer)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-wrapper">
                                                <img src="{{ $customer->user->avatar ? asset('storage/' . $customer->user->avatar) : asset('images/default-avatar.png') }}" 
                                                     class="avatar-img" 
                                                     alt="Avatar"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->user->first_name . ' ' . $customer->user->last_name) }}&background=3b82f6&color=fff'">
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-gray-900">{{ $customer->user->first_name }} {{ $customer->user->last_name }}</div>
                                                <small class="text-muted d-flex align-items-center gap-1">
                                                    <i class="fas fa-envelope" style="font-size: 0.7rem;"></i>
                                                    {{ $customer->user->email }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">{{ $customer->transaction_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-success fw-semibold">﷼{{ number_format($customer->total_spent, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-users-slash"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_customers_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Transactions Section -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-success-subtle">
                            <i class="fas fa-exchange-alt text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('transactions') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('all_transactions') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0" data-dt-init="false">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('transaction_id') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('customer') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('amount') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('status') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('payment_method') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('branch') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allTransactions as $transaction)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="fw-semibold text-gray-900">{{ $transaction->transaction_id }}</div>
                                        @if($transaction->coupon)
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="fas fa-ticket-alt" style="font-size: 0.7rem;"></i>
                                                {{ __('coupon_used') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="fw-semibold text-gray-900">{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</div>
                                        </div>
                                        <small class="text-muted">{{ $transaction->user->email }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-gray-900">﷼{{ number_format($transaction->amount, 2) }}</div>
                                        @if($transaction->discount_amount > 0)
                                            <small class="text-success">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ __('discount') }}: ﷼{{ number_format($transaction->discount_amount, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->status === 'completed' ? 'bg-success-subtle text-success' : ($transaction->status === 'pending' ? 'bg-warning-subtle text-warning' : ($transaction->status === 'refunded' ? 'bg-info-subtle text-info' : 'bg-danger-subtle text-danger')) }} rounded-pill px-3 py-2 fw-semibold">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ $transaction->payment_method ?? __('n/a') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $transaction->branch ? $transaction->branch->name : __('n/a') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="text-muted small">{{ $transaction->created_at->format('Y-m-d') }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $transaction->created_at->format('H:i') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-exchange-alt"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_transactions_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Charts Section -->
<div class="row g-4 mb-4">
    <!-- Coupon Usage Chart Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-purple-subtle">
                        <i class="fas fa-ticket-alt text-purple"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('coupon_insights') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('coupon_usage_trend') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="chart-container-modern" style="height: 320px; position: relative;">
                    <canvas id="couponUsageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loyalty Points Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-teal-subtle">
                        <i class="fas fa-star text-teal"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('loyalty_summary') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('loyalty_points_overview') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row text-center g-0 mb-4">
                    <div class="col-6">
                        <div class="modern-metric-item">
                            <span class="metric-title fw-semibold text-gray-700 d-block mb-2">{{ __('points_issued') }}</span>
                            <div class="value text-primary fw-bold" style="font-size: 1.75rem;">{{ number_format($stats['loyalty_points_issued']) }}</div>
                            <span class="text-muted small d-block mt-2">
                                <i class="fas fa-circle text-primary" style="font-size:0.4rem"></i> {{ __('lifetime') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="modern-metric-item">
                            <span class="metric-title fw-semibold text-gray-700 d-block mb-2">{{ __('points_redeemed') }}</span>
                            <div class="value text-success fw-bold" style="font-size: 1.75rem;">{{ number_format($stats['loyalty_points_redeemed']) }}</div>
                            <span class="text-muted small d-block mt-2">
                                <i class="fas fa-circle text-success" style="font-size:0.4rem"></i> {{ __('Lifetime') }}
                            </span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h3 class="fw-bold text-warning mb-1">{{ number_format($stats['loyalty_points_issued'] - $stats['loyalty_points_redeemed']) }}</h3>
                    <p class="text-muted mb-0 fw-semibold">{{ __('active_points') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Enhanced Chart Configuration
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14, weight: '600' },
                bodyFont: { size: 13 },
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: { size: 12 },
                    color: '#6b7280'
                }
            },
            y: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    font: { size: 12 },
                    color: '#6b7280'
                },
                beginAtZero: true
            }
        }
    };

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartData['revenue_data']->toArray())) !!},
            datasets: [{
                label: "{{ __('revenue') }}",
                data: {!! json_encode(array_values($chartData['revenue_data']->toArray())) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgb(59, 130, 246)',
                pointBorderWidth: 2
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                y: {
                    ...chartOptions.scales.y,
                    ticks: {
                        ...chartOptions.scales.y.ticks,
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Coupon Usage Chart
    const couponUsageCtx = document.getElementById('couponUsageChart').getContext('2d');
    const couponUsageChart = new Chart(couponUsageCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($chartData['coupon_usage_data']->toArray())) !!},
            datasets: [{
                label: "{{ __('coupons_used') }}",
                data: {!! json_encode(array_values($chartData['coupon_usage_data']->toArray())) !!},
                backgroundColor: 'rgba(168, 85, 247, 0.8)',
                borderColor: 'rgba(168, 85, 247, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: chartOptions
    });

    // Chart period buttons
    document.querySelectorAll('.period-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons in the group
            this.parentElement.querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            // Add active class to clicked button
            this.classList.add('active');
        });
    });
</script>
@endpush
