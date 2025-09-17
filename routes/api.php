<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Auth\AdminAuthController;

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

// Public API routes
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('register', [AdminAuthController::class, 'register']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AdminAuthController::class, 'logout']);
            Route::get('me', [AdminAuthController::class, 'me']);
        });
    });
    
    // Test route for debugging (remove in production)
    Route::get('test/orders', [OrderController::class, 'adminIndex']);
    
    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::get('categories-with-products', [CategoryController::class, 'withProducts']);
    
    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('products/{id}/price-lists', [ProductController::class, 'getPriceLists']);
    Route::get('products/slug/{slug}', [ProductController::class, 'bySlug']);
    Route::get('products-featured', [ProductController::class, 'featured']);
    
    // Orders
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{orderNumber}/track', [OrderController::class, 'track']);
    Route::get('orders/{orderNumber}/payment', [PaymentController::class, 'getOrderPayment']);
    Route::get('orders/{orderNumber}/status', [PaymentController::class, 'getOrderStatus']);
    Route::get('orders/recent', [OrderController::class, 'recent']);
    
    // Public Settings
    Route::get('settings/public', [SettingController::class, 'public']);
    
    // Payment routes
    Route::post('payment/create', [PaymentController::class, 'createPayment']);
    Route::post('payment/notification', [PaymentController::class, 'handleNotification']);
    Route::post('payment/webhook', [PaymentController::class, 'webhook']); // Midtrans webhook
    Route::post('payment/status', [PaymentController::class, 'checkStatus']);
    
    // QRIS Payment routes
    Route::post('payment/qris/create', [PaymentController::class, 'createQrisPayment']);
    Route::post('payment/qris/status', [PaymentController::class, 'checkQrisStatus']);
    Route::post('payment/qris/cancel', [PaymentController::class, 'cancelQrisPayment']);
    Route::post('payment/qris/webhook', [PaymentController::class, 'handleQrisWebhook']);
    
    // Payment security
    Route::post('payment/token', [PaymentController::class, 'getPaymentToken']);
    
});

// Payment callback routes (outside API prefix for Midtrans)
Route::get('/payment/finish', [PaymentController::class, 'paymentFinish']);
Route::get('/payment/unfinish', [PaymentController::class, 'paymentUnfinish']);
Route::get('/payment/error', [PaymentController::class, 'paymentError']);

// Admin API routes (protected)
Route::prefix('v1/admin')->middleware(['auth:sanctum'])->group(function () {
    
    // Dashboard stats
    Route::get('dashboard/stats', [OrderController::class, 'dashboardStats']);
    
    // Categories Management
    Route::apiResource('categories', CategoryController::class);
    
    // Products Management
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::put('products/{id}/toggle-status', [ProductController::class, 'toggleStatus']);
    Route::post('products/{id}/image', [ProductController::class, 'updateImage']);
    Route::post('products/sync-digiflazz', [ProductController::class, 'syncDigiflazz']);
    Route::post('products/update-prices', [ProductController::class, 'updatePrices']);
    
    // Price Lists Management
    Route::get('products/{id}/price-lists', [ProductController::class, 'getPriceLists']);
    Route::post('products/{id}/price-lists', [ProductController::class, 'storePriceList']);
    Route::put('products/{productId}/price-lists/{priceListId}', [ProductController::class, 'updatePriceList']);
    Route::delete('products/{productId}/price-lists/{priceListId}', [ProductController::class, 'deletePriceList']);
    
    // Orders Management
    Route::get('orders', [OrderController::class, 'adminIndex']);
    Route::put('orders/{id}/process', [OrderController::class, 'processOrder']);
    Route::put('orders/{id}/complete', [OrderController::class, 'completeOrder']);
    Route::put('orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
    Route::post('orders/{id}/update-status', [OrderController::class, 'updateStatus']);
    Route::post('orders/{id}/process-items', [OrderController::class, 'processItems']);
    // Payment confirmation routes
    Route::put('orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment']);
    Route::put('orders/{id}/reject-payment', [OrderController::class, 'rejectPayment']);
    
    // Settings Management
    Route::get('settings', [SettingController::class, 'adminIndex']);
    Route::put('settings/profit', [SettingController::class, 'updateProfitSettings']);
    Route::put('settings/system', [SettingController::class, 'updateSystemSettings']);
    Route::get('settings/group/{group}', [SettingController::class, 'byGroup']);
    
    // API Testing
    Route::post('test/digiflazz', [SettingController::class, 'testDigiflazz']);
    Route::post('test/midtrans', [SettingController::class, 'testMidtrans']);
    
    // Activities
    Route::get('activities', [SettingController::class, 'getActivities']);
    
});
