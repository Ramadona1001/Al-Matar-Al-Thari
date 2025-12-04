@extends('layouts.dashboard')

@section('title', __('Member Interactions'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Users') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('merchant.members.index') }}">{{ __('Members') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Card Interactions') }}</li>
@endsection

@section('content')
<div class="container">
  <h1 class="mb-2">{{ __('Member') }}: {{ $member->name }} <small class="text-muted">&lt;{{ $member->email }}&gt;</small></h1>
  <p class="text-muted mb-4">{{ __('Loyalty Card') }}: {{ $card->title }}</p>

  <div class="d-flex align-items-center mb-3">
    <form method="POST" action="{{ route('merchant.members.cards.revert-last', [$member->id, $card->id]) }}" onsubmit="return confirm('{{ __('Are you sure? This action is irreversible and will delete the latest transaction.') }}')">
      @csrf
      <button class="btn btn-danger">
        <i class="fas fa-undo-alt me-1"></i>{{ __('Delete last transaction') }}
      </button>
    </form>
    @if($last)
      <span class="ms-3 text-muted">{{ __('Latest') }}: {{ $last->type }} · {{ $last->points }} {{ __('points') }} · {{ $last->created_at->diffForHumans() }}</span>
    @endif
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Points') }}</th>
            <th>{{ __('Description') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($interactions as $p)
            <tr>
              <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
              <td>
                @if($p->isEarned())
                  <span class="badge bg-primary">{{ __('earned') }}</span>
                @elseif($p->isRedeemed())
                  <span class="badge bg-success">{{ __('redeemed') }}</span>
                @elseif($p->isExpired())
                  <span class="badge bg-secondary">{{ __('expired') }}</span>
                @else
                  <span class="badge bg-info">{{ $p->type }}</span>
                @endif
              </td>
              <td>{{ $p->points }}</td>
              <td>{{ $p->description }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted">{{ __('No interactions found.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $interactions->links() }}
    </div>
  </div>
</div>
@endsection

