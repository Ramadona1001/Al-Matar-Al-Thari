@extends('layouts.dashboard')

@section('title', __('Contact Messages'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Contact Messages') }}</li>
@endsection

@section('content')
@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class="chart-icon-wrapper bg-info-subtle">
                <i class="fas fa-envelope text-info"></i>
            </div>
            <div>
                <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Communication') }}</p>
                <h5 class="fw-bold mb-0 text-gray-900">{{ __('Contact Messages') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="contactMessagesTable">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Received At') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr>
                            <td>{{ $message->name }}</td>
                            <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td>
                            <td>{{ $message->phone ?? '-' }}</td>
                            <td>{{ Str::limit($message->subject, 50) }}</td>
                            <td>{{ $message->created_at->diffForHumans() }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.contact_messages.show', $message) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                    </a>
                                    <form action="{{ route('admin.contact_messages.destroy', $message) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this message?') }}')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> {{ __('Delete') }}
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
    'use strict';
    
    // Wait for jQuery and DOM
    function initContactMessagesTable() {
        if (typeof jQuery === 'undefined' || !jQuery('#contactMessagesTable').length) {
            setTimeout(initContactMessagesTable, 50);
            return;
        }
        
        const $table = jQuery('#contactMessagesTable');
        
        // Check if already initialized
        if ($table.hasClass('dataTable')) {
            return;
        }
        
        // Destroy if exists
        if (jQuery.fn.DataTable.isDataTable('#contactMessagesTable')) {
            $table.DataTable().clear().destroy();
        }
        
        // Count actual columns in thead
        const headerCols = $table.find('thead tr th').length;
        const bodyCols = $table.find('tbody tr:first td').length;
        
        if (headerCols !== bodyCols && bodyCols > 0) {
            console.warn('Column count mismatch:', headerCols, 'headers vs', bodyCols, 'body cells');
        }
        
        // Initialize DataTable
        $table.DataTable({
            pageLength: 10,
            lengthChange: true,
            ordering: true,
            searching: true,
            order: [[4, 'desc']], // Sort by "Received" column descending
            columnDefs: [
                { orderable: false, targets: [5] } // Actions column
            ],
            language: window.DATATABLE_LOCALE === 'ar' 
                ? { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
                : {}
        });
    }
    
    // Run after page load
    if (document.readyState === 'complete') {
        setTimeout(initContactMessagesTable, 100);
    } else {
        window.addEventListener('load', function() {
            setTimeout(initContactMessagesTable, 100);
        });
    }
})();
</script>
@endpush
@endsection
