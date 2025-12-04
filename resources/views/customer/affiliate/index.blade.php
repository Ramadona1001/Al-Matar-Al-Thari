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
                            <input type="text" id="affiliate-referral-link" class="form-control" value="{{ $affiliate->referral_link }}" readonly>
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
                @else
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div>
                            <h4 class="fw-semibold mb-1">{{ __('Join the Affiliate Program') }}</h4>
                            <p class="text-muted mb-0">{{ __('Choose a company, share offers, and earn instant commissions.') }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('customer.affiliate.store') }}" class="d-flex flex-column gap-3">
                        @csrf
                        <div>
                            <label for="company_id" class="form-label fw-semibold small text-uppercase text-muted">{{ __('Company') }}</label>
                            <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                <option value="">{{ __('Select company') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->localized_name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="offer_id" class="form-label fw-semibold small text-uppercase text-muted">{{ __('Offer (optional)') }}</label>
                            <select class="form-select @error('offer_id') is-invalid @enderror" id="offer_id" name="offer_id">
                                <option value="">{{ __('Select offer') }}</option>
                                @foreach($offers as $offer)
                                    <option value="{{ $offer->id }}" {{ old('offer_id') == $offer->id ? 'selected' : '' }}>
                                        {{ $offer->title[$currentLocale] ?? $offer->title['en'] ?? $offer->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('offer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-animated w-100">
                            <i class="fas fa-user-plus me-2"></i>{{ __('Apply Now') }}
                        </button>
                    </form>
                    <div class="mt-4">
                        <p class="fw-semibold text-muted small text-uppercase mb-2">{{ __('How it works') }}</p>
                        <ol class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                            <li class="d-flex gap-2"><span class="badge rounded-pill bg-primary-subtle text-primary">1</span><span>{{ __('Pick a company and optional offer to promote.') }}</span></li>
                            <li class="d-flex gap-2"><span class="badge rounded-pill bg-primary-subtle text-primary">2</span><span>{{ __('Share your link across your favourite channels.') }}</span></li>
                            <li class="d-flex gap-2"><span class="badge rounded-pill bg-primary-subtle text-primary">3</span><span>{{ __('Earn loyalty points and cash commissions on every successful referral.') }}</span></li>
                        </ol>
                    </div>
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
                                    <th scope="col">{{ __('Offer/Company') }}</th>
                                    <th scope="col" class="text-end">{{ __('Sale Amount') }}</th>
                                    <th scope="col" class="text-end">{{ __('Commission') }}</th>
                                    <th scope="col" class="text-center">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td class="fw-semibold">{{ $sale->created_at->translatedFormat('d M Y') }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $sale->offer->title[$currentLocale] ?? $sale->offer->title['en'] ?? $sale->company->localized_name ?? '-' }}</div>
                                            <div class="small text-muted">#{{ $sale->id }}</div>
                                        </td>
                                        <td class="text-end fw-semibold text-primary">{{ number_format($sale->sale_amount, 2) }}</td>
                                        <td class="text-end fw-semibold text-success">{{ number_format($sale->commission_amount, 2) }}</td>
                                        <td class="text-center">
                                            @php($statusClass = match($sale->status) {
                                                'pending' => 'bg-warning-subtle text-warning',
                                                'approved' => 'bg-success-subtle text-success',
                                                'rejected' => 'bg-danger-subtle text-danger',
                                                default => 'bg-info-subtle text-info'
                                            })
                                            <span class="badge rounded-pill {{ $statusClass }} px-3 py-2 text-uppercase small">{{ $sale->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-chart-line fa-2x mb-3 text-primary"></i>
                                                <p class="mb-0">{{ __('No sales yet.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
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
