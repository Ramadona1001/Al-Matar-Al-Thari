<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PointsController as AdminPointsController;
use App\Http\Controllers\Admin\AffiliateController as AdminAffiliateController;
use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
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
    Route::resource('companies', AdminCompanyController::class);
    Route::post('companies/{company}/approve', [AdminCompanyController::class, 'approve'])->name('companies.approve');
    Route::post('companies/{company}/reject', [AdminCompanyController::class, 'reject'])->name('companies.reject');
    Route::post('companies/bulk-approve', [AdminCompanyController::class, 'bulkApprove'])->name('companies.bulk-approve');
    Route::post('companies/bulk-reject', [AdminCompanyController::class, 'bulkReject'])->name('companies.bulk-reject');

    // User Management Routes
    Route::resource('users', AdminUserController::class)->except(['show']);

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
    Route::resource('offers', MerchantOfferController::class);
    Route::post('offers/{offer}/toggle-featured', [MerchantOfferController::class, 'toggleFeatured'])->name('offers.toggle-featured');
    
    // Coupon Management Routes
    Route::resource('coupons', MerchantCouponController::class);
    Route::get('coupons/{coupon}/qr-code', [MerchantCouponController::class, 'showQrCode'])->name('coupons.qr-code');
    Route::get('coupons/{coupon}/download-qr', [MerchantCouponController::class, 'downloadQrCode'])->name('coupons.download-qr');
    Route::post('coupons/bulk-generate', [MerchantCouponController::class, 'bulkGenerate'])->name('coupons.bulk-generate');

    // Branch Management
    Route::resource('branches', MerchantBranchController::class)->except(['show']);

    // Affiliate Management
    Route::get('affiliates', [MerchantAffiliateController::class, 'index'])->name('affiliates.index');
    Route::patch('affiliates/{affiliate}/status', [MerchantAffiliateController::class, 'updateStatus'])->name('affiliates.update-status');
    Route::put('affiliate-settings', [MerchantAffiliateController::class, 'updateSettings'])->name('affiliates.settings');
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
    Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');
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