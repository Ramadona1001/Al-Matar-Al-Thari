@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Staff Scanner') }}</h1>
  <div class="alert alert-info">{{ __('Club') }}: {{ optional($staff->club)->name ?? __('All Clubs') }}</div>

  <div class="row">
    <div class="col-lg-6">
      <div class="card mb-4">
        <div class="card-header">{{ __('Award Points') }}</div>
        <div class="card-body">
          <form method="POST" action="{{ route('staff.scan.award') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">{{ __('Member ID') }}</label>
              <input type="number" name="member_id" class="form-control" placeholder="{{ __('Enter member user ID') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Loyalty Card') }}</label>
              <select name="card_id" class="form-select" required>
                @foreach($cards as $card)
                  <option value="{{ $card->id }}">{{ $card->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Transaction Amount') }}</label>
              <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <button class="btn btn-primary">{{ __('Award Points') }}</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card mb-4">
        <div class="card-header">{{ __('Validate Reward') }}</div>
        <div class="card-body">
          <form method="POST" action="{{ route('staff.scan.validate') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">{{ __('Member ID') }}</label>
              <input type="number" name="member_id" class="form-control" placeholder="{{ __('Enter member user ID') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Loyalty Card') }}</label>
              <select name="card_id" class="form-select" required>
                @foreach($cards as $card)
                  <option value="{{ $card->id }}">{{ $card->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Reward ID') }}</label>
              <input type="number" name="reward_id" class="form-control" placeholder="{{ __('Enter reward ID') }}" required>
            </div>
            <button class="btn btn-success">{{ __('Validate Reward') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

