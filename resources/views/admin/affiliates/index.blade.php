@extends('layouts.dashboard')

@section('title', __('Affiliate Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Affiliates') }}</li>
@endsection

@section('content')
<div class="card modern-card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-primary-subtle">
                    <i class="fas fa-users text-primary"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Affiliate Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Affiliate Accounts') }}</h5>
                </div>
            </div>
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach(['pending', 'approved', 'suspended', 'rejected'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-bordered" id="affiliatesTable">
                <thead>
                    <tr>
                        <th>{{ __('Affiliate') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Commission Rate') }}</th>
                        <th>{{ __('Referrals') }}</th>
                        <th>{{ __('Total Earned') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affiliates as $affiliate)
                        <tr>
                            <td>{{ $affiliate->user->full_name ?? $affiliate->user->email }}</td>
                            <td>{{ $affiliate->company->name ?? '-' }}</td>
                            <td>{{ number_format($affiliate->commission_rate, 2) }}%</td>
                            <td>{{ $affiliate->total_referrals }}</td>
                            <td>{{ number_format($affiliate->total_earned, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $affiliate->status === 'approved' ? 'success' : ($affiliate->status === 'suspended' ? 'warning' : ($affiliate->status === 'rejected' ? 'danger' : 'info')) }}">
                                    {{ ucfirst($affiliate->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.affiliates.update-status', $affiliate) }}" class="d-inline-flex gap-2 align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                                        @foreach(['approved', 'suspended', 'rejected'] as $status)
                                            <option value="{{ $status }}" {{ $affiliate->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ __('Update') }}</button>
                                </form>
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
    function initAffiliatesTable() {
        if (typeof jQuery === 'undefined' || !jQuery('#affiliatesTable').length) {
            setTimeout(initAffiliatesTable, 50);
            return;
        }
        
        const $table = jQuery('#affiliatesTable');
        
        // Check if already initialized
        if ($table.hasClass('dataTable')) {
            return;
        }
        
        // Destroy if exists
        if (jQuery.fn.DataTable.isDataTable('#affiliatesTable')) {
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
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [6] } // Actions column
            ],
            language: window.DATATABLE_LOCALE === 'ar' 
                ? { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
                : {}
        });
    }
    
    // Run after page load
    if (document.readyState === 'complete') {
        setTimeout(initAffiliatesTable, 100);
    } else {
        window.addEventListener('load', function() {
            setTimeout(initAffiliatesTable, 100);
        });
    }
})();
</script>
@endpush
@endsection
