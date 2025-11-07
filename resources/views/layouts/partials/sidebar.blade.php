@php
    $user = auth()->user();
    $userRole = $user->roles->first()->name ?? 'customer';
@endphp

@if($userRole === 'super-admin' || $userRole === 'admin')
    <!-- Admin Sidebar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="fas fa-users me-2"></i>{{ __('Users') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" href="{{ route('admin.companies.index') }}">
            <i class="fas fa-building me-2"></i>{{ __('Companies') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}" href="{{ route('admin.offers.index') }}">
            <i class="fas fa-tags me-2"></i>{{ __('Offers') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
            <i class="fas fa-ticket-alt me-2"></i>{{ __('Coupons') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
            <i class="fas fa-exchange-alt me-2"></i>{{ __('Transactions') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <i class="fas fa-list me-2"></i>{{ __('Categories') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
            <i class="fas fa-chart-bar me-2"></i>{{ __('Reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
        </a>
    </li>

@elseif($userRole === 'merchant')
    <!-- Merchant Sidebar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.dashboard') ? 'active' : '' }}" href="{{ route('merchant.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.company.*') ? 'active' : '' }}" href="{{ route('merchant.company.edit') }}">
            <i class="fas fa-building me-2"></i>{{ __('My Company') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.offers.*') ? 'active' : '' }}" href="{{ route('merchant.offers.index') }}">
            <i class="fas fa-tags me-2"></i>{{ __('Offers') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.coupons.*') ? 'active' : '' }}" href="{{ route('merchant.coupons.index') }}">
            <i class="fas fa-ticket-alt me-2"></i>{{ __('Coupons') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.transactions.*') ? 'active' : '' }}" href="{{ route('merchant.transactions.index') }}">
            <i class="fas fa-exchange-alt me-2"></i>{{ __('Transactions') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.customers.*') ? 'active' : '' }}" href="{{ route('merchant.customers.index') }}">
            <i class="fas fa-users me-2"></i>{{ __('Customers') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.loyalty.*') ? 'active' : '' }}" href="{{ route('merchant.loyalty.index') }}">
            <i class="fas fa-star me-2"></i>{{ __('Loyalty Points') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.analytics.*') ? 'active' : '' }}" href="{{ route('merchant.analytics.index') }}">
            <i class="fas fa-chart-line me-2"></i>{{ __('Analytics') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.settings.*') ? 'active' : '' }}" href="{{ route('merchant.settings.index') }}">
            <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
        </a>
    </li>

@else
    <!-- Customer Sidebar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.offers.*') ? 'active' : '' }}" href="{{ route('customer.offers.index') }}">
            <i class="fas fa-tags me-2"></i>{{ __('Offers') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.coupons.*') ? 'active' : '' }}" href="{{ route('customer.coupons.index') }}">
            <i class="fas fa-ticket-alt me-2"></i>{{ __('My Coupons') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.transactions.*') ? 'active' : '' }}" href="{{ route('customer.transactions.index') }}">
            <i class="fas fa-exchange-alt me-2"></i>{{ __('Transactions') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.loyalty.*') ? 'active' : '' }}" href="{{ route('customer.loyalty.index') }}">
            <i class="fas fa-star me-2"></i>{{ __('Loyalty Points') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.favorites.*') ? 'active' : '' }}" href="{{ route('customer.favorites.index') }}">
            <i class="fas fa-heart me-2"></i>{{ __('Favorite Companies') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}" href="{{ route('customer.profile.edit') }}">
            <i class="fas fa-user me-2"></i>{{ __('Profile') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.settings.*') ? 'active' : '' }}" href="{{ route('customer.settings.index') }}">
            <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
        </a>
    </li>
@endif