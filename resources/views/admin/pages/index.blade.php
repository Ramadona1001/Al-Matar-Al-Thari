@extends('layouts.dashboard')

@section('title', __('Pages Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Pages') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create Page') }}
    </a>
@endsection

@section('content')
@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
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
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Pages') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.pages.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="locale" class="form-label fw-semibold">{{ __('Language') }}</label>
                <select name="locale" id="locale" class="form-select">
                    <option value="">{{ __('All Languages') }}</option>
                    @foreach($availableLocales ?? [] as $loc)
                        <option value="{{ $loc }}" {{ request('locale') == $loc ? 'selected' : '' }}>
                            {{ strtoupper($loc) }} - {{ __($loc) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="{{ __('Search by title, slug, or content...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-animated">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Pages Table Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-file-alt text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Pages') }}</h5>
                </div>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Total') }}: <strong>{{ $pages->count() }}</strong>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="pagesTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('Language') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Slug') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Title') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Status') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Created') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr class="table-row-modern">
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                                    <i class="fas fa-globe me-1"></i>
                                    {{ strtoupper($page->locale ?? 'en') }}
                                </span>
                            </td>
                            <td>
                                <code class="text-primary fw-semibold">{{ $page->slug }}</code>
                            </td>
                            <td>
                                <div class="fw-semibold text-gray-900">{{ $page->getTitleForLocale() }}</div>
                                @if($page->getExcerptForLocale())
                                    <small class="text-muted d-block mt-1">{{ \Illuminate\Support\Str::limit($page->getExcerptForLocale(), 60) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($page->is_published)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ __('Published') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ __('Draft') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-gray-700">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                    {{ $page->created_at->format('Y-m-d') }}
                                </div>
                                <small class="text-muted">
                                    {{ $page->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.pages.edit', $page) }}" 
                                       class="btn btn-sm btn-outline-primary btn-animated" 
                                       title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" 
                                          method="post" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this page?') }}')">
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
    // Wait for jQuery and DataTables to be available
    function initPagesTable() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            setTimeout(initPagesTable, 100);
            return;
        }
        
        const $table = jQuery('#pagesTable');
        
        // Destroy existing DataTable instance if any
        if ($.fn.DataTable.isDataTable('#pagesTable')) {
            $table.DataTable().destroy();
        }
        
        const locale = window.DATATABLE_LOCALE || 'en';
        let languageUrl = null;
        switch (locale) {
            case 'ar': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'; break;
            case 'fr': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'; break;
            case 'de': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json'; break;
            case 'es': languageUrl = 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'; break;
            default: languageUrl = null;
        }
        
        // Get column count from header
        const headerCols = $table.find('thead tr th').length;
        
        // Verify table structure before initialization
        const tbodyRows = $table.find('tbody tr');
        let isValidStructure = true;
        
        tbodyRows.each(function() {
            const rowCols = $(this).find('td').length;
            // Check if row has correct number of columns or is a colspan row
            const hasColspan = $(this).find('td[colspan]').length > 0;
            if (!hasColspan && rowCols !== headerCols) {
                isValidStructure = false;
                return false; // break loop
            }
        });
        
        if (!isValidStructure) {
            console.error('Table structure mismatch. Header has', headerCols, 'columns but rows have different counts.');
            return;
        }
        
        const options = {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('All') }}"]],
            order: [[4, 'desc']], // Sort by created date descending
            columnDefs: [
                {
                    targets: [5], // Actions column (0-indexed)
                    orderable: false,
                    searchable: false
                }
            ],
            language: languageUrl ? { url: languageUrl } : undefined,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            emptyTable: '<div class="empty-state-modern text-center py-5"><div class="empty-icon-wrapper mb-3"><i class="fas fa-file-alt"></i></div><p class="text-muted mb-0 fw-semibold">{{ __('No pages found') }}</p><a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm mt-3"><i class="fas fa-plus me-2"></i>{{ __('Create Your First Page') }}</a></div>'
        };
        
        // Initialize DataTables
        try {
            $table.DataTable(options);
        } catch (e) {
            console.error('DataTables initialization error:', e);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPagesTable);
    } else {
        initPagesTable();
    }
})();
</script>
@endpush
@endsection
