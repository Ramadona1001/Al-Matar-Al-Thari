@extends('layouts.dashboard')

@section('title', __('Affiliate Program'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Affiliate') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-body">
                @if($affiliate)
                    <h5 class="text-success">{{ __('You are an affiliate!') }}</h5>
                    <p class="mb-2">{{ __('Referral Code') }}: <strong>{{ $affiliate->referral_code }}</strong></p>
                    <p class="mb-2">{{ __('Referral Link') }}:</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="{{ $affiliate->referral_link }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ $affiliate->referral_link }}')">{{ __('Copy') }}</button>
                    </div>
                    <p>{{ __('Status') }}: <span class="badge bg-info text-uppercase">{{ $affiliate->status }}</span></p>
                @else
                    <h5>{{ __('Join the Affiliate Program') }}</h5>
                    <p class="text-muted">{{ __('Earn commissions when friends use your referral links.') }}</p>
                    <form method="POST" action="{{ route('customer.affiliate.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="company_id" class="form-label">{{ __('Company') }}</label>
                            <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                <option value="">{{ __('Select company') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="offer_id" class="form-label">{{ __('Offer (optional)') }}</label>
                            <select class="form-select @error('offer_id') is-invalid @enderror" id="offer_id" name="offer_id">
                                <option value="">{{ __('Select offer') }}</option>
                                @foreach($offers as $offer)
                                    <option value="{{ $offer->id }}" {{ old('offer_id') == $offer->id ? 'selected' : '' }}>
                                        {{ $offer->title['en'] ?? $offer->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('offer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Apply') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Affiliate Sales') }}</h6>
            </div>
            <div class="card-body">
                @if($affiliate && $sales instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Offer/Company') }}</th>
                                    <th>{{ __('Sale Amount') }}</th>
                                    <th>{{ __('Commission') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $sale->offer->title['en'] ?? $sale->company->name ?? '-' }}</td>
                                        <td>{{ number_format($sale->sale_amount, 2) }}</td>
                                        <td>{{ number_format($sale->commission_amount, 2) }}</td>
                                        <td><span class="badge bg-info text-uppercase">{{ $sale->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('No sales yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $sales->links() }}
                    </div>
                @else
                    <p class="text-muted">{{ __('Sales will appear here once you are approved as an affiliate.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
