@extends('layouts.dashboard')

@section('title', __('Newsletter Subscribers Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Newsletter Subscribers') }}</li>
@endsection

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.newsletter-subscribers.export') }}" class="btn btn-success btn-animated">
            <i class="fas fa-download me-2"></i>{{ __('Export CSV') }}
        </a>
        <a href="{{ route('admin.newsletter-subscribers.create') }}" class="btn btn-primary btn-animated">
            <i class="fas fa-plus me-2"></i>{{ __('Add Subscriber') }}
        </a>
    </div>
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
                        <p class="text-muted small mb-1">{{ __('Total Subscribers') }}</p>
                        <h3 class="mb-0 fw-bold text-gray-900">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="chart-icon-wrapper bg-primary-subtle">
                        <i class="fas fa-users text-primary"></i>
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
                        <p class="text-muted small mb-1">{{ __('Active Subscribers') }}</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['active'] ?? 0 }}</h3>
                    </div>
                    <div class="chart-icon-wrapper bg-success-subtle">
                        <i class="fas fa-check-circle text-success"></i>
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
                        <p class="text-muted small mb-1">{{ __('Unsubscribed') }}</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ $stats['unsubscribed'] ?? 0 }}</h3>
                    </div>
                    <div class="chart-icon-wrapper bg-warning-subtle">
                        <i class="fas fa-user-times text-warning"></i>
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
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Search & Filter Subscribers') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="status" class="form-label fw-semibold">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>{{ __('Unsubscribed') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label fw-semibold">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="{{ __('Search by email or name...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-animated">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Subscribers Table Card -->
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-envelope text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Newsletter Subscribers') }}</h5>
                </div>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Total') }}: <strong>{{ $subscribers->total() }}</strong>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive-modern">
            <table class="table table-modern align-middle mb-0" id="subscribersTable" data-dt-init="false">
                <thead class="table-header-modern">
                    <tr>
                        <th class="fw-semibold text-gray-700">{{ __('Email') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Name') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Subscribed At') }}</th>
                        <th class="fw-semibold text-gray-700">{{ __('Status') }}</th>
                        <th class="fw-semibold text-gray-700 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscribers as $subscriber)
                        <tr class="table-row-modern">
                            <td>
                                <div class="fw-semibold text-gray-900">{{ $subscriber->email }}</div>
                                @if($subscriber->source)
                                    <small class="text-muted">{{ __('Source') }}: {{ $subscriber->source }}</small>
                                @endif
                            </td>
                            <td>{{ $subscriber->name ?? '-' }}</td>
                            <td>
                                <div class="text-gray-700">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                    {{ $subscriber->subscribed_at->format('Y-m-d H:i') }}
                                </div>
                                <small class="text-muted">{{ $subscriber->subscribed_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($subscriber->is_active && !$subscriber->unsubscribed_at)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ __('Active') }}
                                    </span>
                                @elseif($subscriber->unsubscribed_at)
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-user-times me-1"></i>
                                        {{ __('Unsubscribed') }}
                                    </span>
                                @else
                                    <span class="badge bg-dark text-secondary rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i>
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.newsletter-subscribers.show', $subscriber) }}" 
                                       class="btn btn-sm btn-outline-info btn-animated" 
                                       title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.newsletter-subscribers.edit', $subscriber) }}" 
                                       class="btn btn-sm btn-outline-primary btn-animated" 
                                       title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($subscriber->is_active && !$subscriber->unsubscribed_at)
                                        <form action="{{ route('admin.newsletter-subscribers.unsubscribe', $subscriber) }}" 
                                              method="post" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-warning btn-animated" 
                                                    title="{{ __('Unsubscribe') }}">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.newsletter-subscribers.resubscribe', $subscriber) }}" 
                                              method="post" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-success btn-animated" 
                                                    title="{{ __('Resubscribe') }}">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}" 
                                          method="post" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this subscriber?') }}')">
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
        
        @if($subscribers->hasPages())
            <div class="mt-4">
                {{ $subscribers->links() }}
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
        
        const $table = jQuery('#subscribersTable');
        if ($.fn.DataTable.isDataTable('#subscribersTable')) {
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
            order: [[2, 'desc']],
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

