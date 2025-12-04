@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Rewards for') }}: {{ $card->title }}</h1>
    <a href="{{ route('merchant.rewards.create', $card) }}" class="btn btn-primary">{{ __('Create Reward') }}</a>
  </div>
  <div class="alert alert-info">{{ __('Company') }}: {{ $company->localized_name }}</div>
  <div class="card">
    <div class="card-body">
      <table class="table table-striped datatable">
        <thead>
          <tr>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Points Required') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Stock') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rewards as $reward)
            <tr>
              <td>{{ $reward->title }}</td>
              <td>{{ number_format($reward->points_required) }}</td>
              <td><span class="badge bg-{{ $reward->status === 'active' ? 'success' : 'secondary' }}">{{ $reward->status }}</span></td>
              <td>{{ $reward->stock ?? '—' }}</td>
              <td>
                <a href="{{ route('merchant.rewards.edit', [$card, $reward]) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                <form action="{{ route('merchant.rewards.destroy', [$card, $reward]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this reward?') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">{{ __('No rewards found.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $rewards->links() }}
    </div>
  </div>
</div>
@endsection
