<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;

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
});

// Merchant Dashboard Routes
Route::middleware(['auth', 'role:merchant'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistics', [MerchantDashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('/dashboard/chart-data', [MerchantDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/top-customers', [MerchantDashboardController::class, 'getTopCustomers'])->name('dashboard.top-customers');
    Route::get('/dashboard/notifications', [MerchantDashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    Route::post('/dashboard/notifications/mark-as-read', [MerchantDashboardController::class, 'markNotificationsAsRead'])->name('dashboard.notifications.mark-read');
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