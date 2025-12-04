@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Create Reward') }} — {{ $card->title }}</h1>
  <form action="{{ route('merchant.rewards.store', $card) }}" method="POST">
    @csrf
    <div class="mb-3">
      <label class="form-label">{{ __('Title') }}</label>
      <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Description') }}</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="mb-3">
          <label class="form-label">{{ __('Points Required') }}</label>
          <input type="number" name="points_required" class="form-control" min="1" required value="{{ old('points_required') }}">
        </div>
      </div>
      <div class="col-md-4">
        <div class="mb-3">
          <label class="form-label">{{ __('Status') }}</label>
          <select name="status" class="form-select">
            <option value="active" {{ old('status')==='active'?'selected':'' }}>{{ __('Active') }}</option>
            <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>{{ __('Inactive') }}</option>
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mb-3">
          <label class="form-label">{{ __('Stock') }}</label>
          <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock') }}">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">{{ __('Starts At') }}</label>
          <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">{{ __('Ends At') }}</label>
          <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
        </div>
      </div>
    </div>
    <button class="btn btn-primary">{{ __('Save Reward') }}</button>
    <a href="{{ route('merchant.rewards.index', $card) }}" class="btn btn-link">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection

