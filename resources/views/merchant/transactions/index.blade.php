@extends('layouts.dashboard')

@section('title', __('Transactions'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-0">{{ __('All Transactions') }}</h2>
        <p class="text-muted mb-0">{{ __('View all transactions for your company') }}</p>
    </div>
</div>

<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
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
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('transaction_id') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('customer') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('amount') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('status') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('payment_method') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('branch') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('date') }}</th>
                        <th class="fw-semibold text-gray-700 text-center">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
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
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('merchant.transactions.invoice.download', $transaction) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Download Invoice') }}">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('merchant.transactions.invoice.view', $transaction) }}" target="_blank" class="btn btn-sm btn-outline-info" title="{{ __('View Invoice') }}">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
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
        
        @if($transactions->hasPages())
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

