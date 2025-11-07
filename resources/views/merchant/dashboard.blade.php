@extends('layouts.dashboard')

@section('title', 'Merchant Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['total_offers']) }}</div>
                    <div class="stats-label">{{ __('Total Offers') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['total_coupons']) }}</div>
                    <div class="stats-label">{{ __('Total Coupons') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stats-label">{{ __('Total Revenue') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['total_customers']) }}</div>
                    <div class="stats-label">{{ __('Total Customers') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>{{ __('Revenue Trend') }}
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-period="7d">7D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="30d">30D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="90d">90D</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <h5 class="mb-3">
                <i class="fas fa-bolt me-2"></i>{{ __('Quick Actions') }}
            </h5>
            <div class="d-grid gap-2">
                <a href="{{ route('merchant.offers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('Create New Offer') }}
                </a>
                <a href="{{ route('merchant.coupons.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-ticket-alt me-2"></i>{{ __('Generate Coupons') }}
                </a>
                <a href="{{ route('merchant.customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-users me-2"></i>{{ __('View Customers') }}
                </a>
                <a href="{{ route('merchant.analytics.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-chart-bar me-2"></i>{{ __('View Analytics') }}
                </a>
            </div>
            
            <hr class="my-3">
            
            <h6 class="mb-2">{{ __('Performance Metrics') }}</h6>
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small>{{ __('Coupon Usage Rate') }}</small>
                    <small>{{ $stats['total_coupons'] > 0 ? round(($stats['used_coupons'] / $stats['total_coupons']) * 100, 1) : 0 }}%</small>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ $stats['total_coupons'] > 0 ? ($stats['used_coupons'] / $stats['total_coupons']) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small>{{ __('Offer Activation Rate') }}</small>
                    <small>{{ $stats['total_offers'] > 0 ? round(($stats['active_offers'] / $stats['total_offers']) * 100, 1) : 0 }}%</small>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $stats['total_offers'] > 0 ? ($stats['active_offers'] / $stats['total_offers']) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <div class="mb-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small>{{ __('Monthly Revenue Target') }}</small>
                    <small>${{ number_format($stats['monthly_revenue'], 2) }}</small>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: 75%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Offers -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>{{ __('Recent Offers') }}
                </h5>
                <a href="{{ route('merchant.offers.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Coupons') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOffers as $offer)
                        <tr>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $offer->title }}</div>
                                    <small class="text-muted">{{ Str::limit($offer->description, 50) }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $offer->category->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $offer->status }}">
                                    {{ ucfirst($offer->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $offer->coupons->count() }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">{{ __('No offers found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-crown me-2"></i>{{ __('Top Customers') }}
                </h5>
                <a href="{{ route('merchant.customers.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Transactions') }}</th>
                            <th>{{ __('Total Spent') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $customer->user->avatar ? asset('storage/' . $customer->user->avatar) : asset('images/default-avatar.png') }}" 
                                         class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold">{{ $customer->user->first_name }} {{ $customer->user->last_name }}</div>
                                        <small class="text-muted">{{ $customer->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->transaction_count }}</span>
                            </td>
                            <td>
                                <span class="text-success fw-semibold">${{ number_format($customer->total_spent, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">{{ __('No customers found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Coupon Usage Chart -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <h5 class="mb-3">
                <i class="fas fa-ticket-alt me-2"></i>{{ __('Coupon Usage Trend') }}
            </h5>
            <div class="chart-container">
                <canvas id="couponUsageChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Loyalty Points Summary -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <h5 class="mb-3">
                <i class="fas fa-star me-2"></i>{{ __('Loyalty Points Summary') }}
            </h5>
            <div class="row text-center">
                <div class="col-6">
                    <div class="border-end">
                        <h4 class="text-primary">{{ number_format($stats['loyalty_points_issued']) }}</h4>
                        <small class="text-muted">{{ __('Points Issued') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div>
                        <h4 class="text-success">{{ number_format($stats['loyalty_points_redeemed']) }}</h4>
                        <small class="text-muted">{{ __('Points Redeemed') }}</small>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <h3 class="text-warning">{{ number_format($stats['loyalty_points_issued'] - $stats['loyalty_points_redeemed']) }}</h3>
                <small class="text-muted">{{ __('Active Points') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartData['revenue_data']->toArray())) !!},
            datasets: [{
                label: '{{ __('Revenue') }}',
                data: {!! json_encode(array_values($chartData['revenue_data']->toArray())) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
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
                label: '{{ __('Coupons Used') }}',
                data: {!! json_encode(array_values($chartData['coupon_usage_data']->toArray())) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons in the group
            this.parentElement.querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            // Add active class to clicked button
            this.classList.add('active');
            
            // Here you would typically make an AJAX call to get new data
            // For now, we'll just show a notification
            showNotification('Chart period changed to ' + this.dataset.period, 'info');
        });
    });
</script>
@endpush