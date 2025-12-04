@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Clubs') }}</h1>
    <a href="{{ route('merchant.clubs.create') }}" class="btn btn-primary">{{ __('Create Club') }}</a>
  </div>
  <div class="alert alert-info">{{ __('Company') }}: {{ $company->localized_name }}</div>
  <div class="card">
    <div class="card-body">
    <table class="table table-striped datatable">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Slug') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clubs as $club)
            <tr>
              <td>{{ $club->name }}</td>
              <td>{{ $club->slug }}</td>
              <td><span class="badge bg-{{ $club->status === 'active' ? 'success' : 'secondary' }}">{{ $club->status }}</span></td>
              <td>
                <a href="{{ route('merchant.clubs.edit', $club) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                <form action="{{ route('merchant.clubs.destroy', $club) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this club?') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted">{{ __('No clubs yet.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-3">
        {{ $clubs->links() }}
      </div>
    </div>
  </div>
 </div>
@endsection
