@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Create Partner') }}</h1>
  <form action="{{ route('manager.partners.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label class="form-label">{{ __('Name') }}</label>
      <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Description') }}</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Website') }}</label>
      <input type="url" name="website" class="form-control" value="{{ old('website') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Assign Merchant User') }}</label>
      <select name="user_id" class="form-select" required>
        @foreach($merchantUsers as $user)
          <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Network') }}</label>
      <select name="network_id" class="form-select" required>
        @foreach($networks as $network)
          <option value="{{ $network->id }}">{{ $network->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Status') }}</label>
      <select name="status" class="form-select">
        <option value="pending">{{ __('Pending') }}</option>
        <option value="approved">{{ __('Approved') }}</option>
        <option value="rejected">{{ __('Rejected') }}</option>
      </select>
    </div>
    <div class="form-check mb-3">
      <input type="checkbox" name="can_display_cards_on_homepage" value="1" class="form-check-input" id="homepageFlag">
      <label class="form-check-label" for="homepageFlag">{{ __('Can display cards on homepage') }}</label>
    </div>
    <button class="btn btn-primary">{{ __('Save Partner') }}</button>
    <a href="{{ route('manager.partners.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection

