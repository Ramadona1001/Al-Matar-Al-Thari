@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Networks') }}</h1>
    <a href="{{ route('admin.networks.create') }}" class="btn btn-primary">{{ __('Create Network') }}</a>
  </div>
  <div class="card">
    <div class="card-body">
      <table class="table table-striped datatable">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($networks as $network)
          <tr>
            <td>{{ $network->name }}</td>
            <td><span class="badge bg-{{ $network->status === 'active' ? 'success' : 'secondary' }}">{{ $network->status }}</span></td>
            <td>
              <a href="{{ route('admin.networks.edit', $network) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
              <a href="{{ route('admin.networks.managers.edit', $network) }}" class="btn btn-sm btn-outline-primary">{{ __('Assign Managers') }}</a>
              <form action="{{ route('admin.networks.destroy', $network) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this network?') }}')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {{ $networks->links() }}
    </div>
  </div>
 </div>
@endsection
