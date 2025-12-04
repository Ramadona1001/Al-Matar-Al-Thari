@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Staff Dashboard') }}</h1>
    <a href="{{ route('staff.scan.index') }}" class="btn btn-primary">{{ __('Open Scanner') }}</a>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ __('Assigned Club') }}</h5>
          <p class="card-text">{{ optional($staff->club)->name ?? __('All Clubs') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ __('Company') }}</h5>
          <p class="card-text">{{ optional($staff->company)->name }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ __('Status') }}</h5>
          <p class="card-text">{{ $staff->is_verified ? __('Verified') : __('Not Verified') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

