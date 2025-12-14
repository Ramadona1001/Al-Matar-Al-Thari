@extends('layouts.dashboard')

@section('title', 'Customer Dashboard')

@section('content')
@php($currentLocale = app()->getLocale())
@php($metricCards = [
    [
        'label' => __('total_coupons'),
        'value' => number_format($stats['total_coupons']),
        'icon' => 'fas fa-ticket',
        'gradient' => 'indigo',
        'trend' => trans(':count_redeemed', ['count' => number_format($stats['used_coupons'] ?? 0)]),
    ],
    [
        'label' => __('active_coupons'),
        'value' => number_format($stats['active_coupons']),
        'icon' => 'fas fa-bolt',
        'gradient' => 'pink',
        'trend' => trans('expires_soon', ['count' => number_format($stats['expiring_coupons'] ?? 0)]),
    ],
    [
        'label' => __('total_spent'),
        'value' => '$' . number_format($stats['total_spent'], 2),
        'icon' => 'fas fa-wallet',
        'gradient' => 'emerald',
        'trend' => trans('this_month', ['amount' => number_format($stats['spent_month'] ?? 0, 2)]),
    ],
    [
        'label' => __('loyalty_points'),
        'value' => number_format($stats['loyalty_points_balance']),
        'icon' => 'fas fa-star',
        'gradient' => 'teal',
        'trend' => trans('redeemable_now', ['count' => number_format($stats['redeemable_points'] ?? 0)]),
    ],
])

