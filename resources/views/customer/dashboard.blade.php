@extends('layouts.dashboard')

@section('title', 'Customer Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['active_coupons']) }}</div>
                    <div class="stats-label">{{ __('Active Coupons') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">${{ number_format($stats['total_spent'], 2) }}</div>
                    <div class="stats-label">{{ __('Total Spent') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['loyalty_points_balance']) }}</div>
                    <div class="stats-label">{{ __('Loyalty Points') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Available Offers -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>{{ __('Available Offers') }}
                </h5>
                <a href="{{ route('customer.offers.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="row">
                @forelse($availableOffers as $offer)
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">{{ $offer->title }}</h6>
                                <span class="badge bg-primary">{{ $offer->discount_percentage }}% OFF</span>
                            </div>
                            <p class="card-text text-muted small">{{ Str::limit($offer->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-building me-1"></i>{{ $offer->company->name }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $offer->end_date->diffForHumans() }}
                                </small>
                            </div>
                            <div class="mt-2">
                                <div class="progress mb-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $offer->coupons->count() > 0 ? ($offer->coupons->where('status', 'active')->count() / $offer->coupons->count()) * 100 : 0 }}%"></div>
                                </div>
                                <small class="text-muted">
                                    {{ $offer->coupons->where('status', 'active')->count() }} / {{ $offer->coupons->count() }} {{ __('coupons available') }}
                                </small>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('customer.offers.show', $offer) }}" class="btn btn-sm btn-outline-primary w-100">
                                    {{ __('View Offer') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{ __('No offers available at the moment') }}</p>
                    </div>
                </div>
                @endforelse
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
                <a href="{{ route('customer.offers.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>{{ __('Browse Offers') }}
                </a>
                <a href="{{ route('customer.coupons.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-ticket-alt me-2"></i>{{ __('My Coupons') }}
                </a>
                <a href="{{ route('customer.transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-history me-2"></i>{{ __('Transaction History') }}
                </a>
                <a href="{{ route('customer.loyalty.index') }}" class="btn btn-outline-warning">
                    <i class="fas fa-star me-2"></i>{{ __('Loyalty Points') }}
                </a>
            </div>
            
            <hr class="my-3">
            
            <h6 class="mb-2">{{ __('Loyalty Points Summary') }}</h6>
            <div class="row text-center">
                <div class="col-4">
                    <div class="border-end">
                        <h5 class="text-primary mb-1">{{ number_format($stats['loyalty_points_earned']) }}</h5>
                        <small class="text-muted">{{ __('Earned') }}</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-end">
                        <h5 class="text-success mb-1">{{ number_format($stats['loyalty_points_redeemed']) }}</h5>
                        <small class="text-muted">{{ __('Redeemed') }}</small>
                    </div>
                </div>
                <div class="col-4">
                    <div>
                        <h5 class="text-warning mb-1">{{ number_format($stats['loyalty_points_balance']) }}</h5>
                        <small class="text-muted">{{ __('Balance') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- My Coupons -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>{{ __('My Coupons') }}
                </h5>
                <a href="{{ route('customer.coupons.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($myCoupons as $coupon)
                <div class="list-group-item border-0 px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $coupon->offer->title }}</h6>
                            <small class="text-muted">{{ $coupon->offer->company->name }}</small>
                            <div class="small text-muted">
                                {{ __('Code') }}: <strong>{{ $coupon->code }}</strong>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($coupon->status === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @elseif($coupon->status === 'used')
                                <span class="badge bg-secondary">{{ __('Used') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Expired') }}</span>
                            @endif
                            <div class="small text-muted mt-1">
                                {{ $coupon->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-3">
                    <i class="fas fa-ticket-alt fa-2x text-muted mb-2"></i>
                    <p class="text-muted">{{ __('No coupons found') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>{{ __('Recent Transactions') }}
                </h5>
                <a href="{{ route('customer.transactions.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentTransactions as $transaction)
                <div class="list-group-item border-0 px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $transaction->company->name }}</h6>
                            <small class="text-muted">{{ $transaction->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold text-success">${{ number_format($transaction->amount, 2) }}</div>
                            <small class="text-muted">{{ ucfirst($transaction->status) }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-3">
                    <i class="fas fa-exchange-alt fa-2x text-muted mb-2"></i>
                    <p class="text-muted">{{ __('No transactions found') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Spending Trend Chart -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>{{ __('Spending Trend') }}
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-period="7d">7D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="30d">30D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="90d">90D</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="spendingChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Favorite Companies -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-heart me-2"></i>{{ __('Favorite Companies') }}
                </h5>
                <a href="{{ route('customer.favorites.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($favoriteCompanies as $company)
                <div class="list-group-item border-0 px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $company->name }}</h6>
                            <small class="text-muted">{{ $company->industry }}</small>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">
                                {{ $company->transaction_count }} {{ __('transactions') }}
                            </div>
                            <div class="small text-success">
                                <i class="fas fa-star me-1"></i>{{ __('Favorite') }}
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-3">
                    <i class="fas fa-heart fa-2x text-muted mb-2"></i>
                    <p class="text-muted">{{ __('No favorite companies yet') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Spending Chart
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    const spendingChart = new Chart(spendingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartData['spending_data']->toArray())) !!},
            datasets: [{
                label: '{{ __('Spending') }}',
                data: {!! json_encode(array_values($chartData['spending_data']->toArray())) !!},
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