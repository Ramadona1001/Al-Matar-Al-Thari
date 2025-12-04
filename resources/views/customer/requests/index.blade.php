@extends('layouts.dashboard')

@section('title', __('Request Points Links'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">{{ __('Request Points Links') }}</h1>
        <form method="post" action="{{ route('customer.requests.store', app()->getLocale()) }}">
            @csrf
            <div class="input-group">
                <select name="card_id" class="form-select">
                    <option value="">{{ __('Any Card') }}</option>
                    @foreach(\App\Models\LoyaltyCard::orderBy('title')->get() as $card)
                        <option value="{{ $card->id }}">{{ $card->title }}</option>
                    @endforeach
                </select>
                <input type="number" name="expires_days" class="form-control" min="1" max="30" placeholder="{{ __('Expires in days') }}" />
                <button class="btn btn-primary" type="submit" {{ $activeCount >= $maxActive ? 'disabled' : '' }}>
                    {{ __('Generate Link') }}
                </button>
            </div>
        </form>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('limit')
        <div class="alert alert-warning">{{ $message }}</div>
    @enderror

    <div class="card">
        <div class="card-body">
            <div class="mb-2 text-muted">{{ __('Active') }}: {{ $activeCount }} / {{ $maxActive }}</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Link') }}</th>
                            <th>{{ __('Card') }}</th>
                            <th>{{ __('Expires At') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($links as $link)
                            <tr>
                                <td>
                                    <a href="{{ route('public.requests.show', [app()->getLocale(), $link->uuid]) }}" target="_blank">
                                        {{ route('public.requests.show', [app()->getLocale(), $link->uuid]) }}
                                    </a>
                                </td>
                                <td>{{ optional($link->loyaltyCard)->title ?? __('Any') }}</td>
                                <td>{{ $link->expires_at ? $link->expires_at->format('Y-m-d H:i') : __('No expiry') }}</td>
                                <td>
                                    @if($link->isActive())
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">{{ __('No request links yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $links->links() }}</div>
        </div>
    </div>
    <p class="mt-3 text-muted">{{ __('Share these links with friends to receive points. They must be signed in to send points.') }}</p>
    <p class="text-muted">{{ __('Transferred points keep their original expiry and follow FIFO transfer logic.') }}</p>
</div>
@endsection
