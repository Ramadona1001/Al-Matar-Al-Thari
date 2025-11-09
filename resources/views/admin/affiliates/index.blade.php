@extends('layouts.dashboard')

@section('title', __('Affiliate Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Affiliates') }}</li>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Affiliate Accounts') }}</h6>
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
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Affiliate') }}</th>
                        <th>{{ __('Company') }}</th>
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
                            <td>{{ $affiliate->user->full_name ?? $affiliate->user->email }}</td>
                            <td>{{ $affiliate->company->name ?? '-' }}</td>
                            <td>{{ number_format($affiliate->commission_rate, 2) }}%</td>
                            <td>{{ $affiliate->total_referrals }}</td>
                            <td>{{ number_format($affiliate->total_earned, 2) }}</td>
                            <td><span class="badge bg-info">{{ $affiliate->status }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('admin.affiliates.update-status', $affiliate) }}" class="d-flex gap-2">
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
                            <td colspan="7" class="text-center">{{ __('No affiliates found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $affiliates->links() }}
        </div>
    </div>
</div>
@endsection
