@extends('layouts.dashboard')

@section('title', __('Customers'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-0">{{ __('All Customers') }}</h2>
        <p class="text-muted mb-0">{{ __('View all customers who have transacted with your company') }}</p>
    </div>
</div>

<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
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
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('customer') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('transactions') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('total_spent') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
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
        
        @if($customers->hasPages())
            <div class="mt-4">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

