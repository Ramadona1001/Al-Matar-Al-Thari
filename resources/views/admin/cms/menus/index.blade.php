@extends('layouts.dashboard')

@section('title', __('Menus Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Menus') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create Menu Item') }}
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
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Menus') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.menus.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label fw-semibold">{{ __('Menu Name') }}</label>
                <select name="name" id="name" class="form-select">
                    <option value="">{{ __('All Menus') }}</option>
                    @foreach($menuNames ?? [] as $menuName)
                        <option value="{{ $menuName }}" {{ request('name') == $menuName ? 'selected' : '' }}>{{ ucfirst($menuName) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="locale" class="form-label fw-semibold">{{ __('Language') }}</label>
                <select name="locale" id="locale" class="form-select">
                    <option value="">{{ __('All Languages') }}</option>
                    @foreach($locales ?? [] as $loc)
                        <option value="{{ $loc }}" {{ request('locale') == $loc ? 'selected' : '' }}>{{ strtoupper($loc) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-animated"><i class="fas fa-search me-2"></i>{{ __('Search') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle"><i class="fas fa-bars text-info"></i></div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Menus') }}</h5>
                </div>
            </div>
            <div class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ __('Total') }}: <strong>{{ $menus->count() }}</strong></div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="menusTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th>{{ __('Label') }}</th>
                        <th>{{ __('Menu') }}</th>
                        <th>{{ __('Link') }}</th>
                        <th>{{ __('Parent') }}</th>
                        <th>{{ __('Language') }}</th>
                        <th>{{ __('Order') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                        <tr class="table-row-modern">
                            <td>
                                <div class="fw-semibold">{{ $menu->label }}</div>
                                @if($menu->icon)
                                    <small class="text-muted"><i class="{{ $menu->icon }}"></i></small>
                                @endif
                            </td>
                            <td><span class="badge bg-dark">{{ $menu->name }}</span></td>
                            <td><code class="text-primary small">{{ $menu->full_url ?? $menu->url ?? $menu->route ?? '#' }}</code></td>
                            <td>{{ $menu->parent ? $menu->parent->label : '-' }}</td>
                            <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper(app()->getLocale()) }}</span></td>
                            <td><span class="badge bg-dark">{{ $menu->order ?? 0 }}</span></td>
                            <td>
                                @if($menu->is_active)
                                    <span class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary btn-animated"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-animated"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @foreach($menu->children as $child)
                            <tr class="table-row-modern">
                                <td><div class="fw-semibold ms-3"><i class="fas fa-level-down-alt me-2 text-muted"></i>{{ $child->label }}</div></td>
                                <td><span class="badge bg-dark">{{ $child->name }}</span></td>
                                <td><code class="text-primary small">{{ $child->link ?? $child->route ?? '#' }}</code></td>
                                <td><span class="text-muted">{{ $menu->label }}</span></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper(app()->getLocale()) }}</span></td>
                                <td><span class="badge bg-dark">{{ $child->order ?? 0 }}</span></td>
                                <td>
                                    @if($child->is_active)
                                        <span class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.menus.edit', $child) }}" class="btn btn-sm btn-outline-primary btn-animated"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.menus.destroy', $child) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-animated"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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
        const $table = jQuery('#menusTable');
        if ($.fn.DataTable.isDataTable('#menusTable')) $table.DataTable().destroy();
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
            order: [[5, 'asc']],
            columnDefs: [{targets: [7], orderable: false, searchable: false}],
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

