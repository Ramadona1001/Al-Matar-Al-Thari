@extends('layouts.dashboard')

@section('title', __('My Transactions'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Transactions') }}</li>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('My Transactions') }}</h6>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('customer.transactions.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">{{ __('From Date') }}</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">{{ __('To Date') }}</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('Filter') }}
                </button>
            </div>
        </form>

        <!-- Transactions Table -->
        <div class="table-responsive">
            <table class="table table-bordered datatable" data-dt-init="false">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($transaction->company && $transaction->company->logo)
                                        <img src="{{ asset('storage/' . $transaction->company->logo) }}" 
                                             alt="{{ $transaction->company->localized_name }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $transaction->company->localized_name ?? __('N/A') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold {{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->amount >= 0 ? '+' : '' }}${{ number_format(abs($transaction->amount), 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($transaction->type ?? 'transaction') }}</span>
                            </td>
                            <td>
                                @if($transaction->status == 'completed')
                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                @elseif($transaction->status == 'pending')
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @elseif($transaction->status == 'failed')
                                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($transaction->description ?? __('Transaction'), 50) }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-exchange-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">{{ __('No transactions found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

