@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Edit Club') }}</h1>
  <form action="{{ route('merchant.clubs.update', $club) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">{{ __('Name') }}</label>
      <input type="text" name="name" class="form-control" required value="{{ old('name', $club->name) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Slug') }}</label>
      <input type="text" name="slug" class="form-control" value="{{ old('slug', $club->slug) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Description') }}</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description', $club->description) }}</textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Status') }}</label>
      <select name="status" class="form-select">
        <option value="active" {{ old('status', $club->status)==='active'?'selected':'' }}>{{ __('Active') }}</option>
        <option value="inactive" {{ old('status', $club->status)==='inactive'?'selected':'' }}>{{ __('Inactive') }}</option>
      </select>
    </div>
    <button class="btn btn-primary">{{ __('Update Club') }}</button>
    <a href="{{ route('merchant.clubs.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection

