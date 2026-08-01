<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Backend API is connected.',
        'status' => 'ok test',
    ]);
});

// ✅ Handle CORS preflight requests
Route::options('/{any}', function () {
    return response('')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('any', '.*');

require __DIR__.'/api/auth.php';

// ── Public Guest Menu & Order Submission (No Auth Needed) ──────────────────────
Route::get('/public/guest-links/{token}', [\App\Http\Controllers\Api\GuestLinkController::class, 'resolveToken']);
Route::get('/public/guest-menu/{identifier}', [\App\Http\Controllers\Api\GuestLinkController::class, 'resolveToken']);
Route::get('/public/guest-orders', [\App\Http\Controllers\Api\GuestLinkController::class, 'getGuestOrderHistory']);
Route::post('/public/orders', [\App\Http\Controllers\Api\GuestLinkController::class, 'placeGuestOrder'])->middleware('throttle:3,1');

// ── Cambodia location (public — no auth needed) ──────────────────────────────
Route::prefix('kh-location')->group(function () {
    Route::get('/provinces',                         [\App\Http\Controllers\Api\KhLocationController::class, 'provinces']);
    Route::get('/provinces/{code}/districts',        [\App\Http\Controllers\Api\KhLocationController::class, 'districts']);
    Route::get('/districts/{code}/communes',         [\App\Http\Controllers\Api\KhLocationController::class, 'communes']);
    Route::get('/communes/{code}/villages',          [\App\Http\Controllers\Api\KhLocationController::class, 'villages']);
    Route::get('/search',                            [\App\Http\Controllers\Api\KhLocationController::class, 'search']);
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/api/user.php';
    require __DIR__.'/api/currencies.php';
    require __DIR__.'/api/exchange_rates.php';
    require __DIR__.'/api/shops.php';
    require __DIR__.'/api/guest_links.php';
    require __DIR__.'/api/categories.php';
    require __DIR__.'/api/products.php';
    require __DIR__.'/api/prices.php';
    require __DIR__.'/api/stocks.php';
    require __DIR__.'/api/orders.php';
    require __DIR__.'/api/order_items.php';
    require __DIR__.'/api/transactions.php';
    require __DIR__.'/api/profits.php';
    require __DIR__.'/api/notifications.php';
    require __DIR__.'/api/role_permission.php';
    
});
