@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Assign Managers') }} — {{ $network->name }}</h1>
    <a href="{{ route('admin.networks.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.networks.managers.update', $network) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">{{ __('Select Managers') }}</label>
          <div class="row">
            @foreach($managers as $manager)
              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="manager_ids[]" id="mgr{{ $manager->id }}" value="{{ $manager->id }}" {{ in_array($manager->id, $assigned) ? 'checked' : '' }}>
                  <label class="form-check-label" for="mgr{{ $manager->id }}">
                    {{ $manager->name }} — {{ $manager->email }}
                  </label>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <button class="btn btn-primary">{{ __('Save Changes') }}</button>
      </form>
    </div>
  </div>
 </div>
@endsection

