<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PointsController as AdminPointsController;
use App\Http\Controllers\Admin\AffiliateController as AdminAffiliateController;
use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
use App\Http\Controllers\Merchant\LoyaltyCardController as MerchantLoyaltyCardController;
use App\Http\Controllers\Merchant\RewardController as MerchantRewardController;
use App\Http\Controllers\Merchant\LoyaltyCardMemberController as MerchantLoyaltyCardMemberController;
use App\Http\Controllers\Merchant\MemberController as MerchantMemberController;
use App\Http\Controllers\Staff\ScanController as StaffScanController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Merchant\OfferController as MerchantOfferController;
use App\Http\Controllers\Merchant\CouponController as MerchantCouponController;
use App\Http\Controllers\Merchant\BranchController as MerchantBranchController;
use App\Http\Controllers\Merchant\AffiliateController as MerchantAffiliateController;
use App\Http\Controllers\Merchant\ProductController as MerchantProductController;
use App\Http\Controllers\Merchant\SalesController as MerchantSalesController;
use App\Http\Controllers\Merchant\CustomerController as MerchantCustomerController;
use App\Http\Controllers\Merchant\TransactionController as MerchantTransactionController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\DigitalCardController as CustomerDigitalCardController;
use App\Http\Controllers\Customer\ScanController as CustomerScanController;
use App\Http\Controllers\Customer\OfferController as CustomerOfferController;
use App\Http\Controllers\Customer\CouponController as CustomerCouponController;
use App\Http\Controllers\Customer\LoyaltyPointController as CustomerLoyaltyPointController;
use App\Http\Controllers\Customer\AffiliateController as CustomerAffiliateController;
use App\Http\Controllers\Customer\CustomerLoyaltyController as CustomerLoyaltyController;
use App\Http\Controllers\Customer\TicketController as CustomerTicketController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\FreezeController as AdminFreezeController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| These routes are for the different dashboard types based on user roles.
| Each route group is protected by appropriate middleware and permissions.
|
*/

