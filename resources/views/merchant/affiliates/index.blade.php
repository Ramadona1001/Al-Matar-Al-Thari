@extends('layouts.dashboard')

@section('title', __('Affiliate Program'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Affiliates') }}</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Commission Settings') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('merchant.affiliates.settings') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="affiliate_commission_rate" class="form-label">{{ __('Default Commission Rate (%)') }}</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control @error('affiliate_commission_rate') is-invalid @enderror" id="affiliate_commission_rate" name="affiliate_commission_rate" value="{{ old('affiliate_commission_rate', $company->affiliate_commission_rate) }}" required>
                        @error('affiliate_commission_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>{{ __('Save Settings') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Affiliates') }}</h6>
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach(['pending', 'approved', 'suspended', 'rejected'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Affiliate') }}</th>
                                <th>{{ __('Commission Rate') }}</th>
                                <th>{{ __('Referrals') }}</th>
                                <th>{{ __('Total Earned') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($affiliates as $affiliate)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $affiliate->user->full_name ?? $affiliate->user->email }}</div>
                                        <div class="small text-muted">{{ __('Code') }}: {{ $affiliate->referral_code }}</div>
                                    </td>
                                    <td>{{ number_format($affiliate->commission_rate, 2) }}%</td>
                                    <td>{{ $affiliate->total_referrals }}</td>
                                    <td>{{ number_format($affiliate->total_earned, 2) }}</td>
                                    <td><span class="badge bg-info text-uppercase">{{ $affiliate->status }}</span></td>
                                    <td>
                                        <form method="POST" action="{{ route('merchant.affiliates.update-status', $affiliate) }}" class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm">
                                                @foreach(['approved', 'suspended', 'rejected'] as $status)
                                                    <option value="{{ $status }}" {{ $affiliate->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Update') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('No affiliates yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $affiliates->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
