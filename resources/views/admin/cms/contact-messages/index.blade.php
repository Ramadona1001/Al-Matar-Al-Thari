@extends('layouts.dashboard')

@section('title', __('Contact Messages Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Contact Messages') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">{{ __('Total Messages') }}</p>
                        <h3 class="mb-0 fw-bold text-gray-900">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="chart-icon-wrapper bg-primary-subtle">
                        <i class="fas fa-envelope text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">{{ __('Unread Messages') }}</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ $stats['unread'] ?? 0 }}</h3>
                    </div>
                    <div class="chart-icon-wrapper bg-warning-subtle">
                        <i class="fas fa-envelope-open text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card modern-card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">{{ __('Read Messages') }}</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['read'] ?? 0 }}</h3>
                    </div>
                    <div class="chart-icon-wrapper bg-success-subtle">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card modern-card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-primary-subtle">
                <i class="fas fa-filter text-primary"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Filters') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Messages') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.contact_messages.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="{{ __('Search by name, email, subject, or message...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-animated">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Messages Table Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-envelope text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Communication') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Contact Messages') }}</h5>
                </div>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Total') }}: <strong>{{ $messages->total() }}</strong>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="contactMessagesTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('Name') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Email') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Phone') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Subject') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Status') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Received At') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr class="table-row-modern {{ !$message->read_at ? 'table-warning' : '' }}">
                            <td>
                                <div class="fw-semibold text-gray-900">{{ $message->name }}</div>
                                @if(!$message->read_at)
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small">
                                        {{ __('New') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="mailto:{{ $message->email }}" class="text-primary text-decoration-none">
                                    {{ $message->email }}
                                </a>
                            </td>
                            <td>{{ $message->phone ?? '-' }}</td>
                            <td>
                                <div class="fw-semibold text-gray-900">{{ Str::limit($message->subject, 50) }}</div>
                            </td>
                            <td>
                                @if($message->read_at)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ __('Read') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-envelope me-1"></i>
                                        {{ __('Unread') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-gray-700">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                    {{ $message->created_at->format('Y-m-d H:i') }}
                                </div>
                                <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.contact_messages.show', $message) }}" 
                                       class="btn btn-sm btn-outline-info btn-animated" 
                                       title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.contact_messages.destroy', $message) }}" 
                                          method="post" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this message?') }}')">
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
        
        @if($messages->hasPages())
            <div class="mt-4">
                {{ $messages->links() }}
            </div>
        @endif
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
        
        const $table = jQuery('#contactMessagesTable');
        if ($.fn.DataTable.isDataTable('#contactMessagesTable')) {
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
            order: [[5, 'desc']],
            columnDefs: [{
                targets: [6],
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

