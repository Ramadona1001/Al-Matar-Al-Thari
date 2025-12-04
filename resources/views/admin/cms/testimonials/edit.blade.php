@extends('layouts.dashboard')

@section('title', __('Edit Testimonial'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">{{ __('Testimonials') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-quote-right text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit Testimonial') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        @if($testimonial->avatar)
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('Current Avatar') }}</label>
                <div><img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;"></div>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="avatar" class="form-label fw-semibold">{{ __('New Avatar') }}</label>
                    <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                    <small class="text-muted">{{ __('Leave empty to keep current avatar') }}</small>
                </div>
                <div class="col-md-6">
                    <label for="rating" class="form-label fw-semibold">{{ __('Rating') }}</label>
                    <select name="rating" id="rating" class="form-select">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>{{ $i }} {{ __('Stars') }}</option>
                        @endfor
                    </select>
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
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="testimonial-tab-{{ $locale }}" data-bs-toggle="tab" data-bs-target="#testimonial-{{ $locale }}" type="button" role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($locales as $index => $locale)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="testimonial-{{ $locale }}" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="name_{{ $locale }}" class="form-label fw-semibold">{{ __('Name') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <input type="text" name="name_{{ $locale }}" id="name_{{ $locale }}" value="{{ old("name_{$locale}", $testimonial->translate($locale)->name ?? '') }}" class="form-control @error("name_{$locale}") is-invalid @enderror" required>
                                                @error("name_{$locale}")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="position_{{ $locale }}" class="form-label fw-semibold">{{ __('Position') }} ({{ strtoupper($locale) }})</label>
                                                <input type="text" name="position_{{ $locale }}" id="position_{{ $locale }}" value="{{ old("position_{$locale}", $testimonial->translate($locale)->position ?? '') }}" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_{{ $locale }}" class="form-label fw-semibold">{{ __('Company') }} ({{ strtoupper($locale) }})</label>
                                                <input type="text" name="company_{{ $locale }}" id="company_{{ $locale }}" value="{{ old("company_{$locale}", $testimonial->translate($locale)->company ?? '') }}" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <label for="testimonial_{{ $locale }}" class="form-label fw-semibold">{{ __('Testimonial') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <textarea name="testimonial_{{ $locale }}" id="testimonial_{{ $locale }}" rows="5" class="form-control @error("testimonial_{$locale}") is-invalid @enderror" required>{{ old("testimonial_{$locale}", $testimonial->translate($locale)->testimonial ?? '') }}</textarea>
                                                @error("testimonial_{$locale}")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $testimonial->order) }}" class="form-control" min="0">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_featured">{{ __('Featured') }}</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update Testimonial') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

