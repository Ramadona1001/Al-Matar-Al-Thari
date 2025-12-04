@extends('layouts.dashboard')

@section('title', __('Edit Section Setting'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.section-settings.index') }}">{{ __('Section Settings') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-cog text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Section Setting') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.section-settings.update', $sectionSetting) }}">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="section_key" class="form-label fw-semibold">{{ __('Section Key') }} <span class="text-danger">*</span></label>
                    <select name="section_key" id="section_key" class="form-select @error('section_key') is-invalid @enderror" required>
                        <option value="">{{ __('Select Section Key') }}</option>
                        @foreach($sectionKeys as $key)
                            <option value="{{ $key }}" {{ old('section_key', $sectionSetting->section_key) == $key ? 'selected' : '' }}>{{ $key }}</option>
                        @endforeach
                    </select>
                    @error('section_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $sectionSetting->order) }}" class="form-control" min="0">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $sectionSetting->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
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
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="setting-tab-{{ $locale }}" data-bs-toggle="tab" data-bs-target="#setting-{{ $locale }}" type="button" role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($locales as $index => $locale)
                                    @php
                                        $translation = $sectionSetting->translate($locale);
                                    @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="setting-{{ $locale }}" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="title_{{ $locale }}" class="form-label fw-semibold">{{ __('Title') }} ({{ strtoupper($locale) }})</label>
                                                <input type="text" name="title[{{ $locale }}]" id="title_{{ $locale }}" value="{{ old("title.$locale", $translation->title ?? '') }}" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <label for="subtitle_{{ $locale }}" class="form-label fw-semibold">{{ __('Subtitle') }} ({{ strtoupper($locale) }})</label>
                                                <textarea name="subtitle[{{ $locale }}]" id="subtitle_{{ $locale }}" rows="3" class="form-control">{{ old("subtitle.$locale", $translation->subtitle ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <label for="options" class="form-label fw-semibold">{{ __('Options (JSON)') }}</label>
                    <textarea name="options" id="options" rows="5" class="form-control" placeholder='{"key": "value"}'>{{ old('options') ? json_encode(old('options'), JSON_PRETTY_PRINT) : ($sectionSetting->options ? json_encode($sectionSetting->options, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <small class="text-muted">{{ __('Optional JSON data for additional settings') }}</small>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.section-settings.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Section Setting') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

