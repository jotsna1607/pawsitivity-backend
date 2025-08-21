<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\SignupController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ClientDashboardController;
use App\Http\Controllers\Api\ClientActivityController;
use App\Http\Controllers\Api\ClientAppointmentController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminProductsController;
use App\Http\Controllers\Api\AdminOrdersController;
use App\Http\Controllers\Api\AdminRefundsController;
use App\Http\Controllers\Api\AdminCustomersController;
use App\Http\Controllers\Api\AdminShippingController;
use App\Http\Controllers\Api\AdminReportsController;
use App\Http\Controllers\Api\AdminSettingsController;
use App\Http\Controllers\Api\AdminProfileController;
use App\Http\Controllers\Api\RazorpayController;
use App\Http\Controllers\Api\AddressController;

// ------------------- Public APIs -------------------
Route::post('/signup', [SignupController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);

// ------------------- Client Dashboard & Activity -------------------
Route::get('/client-dashboard/{pet}', [ClientDashboardController::class, 'getMetrics']);
Route::get('/client-activity/{pet}', [ClientActivityController::class, 'getPetActivity']);

// ------------------- Client Appointments -------------------
Route::get('/appointments', [ClientAppointmentController::class, 'index']);
Route::post('/appointments', [ClientAppointmentController::class, 'store']);
Route::put('/appointments/{id}', [ClientAppointmentController::class, 'update']);
Route::delete('/appointments/{id}', [ClientAppointmentController::class, 'destroy']);

// ------------------- Tracking -------------------
Route::get('/tracking/{petId?}', [TrackingController::class, 'index']);

// ------------------- Admin Dashboard -------------------
Route::prefix('admin/dashboard')->group(function () {
    Route::get('/notifications', [AdminDashboardController::class, 'notifications']);
    Route::get('/summary', [AdminDashboardController::class, 'summary']);
    Route::get('/sales-data', [AdminDashboardController::class, 'salesData']);
    Route::get('/product-list', [AdminDashboardController::class, 'productList']);
    Route::get('/order-list', [AdminDashboardController::class, 'orderList']);
});

// ------------------- Admin Products -------------------
Route::prefix('admin/products')->group(function () {
    Route::get('/', [AdminProductsController::class, 'index']);
    Route::get('/stats', [AdminProductsController::class, 'stats']);
    Route::get('/category-distribution', [AdminProductsController::class, 'categoryDistribution']);
    Route::get('/stock-status', [AdminProductsController::class, 'stockStatus']);
    Route::get('/sales-trend', [AdminProductsController::class, 'salesTrend']);
});

// ------------------- Admin Orders -------------------
Route::prefix('admin/orders')->group(function () {
    Route::get('/', [AdminOrdersController::class, 'index']);
    Route::get('/stats', [AdminOrdersController::class, 'stats']);
    Route::get('/status-chart', [AdminOrdersController::class, 'statusChart']);
    Route::get('/sales-trend', [AdminOrdersController::class, 'salesTrend']);
});

// ------------------- Admin Refunds -------------------
Route::prefix('admin/refunds')->group(function () {
    Route::get('/', [AdminRefundsController::class, 'index']);
    Route::get('/stats', [AdminRefundsController::class, 'stats']);
    Route::get('/refund-trend', [AdminRefundsController::class, 'refundTrend']);
    Route::put('/{id}/status', [AdminRefundsController::class, 'updateStatus']);
    Route::post('/bulk-update', [AdminRefundsController::class, 'bulkUpdate']);
    Route::get('/status-distribution', [AdminRefundsController::class, 'statusDistribution']);
    Route::get('/top-customers', [AdminRefundsController::class, 'topCustomers']);
});

// ------------------- Admin Customers -------------------
Route::prefix('admin/customers')->group(function () {
    Route::get('/', [AdminCustomersController::class, 'index']);
    Route::get('/stats', [AdminCustomersController::class, 'stats']);
    Route::get('/status-distribution', [AdminCustomersController::class, 'statusDistribution']);
    Route::get('/growth', [AdminCustomersController::class, 'growth']);
    Route::get('/top-spenders', [AdminCustomersController::class, 'topSpenders']);
});

// ------------------- Admin Shipping -------------------
Route::get('/admin/shippings', [AdminShippingController::class, 'index']);
Route::get('/admin/shippings/stats', [AdminShippingController::class, 'stats']);
Route::get('/admin/shippings/status-chart', [AdminShippingController::class, 'statusChart']);
Route::get('/admin/shippings/delivery-trend', [AdminShippingController::class, 'deliveryTrend']);

// ------------------- Admin Reports -------------------
Route::get('/admin/reports/summary', [AdminReportsController::class, 'summary']);

// ------------------- Admin Settings -------------------
Route::get('/admin/settings', [AdminSettingsController::class, 'getSettings']);
Route::post('/admin/settings', [AdminSettingsController::class, 'saveSettings']);

// ------------------- Admin Profile -------------------
Route::prefix('admin')->group(function () {
    Route::get('/profile', [AdminProfileController::class, 'getProfile']);
    Route::put('/profile/update', [AdminProfileController::class, 'updateProfile']);
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword']);
    Route::post('/profile/upload-pic', [AdminProfileController::class, 'uploadProfilePic']);
});

// ------------------- Address (Requires Auth) -------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/addresses', [AddressController::class, 'index']);
    Route::post('/user/addresses', [AddressController::class, 'store']);
    Route::put('/user/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/user/addresses/{id}', [AddressController::class, 'destroy']);
});

// ------------------- Razorpay Routes (Public for Now) -------------------
Route::post('/razorpay/order', [RazorpayController::class, 'createOrder']);
Route::post('/razorpay/verify', [RazorpayController::class, 'verifyPayment']);
Route::post('/razorpay/cod-order', [RazorpayController::class, 'codOrder']);


