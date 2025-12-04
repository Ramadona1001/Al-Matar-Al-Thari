@extends('layouts.dashboard')

@section('title', __('Members'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Users') }}</li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Members') }}</li>
@endsection

@section('content')
<div class="container-fluid">
  <h1 class="mb-3">{{ __('Members Engaged by Staff (Past Year)') }}</h1>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle datatable">
        <thead>
          <tr>
            <th>{{ __('Member') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Loyalty Cards') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($members as $member)
            <tr>
              <td>{{ $member->name }}</td>
              <td>{{ $member->email }}</td>
              <td>
                @php $cardRows = ($cardsPerMember[$member->id] ?? collect()); @endphp
                @forelse($cardRows as $row)
                  @php $card = $cards[$row->loyalty_card_id] ?? null; @endphp
                  @if($card)
                    <a href="{{ route('merchant.members.cards.show', [$member->id, $card->id]) }}" class="badge bg-info text-dark me-1">
                      <i class="fas fa-id-card me-1"></i>{{ $card->title }}
                    </a>
                  @endif
                @empty
                  <span class="text-muted">{{ __('No card interactions') }}</span>
                @endforelse
              </td>
            </tr>
          @empty
            <tr>
              <td class="text-center text-muted">{{ __('No members found for the selected period.') }}</td>
              <td class="text-center text-muted">—</td>
              <td class="text-center text-muted">—</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $members->links() }}
    </div>
  </div>
</div>
@endsection
