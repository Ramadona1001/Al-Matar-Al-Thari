@extends('layouts.dashboard')

@section('title', __('My Loyalty Points'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Loyalty Points') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">{{ __('Available Points') }}</h4>
                <p class="display-5 text-primary">{{ number_format($availablePoints) }}</p>
                <p class="text-muted">{{ __('Each point is worth :value in currency.', ['value' => number_format($redeemRate, 2)]) }}</p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Redeem Points') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.loyalty.redeem') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="points" class="form-label">{{ __('Points to Redeem') }}</label>
                        <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points') }}" min="1" max="{{ $availablePoints }}" required>
                        @error('points')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('Notes (optional)') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="{{ __('How would you like to use the points?') }}">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-gift me-2"></i>{{ __('Request Redemption') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Points History') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Points') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($points as $entry)
                                <tr>
                                    <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $entry->description ?? '-' }}</td>
                                    <td>
                                        @if($entry->transaction_type === 'earned')
                                            <span class="badge bg-success">{{ __('Earned') }}</span>
                                        @elseif($entry->transaction_type === 'redeemed')
                                            <span class="badge bg-danger">{{ __('Redeemed') }}</span>
                                        @else
                                            {{ ucfirst($entry->transaction_type ?? $entry->type) }}
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $entry->points > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $entry->points > 0 ? '+' : '' }}{{ number_format($entry->points) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No points history yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $points->links() }}
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Redemption Requests') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Points') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($redemptions as $redemption)
                                <tr>
                                    <td>{{ $redemption->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ number_format($redemption->points) }}</td>
                                    <td>{{ number_format($redemption->amount, 2) }}</td>
                                    <td><span class="badge bg-info text-uppercase">{{ $redemption->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No redemption requests yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $redemptions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
