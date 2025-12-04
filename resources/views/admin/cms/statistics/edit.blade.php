@extends('layouts.dashboard')

@section('title', __('Edit Statistic'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.statistics.index') }}">{{ __('Statistics') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-chart-line text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Statistic') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.statistics.update', $statistic) }}">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="value" class="form-label fw-semibold">{{ __('Value') }} <span class="text-danger">*</span></label>
                    <input type="text" name="value" id="value" value="{{ old('value', $statistic->value) }}" class="form-control @error('value') is-invalid @enderror" required>
                    @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="suffix" class="form-label fw-semibold">{{ __('Suffix') }}</label>
                    <input type="text" name="suffix" id="suffix" value="{{ old('suffix', $statistic->suffix) }}" class="form-control" placeholder="e.g., +, %, K, M">
                </div>
                <div class="col-md-6">
                    <label for="icon" class="form-label fw-semibold">{{ __('Icon') }}</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $statistic->icon) }}" class="form-control" placeholder="fas fa-users">
                    <small class="text-muted">{{ __('Font Awesome icon class') }}</small>
                </div>
                <div class="col-md-6">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $statistic->order) }}" class="form-control" min="0">
                </div>
                <div class="col-12">
                    <div class="card border mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">{{ __('Translations') }}</h6>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                @foreach($locales as $index => $locale)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="statistic-tab-{{ $locale }}" data-bs-toggle="tab" data-bs-target="#statistic-{{ $locale }}" type="button" role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($locales as $index => $locale)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="statistic-{{ $locale }}" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="label_{{ $locale }}" class="form-label fw-semibold">{{ __('Label') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <input type="text" name="label_{{ $locale }}" id="label_{{ $locale }}" value="{{ old("label_{$locale}", $statistic->translate($locale)->label ?? '') }}" class="form-control @error("label_{$locale}") is-invalid @enderror" required>
                                                @error("label_{$locale}")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="description_{{ $locale }}" class="form-label fw-semibold">{{ __('Description') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="description_{{ $locale }}" id="description_{{ $locale }}" rows="3" class="form-control">{{ old("description_{$locale}", $statistic->translate($locale)->description ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $statistic->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.statistics.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Statistic') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

