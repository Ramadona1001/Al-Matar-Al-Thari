@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
@php($metricCards = [
    [
        'label' => __('total_users'),
        'value' => number_format($stats['total_users']),
        'icon' => 'fas fa-users',
        'gradient' => 'indigo',
        'trend' => __(':count_new_this_week', ['count' => $stats['new_users_week'] ?? 0]),
    ],
    [
        'label' => __('total_companies'),
        'value' => number_format($stats['total_companies']),
        'icon' => 'fas fa-building',
        'gradient' => 'pink',
        'trend' => __(':percentage_approved', ['percentage' => $stats['total_companies'] > 0 ? number_format(($stats['approved_companies'] / max($stats['total_companies'], 1)) * 100, 0) : 0]),
    ],
    [
        'label' => __('total_offers'),
        'value' => number_format($stats['total_offers']),
        'icon' => 'fas fa-tags',
        'gradient' => 'teal',
        'trend' => __(':percentage_active', ['percentage' => $stats['total_offers'] > 0 ? number_format(($stats['active_offers'] / max($stats['total_offers'], 1)) * 100, 0) : 0]),
    ],
    [
        'label' => __('total_revenue'),
        'value' => '$' . number_format($stats['total_revenue'], 2),
        'icon' => 'fas fa-dollar-sign',
        'gradient' => 'emerald',
        'trend' => __('monthly_target_amount', ['amount' => number_format($stats['monthly_revenue_target'] ?? 0, 0)]),
    ],
])
@php($engagementStats = [
    [
        'title' => __('active_users'),
        'value' => number_format($stats['active_users']),
        'percent' => $stats['total_users'] > 0 ? round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) : 0,
        'color' => 'bg-success',
    ],
    [
        'title' => __('approved_companies'),
        'value' => number_format($stats['approved_companies']),
        'percent' => $stats['total_companies'] > 0 ? round(($stats['approved_companies'] / max($stats['total_companies'], 1)) * 100) : 0,
        'color' => 'bg-info',
    ],
    [
        'title' => __('used_coupons'),
        'value' => number_format($stats['used_coupons']),
        'percent' => $stats['total_coupons'] > 0 ? round(($stats['used_coupons'] / max($stats['total_coupons'], 1)) * 100) : 0,
        'color' => 'bg-primary',
    ],
])

<section class="section-intro fade-in-up mb-5">
    <div class="row g-4 align-items-center">
        <div class="col-xl-8">
            <div class="text-white">
                <span class="badge rounded-pill bg-white text-primary bg-opacity-15 mb-3">{{ __('system_overview') }}</span>
                <h2 class="display-6 fw-semibold mb-3">{{ __('stay_on_top_of_platform_performance_at_a_glance') }}</h2>
                <p class="text-white-50 mb-0">{{ __('monitor_user_growth_merchant_onboarding_and_revenue_trends') }}</p>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="glass-card p-4 h-100 text-white fade-in-up delay-1">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="text-white-50 text-uppercase small fw-semibold mb-1">{{ __('live_snapshot') }}</p>
                        <h4 class="fw-semibold mb-0">{{ __('this_week') }}</h4>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25 text-white">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                    <li class="d-flex justify-content-between"><span>{{ __('new_users') }}</span><span class="fw-semibold text-white">+{{ number_format($stats['new_users_week'] ?? 0) }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('new_companies') }}</span><span class="fw-semibold text-white">+{{ number_format($stats['new_companies_week'] ?? 0) }}</span></li>
                    <li class="d-flex justify-content-between"><span>{{ __('revenue_captured') }}</span><span class="fw-semibold text-white">${{ number_format($stats['revenue_week'] ?? 0, 2) }}</span></li>
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
    <!-- User Growth Chart Card -->
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
                                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('engagement_trend') }}</p>
                                <h5 class="fw-bold mb-0 text-gray-900">{{ __('user_growth') }}</h5>
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
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Platform Health Card -->
    <div class="col-xxl-4">
        <div class="card modern-card h-100 shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="chart-icon-wrapper bg-success-subtle">
                        <i class="fas fa-heartbeat text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('platform_health') }}</p>
                        <h5 class="fw-bold mb-0 text-gray-900">{{ __('metrics') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex flex-column gap-4">
                    @foreach($engagementStats as $engagement)
                        <div class="modern-metric-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="metric-title fw-semibold text-gray-700">{{ $engagement['title'] }}</span>
                                <span class="badge rounded-pill {{ $engagement['color'] }} text-white px-3 py-2 fw-semibold">{{ $engagement['value'] }}</span>
                            </div>
                            <div class="progress-modern" style="height: 10px; border-radius: 10px; background: #f1f5f9; overflow: hidden;">
                                <div class="progress-bar-modern {{ $engagement['color'] }}" 
                                     role="progressbar" 
                                     style="width: {{ $engagement['percent'] }}%; height: 100%; border-radius: 10px; transition: width 0.6s ease;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted small">
                                    <i class="fas fa-signal me-1"></i>
                                    {{ $engagement['percent'] }}% {{ __('of_total') }}
                                </span>
                                <span class="badge bg-light text-dark small">{{ $engagement['percent'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="row g-4 mb-4">
    <!-- Recent Users Table Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-info-subtle">
                            <i class="fas fa-users text-info"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('latest_accounts') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('recent_users') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('name') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('email') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('role') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-wrapper">
                                                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                                     class="avatar-img" 
                                                     alt="Avatar"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=3b82f6&color=fff'">
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                <small class="text-muted d-flex align-items-center gap-1">
                                                    <i class="fas fa-clock" style="font-size: 0.7rem;"></i>
                                                    {{ $user->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-700">{{ $user->email }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-dark text-white rounded-pill px-2 py-1 small">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2 fw-semibold">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                            {{ $user->is_active ? __('active') : __('inactive') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-users-slash"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_users_found') }}</p>
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
    
    <!-- Recent Companies Table Card -->
    <div class="col-xxl-6">
        <div class="card modern-card shadow-sm border-0 fade-in-up delay-1">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-warning-subtle">
                            <i class="fas fa-building text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('merchant_onboarding') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('recent_companies') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-arrow-up-right-from-square me-2"></i>{{ __('view_all') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive-modern">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="table-header-modern">
                            <tr>
                                <th class="fw-semibold text-gray-700">{{ __('company') }}</th>
                                <th class="fw-semibold text-gray-700">{{ __('owner') }}</th>
                                <th class="fw-semibold text-gray-700 text-end">{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompanies as $company)
                                <tr class="table-row-modern">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-wrapper bg-primary-subtle">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-gray-900">{{ $company->localized_name }}</div>
                                                <small class="text-muted d-flex align-items-center gap-1">
                                                    <i class="fas fa-industry" style="font-size: 0.7rem;"></i>
                                                    {{ $company->industry ?? __('n/a') }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-gray-900">{{ $company->user->first_name }} {{ $company->user->last_name }}</div>
                                        <small class="text-muted d-flex align-items-center gap-1">
                                            <i class="fas fa-envelope" style="font-size: 0.7rem;"></i>
                                            {{ $company->user->email }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        @php($statusColors = [
                                            'approved' => 'bg-success-subtle text-success',
                                            'pending' => 'bg-warning-subtle text-warning',
                                            'rejected' => 'bg-danger-subtle text-danger',
                                        ])
                                        <span class="badge {{ $statusColors[$company->status] ?? 'bg-secondary-subtle text-secondary' }} rounded-pill px-3 py-2 fw-semibold">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                            {{ ucfirst($company->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="empty-state-modern">
                                            <div class="empty-icon-wrapper mb-3">
                                                <i class="fas fa-building-circle-xmark"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-semibold">{{ __('no_companies_found') }}</p>
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

<!-- Revenue Chart Card -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-emerald-subtle">
                            <i class="fas fa-chart-bar text-emerald"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('revenue_insight') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('revenue_overview') }}</h5>
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
                <div class="chart-container-modern" style="height: 380px; position: relative;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CMS Management Section -->
@if(isset($cmsStats))
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card modern-card shadow-sm border-0 fade-in-up">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="chart-icon-wrapper bg-purple-subtle">
                            <i class="fas fa-cog text-purple"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('content_management') }}</p>
                            <h5 class="fw-bold mb-0 text-gray-900">{{ __('cms_overview') }}</h5>
                        </div>
                    </div>
                    <a href="{{ route('admin.sections.index') }}" class="btn btn-sm btn-primary btn-animated shadow-sm">
                        <i class="fas fa-cog me-2"></i>{{ __('manage_cms') }}
                    </a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('sections') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_sections'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['visible_sections'] ?? 0) }} {{ __('visible') }}</small>
                                </div>
                                <div class="stat-icon bg-primary-subtle text-primary">
                                    <i class="fas fa-th-large"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('banners') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_banners'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['active_banners'] ?? 0) }} {{ __('active') }}</small>
                                </div>
                                <div class="stat-icon bg-info-subtle text-info">
                                    <i class="fas fa-images"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('services') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_services'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['active_services'] ?? 0) }} {{ __('active') }}</small>
                                </div>
                                <div class="stat-icon bg-success-subtle text-success">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('blog_posts') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_blogs'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['published_blogs'] ?? 0) }} {{ __('published') }}</small>
                                </div>
                                <div class="stat-icon bg-warning-subtle text-warning">
                                    <i class="fas fa-blog"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('testimonials') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_testimonials'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['active_testimonials'] ?? 0) }} {{ __('active') }}</small>
                                </div>
                                <div class="stat-icon bg-purple-subtle text-purple">
                                    <i class="fas fa-quote-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('statistics') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_statistics'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['active_statistics'] ?? 0) }} {{ __('active') }}</small>
                                </div>
                                <div class="stat-icon bg-danger-subtle text-danger">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('menus') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_menus'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['active_menus'] ?? 0) }} {{ __('active') }}</small>
                                </div>
                                <div class="stat-icon bg-teal-subtle text-teal">
                                    <i class="fas fa-bars"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="mini-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">{{ __('pages') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format($cmsStats['total_pages'] ?? 0) }}</h5>
                                    <small class="text-success">{{ number_format($cmsStats['published_pages'] ?? 0) }} {{ __('published') }}</small>
                                </div>
                                <div class="stat-icon bg-indigo-subtle text-indigo">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.sections.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-th-large me-2"></i>{{ __('manage_sections') }}
                        </a>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-images me-2"></i>{{ __('manage_banners') }}
                        </a>
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-bars me-2"></i>{{ __('manage_menus') }}
                        </a>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-concierge-bell me-2"></i>{{ __('manage_services') }}
                        </a>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-blog me-2"></i>{{ __('manage_blogs') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
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

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartData['user_growth']->toArray())) !!},
            datasets: [{
                label: '{{ __('new_users') }}',
                data: {!! json_encode(array_values($chartData['user_growth']->toArray())) !!},
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
            plugins: {
                ...chartOptions.plugins,
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 13, weight: '600' }
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($chartData['transaction_data']->toArray())) !!},
            datasets: [{
                label: '{{ __('revenue') }}',
                data: {!! json_encode(array_values($chartData['transaction_data']->toArray())) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
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
