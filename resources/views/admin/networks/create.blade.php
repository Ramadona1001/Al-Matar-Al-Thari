@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Create Network') }}</h1>
  <form action="{{ route('admin.networks.store') }}" method="POST">
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
      <label class="form-label">{{ __('Status') }}</label>
      <select name="status" class="form-select">
        <option value="active" {{ old('status')==='active'?'selected':'' }}>{{ __('Active') }}</option>
        <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>{{ __('Inactive') }}</option>
      </select>
    </div>
    <button class="btn btn-primary">{{ __('Save') }}</button>
    <a href="{{ route('admin.networks.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection

