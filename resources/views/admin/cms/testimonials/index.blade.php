@extends('layouts.dashboard')

@section('title', __('Testimonials Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Testimonials') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create Testimonial') }}
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
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Testimonials') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.testimonials.index') }}" class="row g-3">
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
            <div class="col-md-4">
                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="{{ __('Search by name, company...') }}">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Testimonials Table Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-quote-right text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Testimonials') }}</h5>
                </div>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Total') }}: <strong>{{ $testimonials->count() }}</strong>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="testimonialsTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('Avatar') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Name') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Company') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Rating') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Language') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Status') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $testimonial)
                        <tr class="table-row-modern">
                            <td>
                                @if($testimonial->avatar)
                                    <img src="{{ asset('storage/' . $testimonial->avatar) }}" 
                                         alt="{{ $testimonial->name }}" 
                                         class="rounded-circle" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-gray-900">{{ $testimonial->name }}</div>
                                @if($testimonial->position)
                                    <small class="text-muted">{{ $testimonial->position }}</small>
                                @endif
                            </td>
                            <td>{{ $testimonial->company ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($testimonial->rating ?? 5) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                                    {{ strtoupper($testimonial->locale ?? 'en') }}
                                </span>
                            </td>
                            <td>
                                @if($testimonial->is_active)
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
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" 
                                       class="btn btn-sm btn-outline-primary btn-animated" 
                                       title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" 
                                          method="post" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this testimonial?') }}')">
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
    function initTable() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            setTimeout(initTable, 100);
            return;
        }
        
        const $table = jQuery('#testimonialsTable');
        if ($.fn.DataTable.isDataTable('#testimonialsTable')) {
            $table.DataTable().destroy();
        }
        
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
            order: [[1, 'asc']],
            columnDefs: [{
                targets: [6],
                orderable: false,
                searchable: false
            }],
            language: languageUrl ? { url: languageUrl } : undefined,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            emptyTable: '<div class="empty-state-modern text-center py-5"><div class="empty-icon-wrapper mb-3"><i class="fas fa-quote-right"></i></div><p class="text-muted mb-0 fw-semibold">{{ __('No testimonials found') }}</p><a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm mt-3"><i class="fas fa-plus me-2"></i>{{ __('Create Your First Testimonial') }}</a></div>'
        };
        
        try {
            $table.DataTable(options);
        } catch (e) {
            console.error('DataTables initialization error:', e);
        }
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

