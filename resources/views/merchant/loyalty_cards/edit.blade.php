@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Edit Loyalty Card') }}</h1>
  <form action="{{ route('merchant.loyalty-cards.update', $loyaltyCard) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">{{ __('Title') }}</label>
      <input type="text" name="title" class="form-control" required value="{{ old('title', $loyaltyCard->title) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Slug') }}</label>
      <input type="text" name="slug" class="form-control" required value="{{ old('slug', $loyaltyCard->slug) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Description') }}</label>
      <textarea name="description" class="form-control" rows="4">{{ old('description', $loyaltyCard->description) }}</textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Club') }}</label>
      <select name="club_id" class="form-select">
        <option value="">{{ __('Select a club (optional)') }}</option>
        @foreach($clubs as $club)
          <option value="{{ $club->id }}" {{ old('club_id', $loyaltyCard->club_id)==$club->id?'selected':'' }}>{{ $club->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Card Image') }}</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">{{ __('Points') }}</label>
          <input type="number" name="points" class="form-control" min="0" value="{{ old('points', $loyaltyCard->points ?? 0) }}">
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">{{ __('Balance') }}</label>
          <input type="number" name="balance" step="0.01" min="0" class="form-control" value="{{ old('balance', $loyaltyCard->balance ?? 0) }}">
        </div>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Status') }}</label>
      <select name="status" class="form-select">
        <option value="draft" {{ old('status', $loyaltyCard->status)==='draft'?'selected':'' }}>{{ __('Draft') }}</option>
        <option value="published" {{ old('status', $loyaltyCard->status)==='published'?'selected':'' }}>{{ __('Published') }}</option>
        <option value="archived" {{ old('status', $loyaltyCard->status)==='archived'?'selected':'' }}>{{ __('Archived') }}</option>
      </select>
    </div>
    <div class="form-check mb-3">
      <input type="checkbox" name="visible_on_homepage" value="1" class="form-check-input" id="visibleHomepage" {{ old('visible_on_homepage', $loyaltyCard->visible_on_homepage) ? 'checked' : '' }}>
      <label class="form-check-label" for="visibleHomepage">{{ __('Visible on homepage') }}</label>
    </div>
    <button class="btn btn-primary">{{ __('Update Card') }}</button>
    <a href="{{ route('merchant.loyalty-cards.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection
