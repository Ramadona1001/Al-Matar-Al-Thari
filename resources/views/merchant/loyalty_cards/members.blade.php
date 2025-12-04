@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Members for') }}: {{ $card->title }}</h1>
    <a href="{{ route('merchant.loyalty-cards.index') }}" class="btn btn-link">{{ __('Back to Cards') }}</a>
  </div>
  <div class="alert alert-info">{{ __('Company') }}: {{ $company->localized_name }}</div>

  <div class="card">
    <div class="card-body">
      <table class="table table-striped datatable">
        <thead>
          <tr>
            <th>{{ __('Member') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Points Earned') }}</th>
            <th>{{ __('Points Redeemed') }}</th>
            <th>{{ __('Active Points') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($members as $m)
            <tr>
              <td>{{ optional($m->user)->name ?? ('#'.$m->user_id) }}</td>
              <td>{{ optional($m->user)->email }}</td>
              <td>{{ number_format($m->earned_points ?? 0) }}</td>
              <td>{{ number_format($m->redeemed_points ?? 0) }}</td>
              <td>{{ number_format(($m->earned_points ?? 0) - ($m->redeemed_points ?? 0)) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">{{ __('No members yet for this card.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $members->links() }}
    </div>
  </div>
</div>
@endsection
