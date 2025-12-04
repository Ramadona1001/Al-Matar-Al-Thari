@extends('layouts.dashboard')

@section('title', __('Edit How It Works Step'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.how-it-works-steps.index') }}">{{ __('How It Works Steps') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-list-ol text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit How It Works Step') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.how-it-works-steps.update', $howItWorksStep) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-4">
                    <label for="step_number" class="form-label fw-semibold">{{ __('Step Number') }} <span class="text-danger">*</span></label>
                    <input type="number" name="step_number" id="step_number" value="{{ old('step_number', $howItWorksStep->step_number) }}" class="form-control @error('step_number') is-invalid @enderror" min="1" required>
                    @error('step_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="icon" class="form-label fw-semibold">{{ __('Icon Class') }}</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $howItWorksStep->icon) }}" class="form-control" placeholder="fas fa-check">
                    <small class="text-muted">{{ __('Font Awesome icon class') }}</small>
                </div>
                <div class="col-md-4">
                    <label for="image_path" class="form-label fw-semibold">{{ __('Image') }}</label>
                    @if($howItWorksStep->image_path)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $howItWorksStep->image_path) }}" alt="Current image" class="img-thumbnail" style="max-width: 100px;">
                        </div>
                    @endif
                    <input type="file" name="image_path" id="image_path" class="form-control" accept="image/*">
                    <small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
                </div>
                <div class="col-md-3">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $howItWorksStep->order) }}" class="form-control" min="0">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $howItWorksStep->is_active) ? 'checked' : '' }}>
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
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="step-tab-{{ $locale }}" data-bs-toggle="tab" data-bs-target="#step-{{ $locale }}" type="button" role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($locales as $index => $locale)
                                    @php
                                        $translation = $howItWorksStep->translate($locale);
                                    @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="step-{{ $locale }}" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="title_{{ $locale }}" class="form-label fw-semibold">{{ __('Title') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <input type="text" name="title_{{ $locale }}" id="title_{{ $locale }}" value="{{ old("title_$locale", $translation->title ?? '') }}" class="form-control @error("title_$locale") is-invalid @enderror" required>
                                                @error("title_$locale")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="description_{{ $locale }}" class="form-label fw-semibold">{{ __('Description') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <textarea name="description_{{ $locale }}" id="description_{{ $locale }}" rows="4" class="form-control @error("description_$locale") is-invalid @enderror" required>{{ old("description_$locale", $translation->description ?? '') }}</textarea>
                                                @error("description_$locale")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.how-it-works-steps.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Step') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

