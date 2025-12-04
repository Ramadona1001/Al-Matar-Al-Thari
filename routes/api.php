<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Admin\AnalyticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Payment API
Route::prefix('payments')->group(function () {
    Route::get('/methods', [PaymentController::class, 'getPaymentMethods']);
    Route::post('/create', [PaymentController::class, 'createPayment']);
    Route::post('/process', [PaymentController::class, 'processPayment']);
    Route::get('/status/{transactionId}', [PaymentController::class, 'getTransactionStatus']);
    Route::post('/refund', [PaymentController::class, 'processRefund']);
    Route::post('/webhook/{gatewayType}', [PaymentController::class, 'webhook']);
});

// Analytics JSON endpoints (secured behind auth if needed)
Route::prefix('analytics')->group(function () {
    Route::get('/metrics', [AnalyticsController::class, 'metrics']);
    Route::get('/transactions', [AnalyticsController::class, 'transactionsChartData']);
    Route::get('/affiliates', [AnalyticsController::class, 'affiliatePerformanceData']);
    Route::get('/coupons', [AnalyticsController::class, 'couponUsageData']);
    Route::get('/points', [AnalyticsController::class, 'loyaltyPointsData']);
});
