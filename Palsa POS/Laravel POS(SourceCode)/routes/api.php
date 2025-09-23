<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public product routes (for browsing without auth)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('/orders', [OrderController::class, 'customerOrders']);
        Route::post('/orders', [OrderController::class, 'createOrder']);
        Route::get('/orders/{orderCode}', [OrderController::class, 'orderDetails']);
        
        Route::get('/cart', [OrderController::class, 'getCart']);
        Route::post('/cart/add', [OrderController::class, 'addToCart']);
        Route::put('/cart/{id}', [OrderController::class, 'updateCart']);
        Route::delete('/cart/{id}', [OrderController::class, 'removeFromCart']);
        Route::delete('/cart/clear', [OrderController::class, 'clearCart']);
    });

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/reports/sales', [DashboardController::class, 'salesReport']);
        Route::get('/reports/inventory', [DashboardController::class, 'inventoryReport']);

        // Product management
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::post('/products/{id}/upload-image', [ProductController::class, 'uploadImage']);
        
        // Category management
        Route::apiResource('categories', CategoryController::class)->except(['index']);
        
        // Order management
        Route::get('/orders', [OrderController::class, 'adminOrders']);
        Route::put('/orders/{orderCode}/status', [OrderController::class, 'updateOrderStatus']);
        Route::get('/orders/{orderCode}/receipt', [OrderController::class, 'generateReceipt']);
        
        // Inventory management
        Route::get('/inventory/low-stock', [ProductController::class, 'lowStock']);
        Route::put('/inventory/{id}/adjust', [ProductController::class, 'adjustStock']);
    });
});

// Kenyan Payment Systems
Route::prefix('payments/kenya')->group(function () {
    Route::get('/methods', [App\Http\Controllers\Api\KenyanPaymentController::class, 'getPaymentMethods']);
    Route::post('/initiate', [App\Http\Controllers\Api\KenyanPaymentController::class, 'initiatePayment']);
    Route::post('/validate-phone', [App\Http\Controllers\Api\KenyanPaymentController::class, 'validatePhoneNumber']);
    Route::get('/bank-details', [App\Http\Controllers\Api\KenyanPaymentController::class, 'getBankDetails']);
});

// M-Pesa specific routes (part of Kenyan payments)
Route::prefix('mpesa')->group(function () {
    Route::post('/initiate-payment', [App\Http\Controllers\Api\MpesaController::class, 'initiatePayment']);
    Route::post('/check-status', [App\Http\Controllers\Api\MpesaController::class, 'checkPaymentStatus']);
    Route::post('/callback', [App\Http\Controllers\Api\MpesaController::class, 'handleCallback']);
    Route::post('/timeout', [App\Http\Controllers\Api\MpesaController::class, 'handleTimeout']);
    Route::get('/test-config', [App\Http\Controllers\Api\MpesaController::class, 'testConfiguration']);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// API version info
Route::get('/version', function () {
    return response()->json([
        'api_version' => '1.0.0',
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION
    ]);
});