@extends('layouts.dashboard')

@section('title', __('Points Settings'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Points') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Settings') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.points.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="earn_rate" class="form-label">{{ __('Currency per Point (earning)') }}</label>
                        <input type="number" step="0.01" min="0.01" class="form-control @error('earn_rate') is-invalid @enderror" id="earn_rate" name="earn_rate" value="{{ old('earn_rate', $settings->earn_rate ?? 10) }}" required>
                        @error('earn_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="redeem_rate" class="form-label">{{ __('Currency per Point (redeeming)') }}</label>
                        <input type="number" step="0.01" min="0.0001" class="form-control @error('redeem_rate') is-invalid @enderror" id="redeem_rate" name="redeem_rate" value="{{ old('redeem_rate', $settings->redeem_rate ?? 0.1) }}" required>
                        @error('redeem_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="referral_bonus_points" class="form-label">{{ __('Referral Bonus Points') }}</label>
                        <input type="number" min="0" class="form-control @error('referral_bonus_points') is-invalid @enderror" id="referral_bonus_points" name="referral_bonus_points" value="{{ old('referral_bonus_points', $settings->referral_bonus_points ?? 50) }}" required>
                        @error('referral_bonus_points')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="auto_approve_redemptions" name="auto_approve_redemptions" value="1" {{ old('auto_approve_redemptions', $settings->auto_approve_redemptions ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_approve_redemptions">
                            {{ __('Auto approve redemption requests') }}
                        </label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('Save Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Redemption Requests') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Points') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Requested At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingRedemptions as $redemption)
                                <tr>
                                    <td>{{ $redemption->user->full_name ?? $redemption->user->email }}</td>
                                    <td>{{ number_format($redemption->points) }}</td>
                                    <td>{{ number_format($redemption->amount, 2) }}</td>
                                    <td><span class="badge bg-info text-uppercase">{{ $redemption->status }}</span></td>
                                    <td>{{ $redemption->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.points.redemptions.update', $redemption) }}" class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="pending" {{ $redemption->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                <option value="approved" {{ $redemption->status === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                                <option value="completed" {{ $redemption->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                                <option value="rejected" {{ $redemption->status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Update') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('No redemption requests found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $pendingRedemptions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
