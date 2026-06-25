<?php

use App\Http\Controllers\Api\AuthController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    require __DIR__.'/api/user.php';
    require __DIR__.'/api/currencies.php';
    require __DIR__.'/api/exchange_rates.php';
    require __DIR__.'/api/stores.php';
    require __DIR__.'/api/guest_links.php';
    require __DIR__.'/api/categories.php';
    require __DIR__.'/api/products.php';
    require __DIR__.'/api/prices.php';
    require __DIR__.'/api/stocks.php';
    require __DIR__.'/api/customers.php';
    require __DIR__.'/api/orders.php';
    require __DIR__.'/api/order_items.php';
    require __DIR__.'/api/transactions.php';
    require __DIR__.'/api/profits.php';
    require __DIR__.'/api/notifications.php';
});
