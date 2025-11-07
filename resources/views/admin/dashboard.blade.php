@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['total_users']) }}</div>
                    <div class="stats-label">{{ __('Total Users') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($stats['total_companies']) }}</div>
                    <div class="stats-label">{{ __('Total Companies') }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
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
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
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
</div>

<div class="row">
    <!-- User Growth Chart -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>{{ __('User Growth') }}
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-period="7d">7D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="30d">30D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="90d">90D</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <h5 class="mb-3">
                <i class="fas fa-chart-pie me-2"></i>{{ __('Quick Stats') }}
            </h5>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ __('Active Users') }}</span>
                    <span class="badge bg-success">{{ number_format($stats['active_users']) }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $stats['total_users'] > 0 ? ($stats['active_users'] / $stats['total_users']) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ __('Approved Companies') }}</span>
                    <span class="badge bg-info">{{ number_format($stats['approved_companies']) }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: {{ $stats['total_companies'] > 0 ? ($stats['approved_companies'] / $stats['total_companies']) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ __('Active Offers') }}</span>
                    <span class="badge bg-warning">{{ number_format($stats['active_offers']) }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: {{ $stats['total_offers'] > 0 ? ($stats['active_offers'] / $stats['total_offers']) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <div class="mb-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ __('Used Coupons') }}</span>
                    <span class="badge bg-primary">{{ number_format($stats['used_coupons']) }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ $stats['total_coupons'] > 0 ? ($stats['used_coupons'] / $stats['total_coupons']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>{{ __('Recent Users') }}
                </h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                         class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-secondary">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">{{ __('No users found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Companies -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>{{ __('Recent Companies') }}
                </h5>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Owner') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompanies as $company)
                        <tr>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $company->name }}</div>
                                    <small class="text-muted">{{ $company->industry }}</small>
                                </div>
                            </td>
                            <td>{{ $company->user->first_name }} {{ $company->user->last_name }}</td>
                            <td>
                                <span class="status-badge status-{{ $company->status }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">{{ __('No companies found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-dollar-sign me-2"></i>{{ __('Revenue Overview') }}
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
</div>
@endsection

@push('scripts')
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($chartData['user_growth']->toArray())) !!},
            datasets: [{
                label: '{{ __('New Users') }}',
                data: {!! json_encode(array_values($chartData['user_growth']->toArray())) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
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

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($chartData['transaction_data']->toArray())) !!},
            datasets: [{
                label: '{{ __('Revenue') }}',
                data: {!! json_encode(array_values($chartData['transaction_data']->toArray())) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
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