@extends('layouts.dashboard')

@section('title', __('Banners Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Banners') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create Banner') }}
    </a>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card modern-card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle">
                <i class="fas fa-filter text-primary"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Filters') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Banners') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.banners.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="locale" class="form-label fw-semibold">{{ __('Language') }}</label>
                <select name="locale" id="locale" class="form-select">
                    <option value="">{{ __('All Languages') }}</option>
                    @foreach($locales ?? [] as $loc)
                        <option value="{{ $loc }}" {{ request('locale') == $loc ? 'selected' : '' }}>
                            {{ strtoupper($loc) }} - {{ __($loc) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="{{ __('Search by title or subtitle...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-animated">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Banners Table Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-image text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Banners') }}</h5>
                </div>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Total') }}: <strong>{{ $banners->count() }}</strong>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="bannersTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('Image') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Language') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Title') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Order') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Status') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Created') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banners as $banner)
                        @php
                            $currentTranslation = $banner->translate(app()->getLocale());
                            $enTranslation = $banner->translate('en');
                            $arTranslation = $banner->translate('ar');
                            $title = ($currentTranslation && $currentTranslation->title) ? $currentTranslation->title : (($enTranslation && $enTranslation->title) ? $enTranslation->title : __('No Title'));
                            $subtitle = ($currentTranslation && $currentTranslation->subtitle) ? $currentTranslation->subtitle : (($enTranslation && $enTranslation->subtitle) ? $enTranslation->subtitle : null);
                        @endphp
                        <tr class="table-row-modern">
                            <td>
                                @if($banner->image_path)
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                         alt="{{ $title }}" 
                                         class="rounded" 
                                         style="width: 80px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($enTranslation && $enTranslation->title)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fw-semibold small">
                                            <i class="fas fa-globe me-1"></i>EN
                                        </span>
                                    @endif
                                    @if($arTranslation && $arTranslation->title)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fw-semibold small">
                                            <i class="fas fa-globe me-1"></i>AR
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-gray-900">{{ $title }}</div>
                                @if($subtitle)
                                    <small class="text-muted d-block mt-1">{{ \Illuminate\Support\Str::limit($subtitle, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-dark text-secondary rounded-pill px-3 py-1">
                                    {{ $banner->order ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($banner->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i>
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-gray-700">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                    {{ $banner->created_at->format('Y-m-d') }}
                                </div>
                                <small class="text-muted">
                                    {{ $banner->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" 
                                       class="btn btn-sm btn-outline-primary btn-animated" 
                                       title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                          method="post" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this banner?') }}')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger btn-animated" 
                                                title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    function initBannersTable() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            setTimeout(initBannersTable, 100);
            return;
        }
        
        const $table = jQuery('#bannersTable');
        if ($.fn.DataTable.isDataTable('#bannersTable')) {
            $table.DataTable().destroy();
        }
        
        const headerCols = $table.find('thead tr th').length;
        const locale = window.DATATABLE_LOCALE || 'en';
        let languageUrl = null;
        switch (locale) {
            case 'ar': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'; break;
            case 'fr': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'; break;
            case 'de': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json'; break;
            case 'es': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'; break;
        }
        
        const options = {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('All') }}"]],
            order: [[5, 'desc']],
            columnDefs: [{
                targets: [6],
                orderable: false,
                searchable: false
            }],
            language: languageUrl ? { url: languageUrl } : undefined,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            emptyTable: '<div class="empty-state-modern text-center py-5"><div class="empty-icon-wrapper mb-3"><i class="fas fa-image"></i></div><p class="text-muted mb-0 fw-semibold">{{ __('No banners found') }}</p><a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm mt-3"><i class="fas fa-plus me-2"></i>{{ __('Create Your First Banner') }}</a></div>'
        };
        
        try {
            $table.DataTable(options);
        } catch (e) {
            console.error('DataTables initialization error:', e);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBannersTable);
    } else {
        initBannersTable();
    }
})();
</script>
@endpush
@endsection

