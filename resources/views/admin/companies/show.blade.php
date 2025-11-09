@extends('layouts.dashboard')

@section('title', __('Company Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">{{ __('Companies') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $company->name }}</li>
@endsection

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
        </a>
        @if($company->status == 'pending')
            <form method="POST" action="{{ route('admin.companies.approve', $company) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('Are you sure?') }}')">
                    <i class="fas fa-check me-2"></i>{{ __('Approve') }}
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Company Information') }}</h6>
            </div>
            <div class="card-body text-center">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" 
                         class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-building fa-4x"></i>
                    </div>
                @endif
                
                <h4>{{ $company->name }}</h4>
                
                @if($company->status == 'pending')
                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                @elseif($company->status == 'approved')
                    <span class="badge bg-success">{{ __('Approved') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                @endif
                
                @if($company->description)
                    <p class="mt-3 text-muted">{{ $company->description }}</p>
                @endif
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Statistics') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ __('Total Offers') }}:</strong>
                    <span class="float-end">{{ $stats['total_offers'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Active Offers') }}:</strong>
                    <span class="float-end">{{ $stats['active_offers'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Total Coupons') }}:</strong>
                    <span class="float-end">{{ $stats['total_coupons'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Used Coupons') }}:</strong>
                    <span class="float-end">{{ $stats['used_coupons'] }}</span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Total Transactions') }}:</strong>
                    <span class="float-end">{{ $stats['total_transactions'] }}</span>
                </div>
                <div>
                    <strong>{{ __('Total Revenue') }}:</strong>
                    <span class="float-end">{{ number_format($stats['total_revenue'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Contact Information') }}</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">{{ __('Email') }}:</th>
                        <td>{{ $company->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Phone') }}:</th>
                        <td>{{ $company->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Website') }}:</th>
                        <td>
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Address') }}:</th>
                        <td>{{ $company->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('City') }}:</th>
                        <td>{{ $company->city ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Country') }}:</th>
                        <td>{{ $company->country ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Tax Number') }}:</th>
                        <td>{{ $company->tax_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Commercial Register') }}:</th>
                        <td>{{ $company->commercial_register ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Affiliate Commission Rate') }}:</th>
                        <td>{{ $company->affiliate_commission_rate }}%</td>
                    </tr>
                    <tr>
                        <th>{{ __('Merchant') }}:</th>
                        <td>{{ $company->user->full_name ?? '-' }} ({{ $company->user->email ?? '-' }})</td>
                    </tr>
                    <tr>
                        <th>{{ __('Created At') }}:</th>
                        <td>{{ $company->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($company->branches->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Branches') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->branches as $branch)
                                <tr>
                                    <td>{{ $branch->name }}</td>
                                    <td>{{ $branch->address }}</td>
                                    <td>{{ $branch->city }}</td>
                                    <td>
                                        @if($branch->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

