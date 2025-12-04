@extends('layouts.dashboard')

@section('title', __('Services Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Services') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create Service') }}
    </a>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="card modern-card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle"><i class="fas fa-filter text-primary"></i></div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Filters') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Services') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.services.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="locale" class="form-label fw-semibold">{{ __('Language') }}</label>
                <select name="locale" id="locale" class="form-select">
                    <option value="">{{ __('All Languages') }}</option>
                    @foreach($locales ?? [] as $loc)
                        <option value="{{ $loc }}" {{ request('locale') == $loc ? 'selected' : '' }}>{{ strtoupper($loc) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('Search...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-animated"><i class="fas fa-search me-2"></i>{{ __('Search') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle"><i class="fas fa-concierge-bell text-info"></i></div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Services') }}</h5>
                </div>
            </div>
            <div class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ __('Total') }}: <strong>{{ $services->count() }}</strong></div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="servicesTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Language') }}</th>
                        <th>{{ __('Order') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Featured') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr class="table-row-modern">
                            <td>
                                @php
                                    $currentTranslation = $service->translate(app()->getLocale());
                                    $enTranslation = $service->translate('en');
                                    $title = ($currentTranslation && $currentTranslation->title) ? $currentTranslation->title : (($enTranslation && $enTranslation->title) ? $enTranslation->title : __('No Title'));
                                @endphp
                                <div class="fw-semibold">{{ $title }}</div>
                                @if($service->short_description)
                                    <small class="text-muted d-block mt-1">{{ \Illuminate\Support\Str::limit($service->short_description, 50) }}</small>
                                @endif
                            </td>
                            <td><code class="text-primary small">{{ $service->slug }}</code></td>
                            <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper($service->locale ?? 'en') }}</span></td>
                            <td><span class="badge bg-dark">{{ $service->order ?? 0 }}</span></td>
                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($service->is_featured)
                                    <span class="badge bg-info-subtle text-info"><i class="fas fa-star me-1"></i>{{ __('Featured') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary btn-animated"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-animated"><i class="fas fa-trash"></i></button>
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
    function initTable() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            setTimeout(initTable, 100);
            return;
        }
        const $table = jQuery('#servicesTable');
        if ($.fn.DataTable.isDataTable('#servicesTable')) $table.DataTable().destroy();
        const locale = window.DATATABLE_LOCALE || 'en';
        let languageUrl = null;
        switch (locale) {
            case 'ar': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'; break;
            case 'fr': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'; break;
            case 'de': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json'; break;
            case 'es': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'; break;
        }
        $table.DataTable({
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('All') }}"]],
            order: [[3, 'asc']],
            columnDefs: [{targets: [6], orderable: false, searchable: false}],
            language: languageUrl ? { url: languageUrl } : undefined,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTable);
    } else {
        initTable();
    }
})();
</script>
@endpush
@endsection