// Admin Dashboard Routes
Route::middleware(['auth', 'role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistics', [AdminDashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartDataPublic'])->name('dashboard.chart-data');
    Route::get('/dashboard/notifications', [AdminDashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    Route::post('/dashboard/notifications/mark-as-read', [AdminDashboardController::class, 'markNotificationsAsRead'])->name('dashboard.notifications.mark-read');
    
    // Company Management Routes
    Route::get('/companies', [AdminCompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [AdminCompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [AdminCompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [AdminCompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/{company}/edit', [AdminCompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [AdminCompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [AdminCompanyController::class, 'destroy'])->name('companies.destroy');
    Route::post('/companies/{company}/approve', [AdminCompanyController::class, 'approve'])->name('companies.approve');
    Route::post('/companies/{company}/reject', [AdminCompanyController::class, 'reject'])->name('companies.reject');
    Route::post('/companies/bulk-approve', [AdminCompanyController::class, 'bulkApprove'])->name('companies.bulk-approve');
    Route::post('/companies/bulk-reject', [AdminCompanyController::class, 'bulkReject'])->name('companies.bulk-reject');

    // User Management Routes
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Points settings & redemptions
    Route::get('points', [AdminPointsController::class, 'edit'])->name('points.edit');
    Route::put('points', [AdminPointsController::class, 'update'])->name('points.update');
    Route::patch('points/redemptions/{redemption}', [AdminPointsController::class, 'updateRedemption'])->name('points.redemptions.update');

    // Affiliate management
    Route::get('affiliates', [AdminAffiliateController::class, 'index'])->name('affiliates.index');
    Route::patch('affiliates/{affiliate}', [AdminAffiliateController::class, 'updateStatus'])->name('affiliates.update-status');

    // Category Management Routes
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [AdminCategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Product Management Routes
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::post('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Offer Management Routes
    Route::get('/offers', [AdminOfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/{offer}', [AdminOfferController::class, 'show'])->name('offers.show');
    Route::get('/offers/{offer}/edit', [AdminOfferController::class, 'edit'])->name('offers.edit');
    Route::put('/offers/{offer}', [AdminOfferController::class, 'update'])->name('offers.update');
    Route::delete('/offers/{offer}', [AdminOfferController::class, 'destroy'])->name('offers.destroy');
    Route::post('/offers/{offer}/toggle-featured', [AdminOfferController::class, 'toggleFeatured'])->name('offers.toggle-featured');
    Route::post('/offers/{offer}/toggle-status', [AdminOfferController::class, 'toggleStatus'])->name('offers.toggle-status');

    // Coupon Management Routes
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/{coupon}', [AdminCouponController::class, 'show'])->name('coupons.show');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::post('/coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

    // Ticket Management Routes
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/resolve', [AdminTicketController::class, 'resolve'])->name('tickets.resolve');

    // Freeze Management Routes
    Route::post('/users/{user}/freeze', [AdminFreezeController::class, 'freezeUser'])->name('users.freeze');
    Route::post('/users/{user}/unfreeze', [AdminFreezeController::class, 'unfreezeUser'])->name('users.unfreeze');
    Route::post('/companies/{company}/freeze', [AdminFreezeController::class, 'freezeCompany'])->name('companies.freeze');
    Route::post('/companies/{company}/unfreeze', [AdminFreezeController::class, 'unfreezeCompany'])->name('companies.unfreeze');
    Route::post('/cards/{card}/freeze', [AdminFreezeController::class, 'freezeCard'])->name('cards.freeze');
    Route::post('/cards/{card}/unfreeze', [AdminFreezeController::class, 'unfreezeCard'])->name('cards.unfreeze');

    // Invoice routes (Admin can view all transactions)
    Route::get('/transactions/{transaction}/invoice', [InvoiceController::class, 'download'])->name('transactions.invoice.download');
    Route::get('/transactions/{transaction}/invoice/view', [InvoiceController::class, 'view'])->name('transactions.invoice.view');
});

// Merchant Dashboard Routes
Route::middleware(['auth', 'verified', 'role:merchant', 'frozen'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistics', [MerchantDashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('/dashboard/chart-data', [MerchantDashboardController::class, 'getChartDataPublic'])->name('dashboard.chart-data');
    Route::get('/dashboard/top-customers', [MerchantDashboardController::class, 'getTopCustomersPublic'])->name('dashboard.top-customers');
    Route::get('/dashboard/notifications', [MerchantDashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    Route::post('/dashboard/notifications/mark-as-read', [MerchantDashboardController::class, 'markNotificationsAsRead'])->name('dashboard.notifications.mark-read');
    
    // Offer Management Routes
    Route::get('/offers', [MerchantOfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/create', [MerchantOfferController::class, 'create'])->name('offers.create');
    Route::post('/offers', [MerchantOfferController::class, 'store'])->name('offers.store');
    Route::get('/offers/{offer}', [MerchantOfferController::class, 'show'])->name('offers.show');
    Route::get('/offers/{offer}/edit', [MerchantOfferController::class, 'edit'])->name('offers.edit');
    Route::put('/offers/{offer}', [MerchantOfferController::class, 'update'])->name('offers.update');
    Route::delete('/offers/{offer}', [MerchantOfferController::class, 'destroy'])->name('offers.destroy');
    Route::post('/offers/{offer}/toggle-featured', [MerchantOfferController::class, 'toggleFeatured'])->name('offers.toggle-featured');
    
    // Coupon Management Routes
    Route::get('/coupons', [MerchantCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [MerchantCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [MerchantCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}', [MerchantCouponController::class, 'show'])->name('coupons.show');
    Route::get('/coupons/{coupon}/edit', [MerchantCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [MerchantCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [MerchantCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::get('/coupons/{coupon}/qr-code', [MerchantCouponController::class, 'showQrCode'])->name('coupons.qr-code');
    Route::get('/coupons/{coupon}/download-qr', [MerchantCouponController::class, 'downloadQrCode'])->name('coupons.download-qr');
    Route::post('/coupons/bulk-generate', [MerchantCouponController::class, 'bulkGenerate'])->name('coupons.bulk-generate');

    // Product Management Routes
    Route::get('/products', [MerchantProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [MerchantProductController::class, 'create'])->name('products.create');
    Route::post('/products', [MerchantProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [MerchantProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [MerchantProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [MerchantProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [MerchantProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/toggle-featured', [MerchantProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

    // Sales Routes
    Route::get('/sales', [MerchantSalesController::class, 'index'])->name('sales.index');
    Route::post('/sales/process', [MerchantSalesController::class, 'processSale'])->name('sales.process');
    Route::post('/sales/process-manual', [MerchantSalesController::class, 'processSaleManual'])->name('sales.process-manual');

    // Branch Management
    Route::get('/branches', [MerchantBranchController::class, 'index'])->name('branches.index');
    Route::get('/branches/create', [MerchantBranchController::class, 'create'])->name('branches.create');
    Route::post('/branches', [MerchantBranchController::class, 'store'])->name('branches.store');
    Route::get('/branches/{branch}/edit', [MerchantBranchController::class, 'edit'])->name('branches.edit');
    Route::put('/branches/{branch}', [MerchantBranchController::class, 'update'])->name('branches.update');
    Route::delete('/branches/{branch}', [MerchantBranchController::class, 'destroy'])->name('branches.destroy');

    // Affiliate Management
    Route::get('affiliates', [MerchantAffiliateController::class, 'index'])->name('affiliates.index');
    Route::patch('affiliates/{affiliate}/status', [MerchantAffiliateController::class, 'updateStatus'])->name('affiliates.update-status');
    Route::put('affiliate-settings', [MerchantAffiliateController::class, 'updateSettings'])->name('affiliates.settings');
    // Loyalty Cards Management
    Route::get('/loyalty-cards', [MerchantLoyaltyCardController::class, 'index'])->name('loyalty-cards.index');
    Route::get('/loyalty-cards/create', [MerchantLoyaltyCardController::class, 'create'])->name('loyalty-cards.create');
    Route::post('/loyalty-cards', [MerchantLoyaltyCardController::class, 'store'])->name('loyalty-cards.store');
    Route::get('/loyalty-cards/{loyaltyCard}/edit', [MerchantLoyaltyCardController::class, 'edit'])->name('loyalty-cards.edit');
    Route::put('/loyalty-cards/{loyaltyCard}', [MerchantLoyaltyCardController::class, 'update'])->name('loyalty-cards.update');
    Route::delete('/loyalty-cards/{loyaltyCard}', [MerchantLoyaltyCardController::class, 'destroy'])->name('loyalty-cards.destroy');

    // Rewards nested under Loyalty Cards
    Route::prefix('loyalty-cards/{loyaltyCard}')->group(function () {
        Route::get('rewards', [MerchantRewardController::class, 'index'])->name('rewards.index');
        Route::get('rewards/create', [MerchantRewardController::class, 'create'])->name('rewards.create');
        Route::post('rewards', [MerchantRewardController::class, 'store'])->name('rewards.store');
        Route::get('rewards/{reward}/edit', [MerchantRewardController::class, 'edit'])->name('rewards.edit');
        Route::put('rewards/{reward}', [MerchantRewardController::class, 'update'])->name('rewards.update');
        Route::delete('rewards/{reward}', [MerchantRewardController::class, 'destroy'])->name('rewards.destroy');

        // Members listing per card
        Route::get('members', [MerchantLoyaltyCardMemberController::class, 'index'])->name('loyalty-cards.members.index');
    });

    // Users > Members
    Route::get('members', [MerchantMemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}/cards/{loyaltyCard}', [MerchantMemberController::class, 'showCard'])->name('members.cards.show');
    Route::post('members/{member}/cards/{loyaltyCard}/revert-last', [MerchantMemberController::class, 'revertLast'])->name('members.cards.revert-last');

    // Customers
    Route::get('customers', [MerchantCustomerController::class, 'index'])->name('customers.index');

    // Transactions
    Route::get('transactions', [MerchantTransactionController::class, 'index'])->name('transactions.index');

    // Invoice routes
    Route::get('transactions/{transaction}/invoice', [InvoiceController::class, 'download'])->name('transactions.invoice.download');
    Route::get('transactions/{transaction}/invoice/view', [InvoiceController::class, 'view'])->name('transactions.invoice.view');
});

// Customer Dashboard Routes
Route::middleware(['auth', 'verified', 'role:customer', 'frozen'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistics', [CustomerDashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('/dashboard/chart-data', [CustomerDashboardController::class, 'getChartDataPublic'])->name('dashboard.chart-data');
    Route::get('/dashboard/available-offers', [CustomerDashboardController::class, 'getAvailableOffersPublic'])->name('dashboard.available-offers');
    Route::get('/dashboard/my-coupons', [CustomerDashboardController::class, 'getMyCouponsPublic'])->name('dashboard.my-coupons');
    Route::get('/dashboard/recent-transactions', [CustomerDashboardController::class, 'getRecentTransactionsPublic'])->name('dashboard.recent-transactions');
    Route::get('/dashboard/loyalty-points', [CustomerDashboardController::class, 'getLoyaltyPointsPublic'])->name('dashboard.loyalty-points');
    Route::get('/dashboard/favorite-companies', [CustomerDashboardController::class, 'getFavoriteCompaniesPublic'])->name('dashboard.favorite-companies');

    // Offer browsing
    Route::get('/offers', [CustomerOfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/{offer}', [CustomerOfferController::class, 'show'])->name('offers.show');

    // Coupon browsing
    Route::get('/coupons', [CustomerCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/{coupon}', [CustomerCouponController::class, 'show'])->name('coupons.show');

    // Product browsing and purchase
    Route::get('/products', [\App\Http\Controllers\Customer\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [\App\Http\Controllers\Customer\ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product}/purchase', [\App\Http\Controllers\Customer\ProductController::class, 'purchase'])->name('products.purchase');

    // Loyalty points
    Route::get('/loyalty', [CustomerLoyaltyPointController::class, 'index'])->name('loyalty.index');
    Route::post('/loyalty/redeem', [CustomerLoyaltyPointController::class, 'store'])->name('loyalty.redeem');

    // Wallet homepage & follow/unfollow
    Route::get('/wallet', [CustomerLoyaltyController::class, 'index'])->name('wallet.index');
    Route::post('/cards/{loyaltyCard}/follow', [CustomerLoyaltyController::class, 'follow'])->name('cards.follow');
    Route::delete('/cards/{loyaltyCard}/unfollow', [CustomerLoyaltyController::class, 'unfollow'])->name('cards.unfollow');

    // Request Points Links
    Route::get('/requests', [\App\Http\Controllers\Customer\PointRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [\App\Http\Controllers\Customer\PointRequestController::class, 'store'])->name('requests.store');

    // Redeem Points Codes
    Route::get('/redeem-codes', [\App\Http\Controllers\Customer\RedeemCodeController::class, 'index'])->name('redeem-codes.index');
    Route::post('/redeem-codes', [\App\Http\Controllers\Customer\RedeemCodeController::class, 'store'])->name('redeem-codes.store');
    
    // Affiliate program
    Route::get('/affiliate', [CustomerAffiliateController::class, 'index'])->name('affiliate.index');
    Route::post('/affiliate', [CustomerAffiliateController::class, 'store'])->name('affiliate.store');
    
    // Digital Card Routes
    Route::get('/digital-card', [CustomerDigitalCardController::class, 'index'])->name('digital-card.index');
    Route::get('/digital-card/show', [CustomerDigitalCardController::class, 'show'])->name('digital-card.show');
    Route::get('/digital-card/download-qr', [CustomerDigitalCardController::class, 'downloadQrCode'])->name('digital-card.download-qr');
    Route::get('/digital-card/download', [CustomerDigitalCardController::class, 'downloadCard'])->name('digital-card.download');
    
    // QR Code Scanning Routes
    Route::get('/scan', [CustomerScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/process', [CustomerScanController::class, 'process'])->name('scan.process');
    Route::post('/scan/manual-entry', [CustomerScanController::class, 'manualEntry'])->name('scan.manual-entry');
    
    // Transactions Routes
    Route::get('/transactions', [CustomerDashboardController::class, 'transactions'])->name('transactions.index');

    // Invoice routes
    Route::get('/transactions/{transaction}/invoice', [InvoiceController::class, 'download'])->name('transactions.invoice.download');
    Route::get('/transactions/{transaction}/invoice/view', [InvoiceController::class, 'view'])->name('transactions.invoice.view');
    
    // Favorites Routes
    Route::get('/favorites', [CustomerDashboardController::class, 'favorites'])->name('favorites.index');
    
    // Ticket Routes
    Route::get('/tickets', [CustomerTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [CustomerTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [CustomerTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [CustomerTicketController::class, 'show'])->name('tickets.show');
});

// Common Dashboard Routes (accessible to all authenticated users)
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    // Language switcher
Route::get('/language/{switchLocale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');
});

// Default dashboard redirect based on role
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('merchant')) {
        return redirect()->route('merchant.dashboard');
    } elseif ($user->hasRole('customer')) {
        return redirect()->route('customer.dashboard');
    }
    
    return redirect()->route('home');
})->name('dashboard');
// Staff Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/scan', [StaffScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/award', [StaffScanController::class, 'award'])->name('scan.award');
    Route::post('/scan/validate', [StaffScanController::class, 'validateReward'])->name('scan.validate');
});
