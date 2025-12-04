@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Loyalty Cards') }}</h1>
    <a href="{{ route('merchant.loyalty-cards.create') }}" class="btn btn-primary">{{ __('Create Card') }}</a>
  </div>
  <div class="alert alert-info">{{ __('Company') }}: {{ $company->localized_name }}</div>
  <div class="card">
    <div class="card-body">
      <table class="table table-striped datatable">
        <thead>
          <tr>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Homepage') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cards as $card)
          <tr>
            <td>{{ $card->title }}</td>
            <td><span class="badge bg-{{ $card->status === 'published' ? 'success' : ($card->status==='draft'?'secondary':'danger') }}">{{ $card->status }}</span></td>
            <td>
              @if($card->visible_on_homepage)
                <span class="badge bg-success">{{ __('Visible') }}</span>
              @else
                <span class="badge bg-secondary">{{ __('Hidden') }}</span>
              @endif
            </td>
            <td>
              <a href="{{ route('merchant.loyalty-cards.edit', $card) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
              <a href="{{ route('merchant.rewards.index', $card) }}" class="btn btn-sm btn-outline-primary">{{ __('Rewards') }}</a>
              <a href="{{ route('merchant.loyalty-cards.members.index', $card) }}" class="btn btn-sm btn-outline-info">{{ __('Members') }}</a>
              <form action="{{ route('merchant.loyalty-cards.destroy', $card) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this card?') }}')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {{ $cards->links() }}
    </div>
  </div>
</div>
@endsection
