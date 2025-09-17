<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;

// Health check routes
Route::get('/up', [HealthController::class, 'index']);
Route::get('/health', [HealthController::class, 'detailed']);
Route::get('/health/database', [HealthController::class, 'database']);
Route::get('/health/cache', [HealthController::class, 'cache']);
Route::get('/health/external-apis', [HealthController::class, 'externalApis']);
Route::get('/health/metrics', [HealthController::class, 'metrics']);

// Login route for auth redirects
Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated. Please login via API.',
        'login_url' => '/admin/login'
    ], 401);
})->name('login');

// Catch-all route for Vue.js SPA (exclude API routes and health checks)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|up|health|login).*$');
