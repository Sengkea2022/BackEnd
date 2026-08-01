<?php

use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShopJoinRequestController;
use Illuminate\Support\Facades\Route;

// Shop Join Requests (top-level alias & nested)
Route::get('/shop-requests', [ShopJoinRequestController::class, 'index']);
Route::post('/shop-requests', [ShopJoinRequestController::class, 'store']);
Route::put('/shop-requests/{id}', [ShopJoinRequestController::class, 'update']);
Route::delete('/shop-requests/{id}', [ShopJoinRequestController::class, 'destroy']);

Route::prefix('shops')->group(function () {
    // Shop Join Requests (must be registered BEFORE dynamic /{uuid} parameter route)
    Route::get('/shop-requests', [ShopJoinRequestController::class, 'index']);
    Route::post('/shop-requests', [ShopJoinRequestController::class, 'store']);
    Route::put('/shop-requests/{id}', [ShopJoinRequestController::class, 'update']);
    Route::delete('/shop-requests/{id}', [ShopJoinRequestController::class, 'destroy']);

    Route::get('/', [ShopController::class, 'index']);
    Route::post('/', [ShopController::class, 'store']);
    Route::get('/{uuid}', [ShopController::class, 'show']);
    Route::put('/{uuid}', [ShopController::class, 'update']);
    Route::delete('/{uuid}', [ShopController::class, 'destroy']);
});
