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
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\DigitalCardController as CustomerDigitalCardController;
use App\Http\Controllers\Customer\ScanController as CustomerScanController;
use App\Http\Controllers\Customer\OfferController as CustomerOfferController;
use App\Http\Controllers\Customer\CouponController as CustomerCouponController;
use App\Http\Controllers\Customer\LoyaltyPointController as CustomerLoyaltyPointController;
use App\Http\Controllers\Customer\AffiliateController as CustomerAffiliateController;
use App\Http\Controllers\Customer\CustomerLoyaltyController as CustomerLoyaltyController;

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
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
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
});

// Merchant Dashboard Routes
Route::middleware(['auth', 'role:merchant'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistics', [MerchantDashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('/dashboard/chart-data', [MerchantDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/top-customers', [MerchantDashboardController::class, 'getTopCustomers'])->name('dashboard.top-customers');
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
});

// Customer Dashboard Routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistics', [CustomerDashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('/dashboard/chart-data', [CustomerDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/available-offers', [CustomerDashboardController::class, 'getAvailableOffers'])->name('dashboard.available-offers');
    Route::get('/dashboard/my-coupons', [CustomerDashboardController::class, 'getMyCoupons'])->name('dashboard.my-coupons');
    Route::get('/dashboard/recent-transactions', [CustomerDashboardController::class, 'getRecentTransactions'])->name('dashboard.recent-transactions');
    Route::get('/dashboard/loyalty-points', [CustomerDashboardController::class, 'getLoyaltyPoints'])->name('dashboard.loyalty-points');
    Route::get('/dashboard/favorite-companies', [CustomerDashboardController::class, 'getFavoriteCompanies'])->name('dashboard.favorite-companies');

    // Offer browsing
    Route::get('/offers', [CustomerOfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/{offer}', [CustomerOfferController::class, 'show'])->name('offers.show');

    // Coupon browsing
    Route::get('/coupons', [CustomerCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/{coupon}', [CustomerCouponController::class, 'show'])->name('coupons.show');

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
    Route::post('/digital-card/upgrade', [CustomerDigitalCardController::class, 'upgrade'])->name('digital-card.upgrade');
    
    // QR Code Scanning Routes
    Route::get('/scan', [CustomerScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/process', [CustomerScanController::class, 'process'])->name('scan.process');
    Route::post('/scan/manual-entry', [CustomerScanController::class, 'manualEntry'])->name('scan.manual-entry');
    
    // Transactions Routes
    Route::get('/transactions', [CustomerDashboardController::class, 'transactions'])->name('transactions.index');
    
    // Favorites Routes
    Route::get('/favorites', [CustomerDashboardController::class, 'favorites'])->name('favorites.index');
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
