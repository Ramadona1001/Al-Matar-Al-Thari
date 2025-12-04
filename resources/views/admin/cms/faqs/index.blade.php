@extends('layouts.dashboard')

@section('title', __('FAQs Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('FAQs') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-animated">
        <i class="fas fa-plus me-2"></i>{{ __('Create FAQ') }}
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
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter FAQs') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="category" class="form-label fw-semibold">{{ __('Category') }}</label>
                <select name="category" id="category" class="form-select">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
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
                       placeholder="{{ __('Search by question or answer...') }}">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FAQs Table Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-question-circle text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('FAQs') }}</h5>
                </div>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Total') }}: <strong>{{ $faqs->count() }}</strong>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="faqsTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('Question') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Category') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Order') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Status') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faqs as $faq)
                        <tr class="table-row-modern">
                            <td>
                                @php
                                    $currentTranslation = $faq->translate(app()->getLocale());
                                    $enTranslation = $faq->translate('en');
                                    $question = ($currentTranslation && $currentTranslation->question) ? $currentTranslation->question : (($enTranslation && $enTranslation->question) ? $enTranslation->question : '-');
                                @endphp
                                <div class="fw-semibold text-gray-900">{{ Str::limit($question, 80) }}</div>
                            </td>
                            <td>
                                @if($faq->category)
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1">
                                        {{ $faq->category }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-dark text-secondary rounded-pill px-3 py-1">
                                    {{ $faq->order ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($faq->is_active)
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
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" 
                                       class="btn btn-sm btn-outline-primary btn-animated" 
                                       title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.faqs.destroy', $faq) }}" 
                                          method="post" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this FAQ?') }}')">
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
        
        const $table = jQuery('#faqsTable');
        if ($.fn.DataTable.isDataTable('#faqsTable')) {
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
            order: [[2, 'asc']],
            columnDefs: [{
                targets: [4],
                orderable: false,
                searchable: false
            }],
            language: languageUrl ? { url: languageUrl } : undefined,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
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

