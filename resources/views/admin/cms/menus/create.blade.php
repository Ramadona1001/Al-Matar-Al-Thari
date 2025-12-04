@extends('layouts.dashboard')

@section('title', __('Create Menu Item'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">{{ __('Menus') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-bars text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Create Menu Item') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('admin.menus.store') }}">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">{{ __('Menu Name') }} <span class="text-danger">*</span></label>
                    <select name="name" id="name" class="form-select @error('name') is-invalid @enderror" required>
                        <option value="">{{ __('Select Menu') }}</option>
                        @foreach($menuNames ?? [] as $menuName)
                            <option value="{{ $menuName }}" {{ old('name') == $menuName ? 'selected' : '' }}>{{ ucfirst($menuName) }}</option>
                        @endforeach
                    </select>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="label-tab-{{ $locale }}" data-bs-toggle="tab" data-bs-target="#label-{{ $locale }}" type="button" role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($locales as $index => $locale)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="label-{{ $locale }}" role="tabpanel">
                                        <label for="label_{{ $locale }}" class="form-label fw-semibold">{{ __('Label') }} ({{ strtoupper($locale) }}) <span class="text-danger">*</span></label>
                                        <input type="text" name="label_{{ $locale }}" id="label_{{ $locale }}" value="{{ old("label_{$locale}") }}" class="form-control @error("label_{$locale}") is-invalid @enderror" required>
                                        @error("label_{$locale}")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="parent_id" class="form-label fw-semibold">{{ __('Parent Menu') }}</label>
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">{{ __('No Parent (Root Item)') }}</option>
                        @foreach($parents ?? [] as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="url" class="form-label fw-semibold">{{ __('URL') }}</label>
                    <input type="text" name="url" id="url" value="{{ old('url') }}" class="form-control" placeholder="/page or https://example.com">
                </div>
                <div class="col-md-6">
                    <label for="route" class="form-label fw-semibold">{{ __('Route Name') }}</label>
                    <input type="text" name="route" id="route" value="{{ old('route') }}" class="form-control" placeholder="public.home">
                </div>
                <div class="col-md-6">
                    <label for="icon" class="form-label fw-semibold">{{ __('Icon') }}</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon') }}" class="form-control" placeholder="fas fa-home">
                </div>
                <div class="col-md-6">
                    <label for="order" class="form-label fw-semibold">{{ __('Order') }}</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" class="form-control" min="0">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="open_in_new_tab" name="open_in_new_tab" value="1" {{ old('open_in_new_tab') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="open_in_new_tab">{{ __('Open in New Tab') }}</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">{{ __('Create Menu Item') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

