@extends('layouts.dashboard')

@section('title', __('Companies Management'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Companies') }}</li>
@endsection

@section('actions')
    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('Add Company') }}
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Companies') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Pending') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Approved') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('Rejected') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters') }}</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="search" id="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="{{ __('Search by name, email, phone...') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('Search') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Companies Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Companies List') }}</h6>
        @if(request('status') == 'pending' && $companies->count() > 0)
            <form method="POST" action="{{ route('admin.companies.bulk-approve') }}" id="bulkApproveForm" class="d-inline">
                @csrf
                <input type="hidden" name="company_ids" id="bulkApproveIds">
                <button type="submit" class="btn btn-success btn-sm" onclick="bulkApprove()">
                    <i class="fas fa-check me-2"></i>{{ __('Bulk Approve Selected') }}
                </button>
            </form>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" data-dt-init="false" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        @if(request('status') == 'pending')
                            <th width="50">
                                <input type="checkbox" id="selectAll">
                            </th>
                        @endif
                        <th>{{ __('Logo') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Merchant') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th width="150">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            @if(request('status') == 'pending')
                                <td>
                                    <input type="checkbox" class="company-checkbox" value="{{ $company->id }}">
                                </td>
                            @endif
                            <td>
                                @if($company->logo)
            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->localized_name }}"
                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                @endif
                            </td>
            <td>{{ $company->localized_name }}</td>
                            <td>{{ $company->email }}</td>
                            <td>{{ $company->phone ?? '-' }}</td>
                            <td>
                                @if($company->status == 'pending')
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @elseif($company->status == 'approved')
                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                @endif
                            </td>
                            <td>{{ $company->user->full_name ?? '-' }}</td>
                            <td>{{ $company->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($company->status == 'pending')
                                        <form method="POST" action="{{ route('admin.companies.approve', $company) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="{{ __('Approve') }}" 
                                                    onclick="return confirm('{{ __('Are you sure you want to approve this company?') }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.companies.reject', $company) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Reject') }}"
                                                    onclick="return confirm('{{ __('Are you sure you want to reject this company?') }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this company?') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ request('status') == 'pending' ? '9' : '8' }}" class="text-center">
                                {{ __('No companies found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $companies->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select all checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.company-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk approve function
    function bulkApprove() {
        const checkboxes = document.querySelectorAll('.company-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        if (ids.length === 0) {
            alert('{{ __('Please select at least one company.') }}');
            return false;
        }
        
        document.getElementById('bulkApproveIds').value = JSON.stringify(ids);
        return confirm('{{ __('Are you sure you want to approve selected companies?') }}');
    }
</script>
@endpush
@endsection

