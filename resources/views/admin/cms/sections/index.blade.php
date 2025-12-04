@extends('layouts.dashboard')

@section('title', __('Sections Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Sections') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create Section') }}
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
            <div class="chart-icon-wrapper bg-primary-subtle">
                <i class="fas fa-filter text-primary"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Filters') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Sections') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.sections.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="page" class="form-label fw-semibold">{{ __('Page') }}</label>
                <select name="page" id="page" class="form-select">
                    <option value="">{{ __('All Pages') }}</option>
                    @foreach($pages ?? [] as $page)
                        <option value="{{ $page }}" {{ request('page') == $page ? 'selected' : '' }}>{{ ucfirst($page) }}</option>
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
                <div class="chart-icon-wrapper bg-info-subtle"><i class="fas fa-puzzle-piece text-info"></i></div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Sections') }}</h5>
                </div>
            </div>
            <div class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ __('Total') }}: <strong>{{ $sections->count() }}</strong></div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="sectionsTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Page') }}</th>
                        <th>{{ __('Order') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $section)
                        <tr class="table-row-modern">
                            <td><code class="text-primary">{{ $section->name }}</code></td>
                            @php
                                $currentTranslation = $section->translate(app()->getLocale());
                                $enTranslation = $section->translate('en');
                                $title = ($currentTranslation && $currentTranslation->title) ? $currentTranslation->title : (($enTranslation && $enTranslation->title) ? $enTranslation->title : '-');
                            @endphp
                            <td><div class="fw-semibold">{{ $title }}</div></td>
                            <td><span class="badge bg-dark">{{ $section->page ?? '-' }}</span></td>
                            <td><span class="badge bg-dark">{{ $section->order ?? 0 }}</span></td>
                            <td>
                                @if($section->is_visible)
                                    <span class="badge bg-success-subtle text-success">{{ __('Visible') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ __('Hidden') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-sm btn-outline-primary btn-animated"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.sections.destroy', $section) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
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
        const $table = jQuery('#sectionsTable');
        if ($.fn.DataTable.isDataTable('#sectionsTable')) $table.DataTable().destroy();
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
            columnDefs: [{targets: [5], orderable: false, searchable: false}],
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

