@extends('layouts.dashboard')

@section('title', __('Branches'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Branches') }}</li>
@endsection

@section('actions')
    <a href="{{ route('merchant.branches.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('Add Branch') }}
    </a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Branches for :company', ['company' => $company->name]) }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Address') }}</th>
                        <th>{{ __('City') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th width="150">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->full_address }}</td>
                            <td>{{ $branch->city ?? '-' }}</td>
                            <td>{{ $branch->phone ?? '-' }}</td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('merchant.branches.edit', $branch) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('merchant.branches.destroy', $branch) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this branch?') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('No branches found. Add your first branch!') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $branches->links() }}
        </div>
    </div>
</div>
@endsection
