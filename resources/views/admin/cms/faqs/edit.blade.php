@extends('layouts.dashboard')

@section('title', __('Edit FAQ'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">{{ __('FAQs') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-question-circle text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Edit FAQ') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="category" class="form-label fw-semibold">{{ __('Category') }}</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $faq->category) }}" class="form-control" list="categories">
                    <datalist id="categories">
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-3">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $faq->order) }}" class="form-control" min="0">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}>
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
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="faq-tab-{{ $locale }}" data-bs-toggle="tab" data-bs-target="#faq-{{ $locale }}" type="button" role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($locales as $index => $locale)
                                    @php
                                        $translation = $faq->translate($locale);
                                    @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="faq-{{ $locale }}" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="question_{{ $locale }}" class="form-label fw-semibold">{{ __('Question') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <input type="text" name="question_{{ $locale }}" id="question_{{ $locale }}" value="{{ old("question_$locale", $translation->question ?? '') }}" class="form-control @error("question_$locale") is-invalid @enderror" required>
                                                @error("question_$locale")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="answer_{{ $locale }}" class="form-label fw-semibold">{{ __('Answer') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                                <textarea name="answer_{{ $locale }}" id="answer_{{ $locale }}" rows="6" class="form-control @error("answer_$locale") is-invalid @enderror" required>{{ old("answer_$locale", $translation->answer ?? '') }}</textarea>
                                                @error("answer_$locale")
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
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Update FAQ') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

