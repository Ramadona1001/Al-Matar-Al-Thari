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
    <!-- Reward Loyalty -->
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Reward Loyalty') }}</li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.networks.*') ? 'active' : '' }}" href="{{ route('admin.networks.index') }}">
            <i class="fas fa-network-wired me-2"></i>{{ __('Networks') }}
        </a>
    </li>
    @if(Route::has('admin.loyalty-cards.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.loyalty-cards.*') ? 'active' : '' }}" href="{{ route('admin.loyalty-cards.index') }}">
                <i class="fas fa-id-card me-2"></i>{{ __('Loyalty Cards') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.users.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="fas fa-users me-2"></i>{{ __('Users') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.companies.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" href="{{ route('admin.companies.index') }}">
                <i class="fas fa-building me-2"></i>{{ __('Companies') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.products.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                <i class="fas fa-box me-2"></i>{{ __('Products') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.offers.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}" href="{{ route('admin.offers.index') }}">
                <i class="fas fa-tags me-2"></i>{{ __('Offers') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.coupons.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                <i class="fas fa-ticket-alt me-2"></i>{{ __('Coupons') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.transactions.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
                <i class="fas fa-exchange-alt me-2"></i>{{ __('Transactions') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.categories.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="fas fa-list me-2"></i>{{ __('Categories') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.reports.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                <i class="fas fa-chart-bar me-2"></i>{{ __('Reports') }}
            </a>
        </li>
    @endif
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('CMS Management') }}</li>
    @if(Route::has('admin.sections.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" href="{{ route('admin.sections.index') }}">
                <i class="fas fa-th-large me-2"></i>{{ __('Sections') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.page-builder.show'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.page-builder.*') ? 'active' : '' }}" href="{{ route('admin.page-builder.show', 'home') }}">
                <i class="fas fa-palette me-2"></i>{{ __('Page Builder') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.banners.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                <i class="fas fa-images me-2"></i>{{ __('Banners') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.menus.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}" href="{{ route('admin.menus.index') }}">
                <i class="fas fa-bars me-2"></i>{{ __('Menus') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.services.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">
                <i class="fas fa-concierge-bell me-2"></i>{{ __('Services') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.blogs.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}">
                <i class="fas fa-blog me-2"></i>{{ __('Blog Posts') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.testimonials.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}" href="{{ route('admin.testimonials.index') }}">
                <i class="fas fa-quote-left me-2"></i>{{ __('Testimonials') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.statistics.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}" href="{{ route('admin.statistics.index') }}">
                <i class="fas fa-chart-pie me-2"></i>{{ __('Statistics') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.how-it-works-steps.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.how-it-works-steps.*') ? 'active' : '' }}" href="{{ route('admin.how-it-works-steps.index') }}">
                <i class="fas fa-list-ol me-2"></i>{{ __('How It Works Steps') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.faqs.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
                <i class="fas fa-question-circle me-2"></i>{{ __('FAQs') }}
            </a>
        </li>
    @endif
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Site Management') }}</li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.site.settings.*') ? 'active' : '' }}" href="{{ route('admin.site.settings.edit') }}">
            <i class="fas fa-brush me-2"></i>{{ __('Brand & Settings') }}
        </a>
    </li>
    @if(Route::has('admin.social-media.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.social-media.*') ? 'active' : '' }}" href="{{ route('admin.social-media.index') }}">
                <i class="fab fa-facebook me-2"></i>{{ __('Social Media') }}
            </a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
            <i class="fas fa-file-alt me-2"></i>{{ __('Pages') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.translations.*') ? 'active' : '' }}" href="{{ route('admin.translations.index') }}">
            <i class="fas fa-language me-2"></i>{{ __('Translations') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.contact_messages.*') ? 'active' : '' }}" href="{{ route('admin.contact_messages.index') }}">
            <i class="fas fa-envelope me-2"></i>{{ __('Contact Messages') }}
        </a>
    </li>
    @if(Route::has('admin.settings.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.points.edit'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.points.*') ? 'active' : '' }}" href="{{ route('admin.points.edit') }}">
                <i class="fas fa-star me-2"></i>{{ __('Points') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.affiliates.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}" href="{{ route('admin.affiliates.index') }}">
                <i class="fas fa-hands-helping me-2"></i>{{ __('Affiliates') }}
            </a>
        </li>
    @endif
    @if(Route::has('admin.tickets.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                <i class="fas fa-ticket-alt me-2"></i>{{ __('Tickets') }}
            </a>
        </li>
    @endif

@elseif($userRole === 'manager')
    <!-- Manager Sidebar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}" href="{{ route('manager.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
        </a>
    </li>
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Reward Loyalty') }}</li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('manager.partners.*') ? 'active' : '' }}" href="{{ route('manager.partners.index') }}">
            <i class="fas fa-handshake me-2"></i>{{ __('Partners') }}
        </a>
    </li>
    @if(Route::has('merchant.loyalty-cards.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.loyalty-cards.*') ? 'active' : '' }}" href="{{ route('merchant.loyalty-cards.index', app()->getLocale()) }}">
                <i class="fas fa-id-card me-2"></i>{{ __('Loyalty Cards') }}
            </a>
        </li>
    @endif

@elseif($userRole === 'merchant' || $userRole === 'partner')
    <!-- Merchant Sidebar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('merchant.dashboard') ? 'active' : '' }}" href="{{ route('merchant.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
        </a>
    </li>
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Products & Offers') }}</li>
    @if(Route::has('merchant.products.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.products.*') ? 'active' : '' }}" href="{{ route('merchant.products.index') }}">
                <i class="fas fa-box me-2"></i>{{ __('Products') }}
            </a>
        </li>
    @endif
    @if(Route::has('merchant.sales.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.sales.*') ? 'active' : '' }}" href="{{ route('merchant.sales.index') }}">
                <i class="fas fa-cash-register me-2"></i>{{ __('Sales') }}
            </a>
        </li>
    @endif
    @if(Route::has('merchant.offers.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.offers.*') ? 'active' : '' }}" href="{{ route('merchant.offers.index') }}">
                <i class="fas fa-tags me-2"></i>{{ __('Offers') }}
            </a>
        </li>
    @endif
    @if(Route::has('merchant.coupons.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.coupons.*') ? 'active' : '' }}" href="{{ route('merchant.coupons.index') }}">
                <i class="fas fa-ticket-alt me-2"></i>{{ __('Coupons') }}
            </a>
        </li>
    @endif
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Users & Management') }}</li>
    @if(Route::has('merchant.customers.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.customers.*') ? 'active' : '' }}" href="{{ route('merchant.customers.index') }}">
                <i class="fas fa-users me-2"></i>{{ __('Customers') }}
            </a>
        </li>
    @endif
    @if(Route::has('merchant.transactions.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.transactions.*') ? 'active' : '' }}" href="{{ route('merchant.transactions.index') }}">
                <i class="fas fa-exchange-alt me-2"></i>{{ __('Transactions') }}
            </a>
        </li>
    @endif
    @if(Route::has('merchant.branches.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.branches.*') ? 'active' : '' }}" href="{{ route('merchant.branches.index') }}">
                <i class="fas fa-map-marker-alt me-2"></i>{{ __('Branches') }}
            </a>
        </li>
    @endif
    @if(Route::has('merchant.affiliates.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('merchant.affiliates.*') ? 'active' : '' }}" href="{{ route('merchant.affiliates.index') }}">
                <i class="fas fa-hands-helping me-2"></i>{{ __('Affiliates') }}
            </a>
        </li>
    @endif

@else
    <!-- Customer Sidebar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
        </a>
    </li>
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Shopping & Offers') }}</li>
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
    @if(Route::has('customer.favorites.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.favorites.*') ? 'active' : '' }}" href="{{ route('customer.favorites.index') }}">
                <i class="fas fa-heart me-2"></i>{{ __('Favorite Companies') }}
            </a>
        </li>
    @endif
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Loyalty & Rewards') }}</li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.digital-card.*') ? 'active' : '' }}" href="{{ route('customer.digital-card.index') }}">
            <i class="fas fa-credit-card me-2"></i>{{ __('My Digital Card') }}
        </a>
    </li>
    @if(Route::has('customer.loyalty.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.loyalty.*') ? 'active' : '' }}" href="{{ route('customer.loyalty.index') }}">
                <i class="fas fa-star me-2"></i>{{ __('Loyalty Points') }}
            </a>
        </li>
    @endif
    @if(Route::has('customer.wallet.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.wallet.*') ? 'active' : '' }}" href="{{ route('customer.wallet.index') }}">
                <i class="fas fa-wallet me-2"></i>{{ __('Loyalty Wallet') }}
            </a>
        </li>
    @endif
    @if(Route::has('customer.requests.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.requests.*') ? 'active' : '' }}" href="{{ route('customer.requests.index') }}">
                <i class="fas fa-link me-2"></i>{{ __('Point Requests') }}
            </a>
        </li>
    @endif
    @if(Route::has('customer.redeem-codes.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.redeem-codes.*') ? 'active' : '' }}" href="{{ route('customer.redeem-codes.index') }}">
                <i class="fas fa-gift me-2"></i>{{ __('Redeem Codes') }}
            </a>
        </li>
    @endif
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Transactions & Activity') }}</li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.scan.*') ? 'active' : '' }}" href="{{ route('customer.scan.index') }}">
            <i class="fas fa-qrcode me-2"></i>{{ __('Scan QR Code') }}
        </a>
    </li>
    @if(Route::has('customer.transactions.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.transactions.*') ? 'active' : '' }}" href="{{ route('customer.transactions.index') }}">
                <i class="fas fa-exchange-alt me-2"></i>{{ __('Transactions') }}
            </a>
        </li>
    @endif
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Earn More') }}</li>
    @if(Route::has('customer.affiliate.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.affiliate.*') ? 'active' : '' }}" href="{{ route('customer.affiliate.index') }}">
                <i class="fas fa-hands-helping me-2"></i>{{ __('Affiliate Program') }}
            </a>
        </li>
    @endif
    
    <li class="nav-item mt-2 text-uppercase small  px-3" style=" color: var(--bs-blue) !important; padding: 10px; font-weight: bold; ">{{ __('Account') }}</li>
    @if(Route::has('customer.tickets.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}" href="{{ route('customer.tickets.index') }}">
                <i class="fas fa-headset me-2"></i>{{ __('Support Tickets') }}
            </a>
        </li>
    @endif
    @if(Route::has('customer.profile.edit'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}" href="{{ route('customer.profile.edit') }}">
                <i class="fas fa-user me-2"></i>{{ __('Profile') }}
            </a>
        </li>
    @endif
    @if(Route::has('customer.settings.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.settings.*') ? 'active' : '' }}" href="{{ route('customer.settings.index') }}">
                <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
            </a>
        </li>
    @endif
@endif
