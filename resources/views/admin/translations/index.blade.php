@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">{{ __('Translations') }}</h1>
        @if(session('status'))
            <div class="alert alert-success py-1 px-2 mb-0">{{ session('status') }}</div>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-header">{{ __('Add Translation') }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.translations.store') }}" class="row g-3">
                @csrf
                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('Key') }}</label>
                    <input type="text" name="key" class="form-control" required>
                </div>
                @foreach($locales as $locale)
                    <div class="col-12 col-md-6">
                        <label class="form-label">{{ strtoupper($locale) }}</label>
                        <input type="text" name="values[{{ $locale }}]" class="form-control">
                    </div>
                @endforeach
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{ __('Manage Translations') }}</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 20%">{{ __('Key') }}</th>
                            @foreach($locales as $locale)
                                <th>{{ strtoupper($locale) }}</th>
                            @endforeach
                            <th style="width: 12%">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keys as $key)
                            <tr>
                                <form method="POST" action="{{ route('admin.translations.update') }}">
                                    @csrf
                                    <td>
                                        <input type="hidden" name="key" value="{{ $key }}">
                                        <input type="text" name="new_key" value="{{ $key }}" class="form-control">
                                    </td>
                                    @foreach($locales as $locale)
                                        <td>
                                            <input type="text" name="values[{{ $locale }}]" value="{{ $translations[$locale][$key] ?? '' }}" class="form-control">
                                        </td>
                                    @endforeach
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-sm btn-success">{{ __('Save') }}</button>
                                            <form method="POST" action="{{ route('admin.translations.destroy') }}">
                                                @csrf
                                                <input type="hidden" name="key" value="{{ $key }}">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Delete this translation?') }}')">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + count($locales) }}" class="text-center text-muted">{{ __('No translations found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection