@extends('layouts.dashboard')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Partners') }}</h1>
    <a href="{{ route('manager.partners.create') }}" class="btn btn-primary">{{ __('Create Partner') }}</a>
  </div>
  <div class="card">
    <div class="card-body">
<table class="table table-striped datatable">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Network') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Homepage Display') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($companies as $company)
          <tr>
            <td>{{ $company->localized_name }}</td>
            <td>{{ optional($company->network)->name }}</td>
            <td><span class="badge bg-{{ $company->status === 'approved' ? 'success' : ($company->status==='pending'?'warning':'danger') }}">{{ $company->status }}</span></td>
            <td>
              @if($company->can_display_cards_on_homepage)
                <span class="badge bg-success">{{ __('Allowed') }}</span>
              @else
                <span class="badge bg-secondary">{{ __('Disallowed') }}</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {{ $companies->links() }}
    </div>
  </div>
</div>
@endsection