<section class="section-intro fade-in-up mb-5">
    <div class="row g-4 align-items-center">
        <div class="col-xl-8">
            <div class="text-white">
                <span class="badge rounded-pill bg-white text-primary bg-opacity-15 mb-3">{{ __('welcome_back') }}</span>
                <h2 class="display-6 fw-semibold mb-3">{{ __('discover_fresh_deals') }}</h2>
                <p class="text-white-50 mb-0">{{ __('your_personal_hub') }}</p>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="glass-card p-4 h-100 text-white fade-in-up delay-1">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="text-white-50 text-uppercase small fw-semibold mb-1">{{ __('today\'s_wins') }}</p>
                        <h4 class="fw-semibold mb-0">{{ __('savings_snapshot') }}</h4>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25 text-white">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>
                <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                    <li class="d-flex justify-content-between"><span>{{ __('coupons_to_use_today') }}</span><span class="fw-semibold text-white">{{ number_format($stats['ready_to_use_coupons'] ?? 0) }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('pending_redemptions') }}</span><span class="fw-semibold text-white">{{ number_format($stats['pending_redemptions'] ?? 0) }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('points_expiring_soon') }}</span><span class="fw-semibold text-white">{{ number_format($stats['points_expiring'] ?? 0) }}</span></li>
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
    <!-- Spending Chart Card -->
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
                                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('spending_insight') }}</p>
                                <h5 class="fw-bold mb-0 text-gray-900">{{ __('spending_trend') }}</h5>
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
                    <canvas id="spendingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Loyalty Points Card -->
    <div class="col-xxl-4">
        <div class="card modern-card h-100 shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-success-subtle">
                        <i class="fas fa-star text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('loyalty_points') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('summary') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row text-center g-0 mb-4">
                    <div class="col-4">
                        <div class="modern-metric-item">
                            <span class="metric-title fw-semibold text-gray-700 d-block mb-2 small">{{ __('earned') }}</span>
                            <div class="value text-primary fw-bold" style="font-size: 1.5rem;">{{ number_format($stats['loyalty_points_earned']) }}</div>
                            <span class="text-muted small d-block mt-2">
                                <i class="fas fa-circle text-primary" style="font-size:0.4rem"></i> {{ __('lifetime') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="modern-metric-item">
                            <span class="metric-title fw-semibold text-gray-700 d-block mb-2 small">{{ __('redeemed') }}</span>
                            <div class="value text-success fw-bold" style="font-size: 1.5rem;">{{ number_format($stats['loyalty_points_redeemed']) }}</div>
                            <span class="text-muted small d-block mt-2">
                                <i class="fas fa-circle text-success" style="font-size:0.4rem"></i> {{ __('all_time') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="modern-metric-item">
                            <span class="metric-title fw-semibold text-gray-700 d-block mb-2 small">{{ __('balance') }}</span>
                            <div class="value text-warning fw-bold" style="font-size: 1.5rem;">{{ number_format($stats['loyalty_points_balance']) }}</div>
                            <span class="text-muted small d-block mt-2">
                                <i class="fas fa-circle text-warning" style="font-size:0.4rem"></i> {{ __('ready') }}
                            </span>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="quick-actions d-grid gap-2">
                    <a href="{{ route('customer.offers.index') }}" class="btn btn-primary btn-animated shadow-sm">
                        <i class="fas fa-search me-2"></i>{{ __('browse_offers') }}
                    </a>
                    <a href="{{ route('customer.coupons.index') }}" class="btn btn-outline-primary btn-animated shadow-sm">
                        <i class="fas fa-ticket me-2"></i>{{ __('my_coupons') }}
                    </a>
                    <a href="{{ route('customer.loyalty.index') }}" class="btn btn-outline-success btn-animated shadow-sm">
                        <i class="fas fa-star me-2"></i>{{ __('loyalty_wallet') }}
                    </a>
                    @if(Route::has('customer.affiliate.index'))
                        <a href="{{ route('customer.affiliate.index') }}" class="btn btn-outline-secondary btn-animated shadow-sm">
                            <i class="fas fa-share-nodes me-2"></i>{{ __('refer_earn') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Section -->
@if(isset($products) && $products->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-success-subtle">
                            <i class="fas fa-box text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Featured Products') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('Products & Offers') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('customer.products.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    @foreach($products as $product)
                        <div class="col-md-3 col-sm-6">
                            <div class="card border-0 shadow-sm h-100 product-card">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . str_replace('/storage/', '', $product->image)) }}" 
                                         alt="{{ $product->localized_name }}" 
                                         class="card-img-top" 
                                         style="height: 180px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-2 text-gray-900" style="min-height: 40px;">
                                        {{ Str::limit($product->localized_name, 50) }}
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-primary fw-bold">{{ number_format($product->price, 2) }} {{ __('SAR') }}</span>
                                        @if($product->is_featured)
                                            <span class="badge bg-warning text-dark"><i class="fas fa-star"></i></span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                        <span><i class="fas fa-store me-1 text-primary"></i>{{ $product->company->localized_name ?? '-' }}</span>
                                        @if($product->isInStock())
                                            <span class="badge bg-success-subtle text-success">{{ __('In Stock') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('Out of Stock') }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('customer.products.show', $product) }}" class="btn btn-sm btn-primary w-100">
                                        {{ __('View Product') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tables Section -->
<div class="row g-4 mb-4">
    <!-- Available Offers Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-info-subtle">
                            <i class="fas fa-tags text-info"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('fresh_for_you') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('available_offers') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('customer.offers.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    @forelse($availableOffers as $offer)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-semibold mb-0 text-gray-900">{{ is_array($offer->title) ? ($offer->title[$currentLocale] ?? $offer->title['en'] ?? '') : ($offer->title ?? __('untitled_offer')) }}</h6>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">{{ $offer->discount_percentage ?? 0 }}%</span>
                                    </div>
                                    <p class="text-muted small mb-2">{{ Str::limit(is_array($offer->description) ? ($offer->description[$currentLocale] ?? $offer->description['en'] ?? '') : ($offer->description ?? ''), 60) }}</p>
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                        <span><i class="fas fa-store me-1 text-primary" style="font-size: 0.7rem;"></i>{{ $offer->company->localized_name ?? $offer->company->name }}</span>
                                        <span><i class="fas fa-clock me-1 text-warning" style="font-size: 0.7rem;"></i>{{ $offer->end_date?->diffForHumans() }}</span>
                                    </div>
                                    <a href="{{ route('customer.offers.show', $offer) }}" class="btn btn-sm btn-outline-primary w-100">{{ __('view_offer') }}</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state-modern text-center py-5">
                                <div class="empty-icon-wrapper mb-3">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <p class="text-muted mb-0 fw-semibold">{{ __('no_offers_available') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- My Coupons Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-warning-subtle">
                            <i class="fas fa-ticket-alt text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('your_coupons') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('my_coupons') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('customer.coupons.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0" data-dt-init="false">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('coupon') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('company') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myCoupons as $coupon)
                                <tr class="table-row-modern">
                                    <td>
                                        <div>
                                            <div class="fw-semibold text-gray-900">{{ is_array($coupon->offer->title) ? ($coupon->offer->title[$currentLocale] ?? $coupon->offer->title['en'] ?? '') : ($coupon->offer->title ?? '') }}</div>
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="fas fa-barcode" style="font-size: 0.7rem;"></i>
                                                {{ $coupon->code }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-700">{{ $coupon->offer->company->localized_name ?? $coupon->offer->company->name }}</span>
                                    </td>
                                    <td class="text-end">
                                        @php($statusClass = match($coupon->status) {
                                            'active' => 'bg-success-subtle text-success',
                                            'used' => 'bg-secondary text-white',
                                            default => 'bg-danger-subtle text-danger'
                                        })
                                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 fw-semibold">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                            {{ __(ucfirst($coupon->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-ticket-alt"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_coupons_found') }}</p>
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

<!-- Additional Tables Section -->
<div class="row g-4 mb-4">
    <!-- Recent Transactions Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-success-subtle">
                            <i class="fas fa-exchange-alt text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('latest_activity') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('recent_transactions') }}</h5>
                        </div>
                    </div>
                    @if(Route::has('customer.transactions.index'))
                        <a href="{{ route('customer.transactions.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                            <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0" data-dt-init="false">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('company') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('date') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="fw-semibold text-gray-900">{{ $transaction->company->localized_name ?? $transaction->company->name }}</div>
                                    </td>
                                    <td>
                                        <small class="text-muted d-flex align-items-center gap-1">
                                            <i class="fas fa-clock" style="font-size: 0.7rem;"></i>
                                            {{ $transaction->created_at->translatedFormat('d M Y H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-semibold text-success">${{ number_format($transaction->amount, 2) }}</span>
                                        <div>
                                            <small class="text-muted">{{ __(ucfirst($transaction->status)) }}</small>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
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
    
    <!-- Favorite Companies Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-purple-subtle">
                            <i class="fas fa-heart text-purple"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('trusted_brands') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('favorite_companies') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('customer.favorites.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0" data-dt-init="false">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('company') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('industry') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('transactions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($favoriteCompanies as $company)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="fw-semibold text-gray-900">{{ $company->localized_name }}</div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $company->industry ?? '-' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">
                                            <i class="fas fa-star me-1" style="font-size: 0.5rem;"></i>
                                            {{ $company->transaction_count ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-heart"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_favourite_companies_yet') }}</p>
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
                    color: '#6b7280',
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                },
                beginAtZero: true
            }
        }
    };

    // Spending Chart
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    const spendingChart = new Chart(spendingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartData['spending_data']->toArray())) !!},
            datasets: [{
                label: "{{ __('spending') }}",
                data: {!! json_encode(array_values($chartData['spending_data']->toArray())) !!},
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
