@extends('layouts.dashboard')

@section('title', __('Affiliate Program'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Affiliate') }}</li>
@endsection

@section('content')
@php($currentLocale = app()->getLocale())
@php($pagedSales = $affiliate && $sales instanceof \Illuminate\Contracts\Pagination\Paginator ? collect($sales->items()) : collect())
@php($pageTotals = [
    'count' => $pagedSales->count(),
    'amount' => $pagedSales->sum('sale_amount'),
    'commission' => $pagedSales->sum('commission_amount'),
])

<section class="section-intro fade-in-up mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <div>
                <span class="badge rounded-pill bg-light text-dark mb-3">{{ __('Affiliate Hub') }}</span>
                <h2 class="h1 text-white mb-3">{{ __('Boost your rewards by sharing offers you love') }}</h2>
                <p class="text-white-50 mb-0">{{ __('Track applications, monitor sales, and copy your referral link in one place. Your dashboard updates instantly as commissions roll in.') }}</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center text-white">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="icon-circle bg-white bg-opacity-25 text-white">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                    <div>
                        <p class="small text-uppercase mb-1 text-white-50">{{ __('This page') }}</p>
                        <h4 class="fw-semibold mb-0">{{ trans_choice('{0}No sales yet|{1}1 sale listed|[2,*]:count sales listed', $pageTotals['count'], ['count' => $pageTotals['count']]) }}</h4>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2 small text-white-50">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Sales (page)') }}</span>
                        <span class="fw-semibold">{{ number_format($pageTotals['amount'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Commission (page)') }}</span>
                        <span class="fw-semibold">{{ number_format($pageTotals['commission'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card card-hover border-0 h-100 fade-in-up delay-1">
            <div class="card-body p-4">
                @if($affiliate)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill text-uppercase small">{{ __('Active Affiliate') }}</span>
                            <h5 class="fw-semibold mt-3 mb-2">{{ __('Welcome back!') }}</h5>
                        </div>
                        <div class="icon-circle bg-primary-subtle text-primary">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small text-uppercase">{{ __('Referral Code') }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="h5 mb-0 fw-bold text-primary">{{ $affiliate->referral_code }}</span>
                            <span class="badge rounded-pill bg-info-subtle text-info text-uppercase">{{ $affiliate->status }}</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="affiliate-referral-link" class="fw-semibold text-muted small text-uppercase">{{ __('Referral Link') }}</label>
                        <div class="input-group shadow-sm">
                            <input type="text" id="affiliate-referral-link" class="form-control" value="{{ url('/register?ref=' . $affiliate->referral_code) }}" readonly>
                            <button class="btn btn-outline-primary btn-animated" type="button" data-copy="#affiliate-referral-link">
                                <i class="fas fa-copy me-2"></i>{{ __('Copy') }}
                            </button>
                        </div>
                    </div>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-circle text-success" style="font-size: 0.5rem"></i>
                            {{ __('Share your link to track conversions in real-time.') }}
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-circle text-success" style="font-size: 0.5rem"></i>
                            {{ __('Withdraw commissions once they are approved.') }}
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fas fa-circle text-success" style="font-size: 0.5rem"></i>
                            {{ __('Need help? Contact support from your profile menu.') }}
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card card-hover border-0 h-100 fade-in-up delay-2">
            <div class="card-header bg-transparent border-0 px-4 pt-4 pb-0">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Performance Overview') }}</p>
                        <h4 class="fw-bold mb-0">{{ __('Affiliate Sales') }}</h4>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">{{ __('Page results') }}: {{ $pageTotals['count'] }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if($affiliate && $sales instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="table-responsive shadow-sm rounded overflow-hidden">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th scope="col">{{ __('Date') }}</th>
                                    <th scope="col">{{ __('Product/Company') }}</th>
                                    <th scope="col" class="text-end">{{ __('Sale Amount') }}</th>
                                    <th scope="col" class="text-end">{{ __('Commission') }}</th>
                                    <th scope="col" class="text-center">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between flex-column flex-md-row align-items-center gap-3 mt-4">
                        <div class="d-flex gap-3 text-muted small">
                            <span><strong>{{ __('Current page') }}:</strong> {{ $sales->currentPage() }}</span>
                            <span><strong>{{ __('Total results') }}:</strong> {{ $sales->total() }}</span>
                        </div>
                        <div class="pagination-wrapper">
                            {{ $sales->links() }}
                        </div>
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <div class="icon-circle bg-primary-subtle text-primary mb-3">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">{{ __('Get started to see your stats here') }}</h5>
                        <p class="text-muted mb-0">{{ __('Once your application is approved, every referral and commission will be listed with real-time updates.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
