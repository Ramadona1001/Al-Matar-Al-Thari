@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="mb-3">{{ __('Edit Network') }}</h1>
  <form action="{{ route('admin.networks.update', $network) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">{{ __('Name') }}</label>
      <input type="text" name="name" class="form-control" required value="{{ old('name', $network->name) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Description') }}</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description', $network->description) }}</textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Status') }}</label>
      <select name="status" class="form-select">
        <option value="active" {{ old('status', $network->status)==='active'?'selected':'' }}>{{ __('Active') }}</option>
        <option value="inactive" {{ old('status', $network->status)==='inactive'?'selected':'' }}>{{ __('Inactive') }}</option>
      </select>
    </div>
    <button class="btn btn-primary">{{ __('Update') }}</button>
    <a href="{{ route('admin.networks.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection

