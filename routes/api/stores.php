<?php

use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\StoreJoinRequestController;
use Illuminate\Support\Facades\Route;

// Store Join Requests (top-level alias & nested)
Route::get('/store-requests', [StoreJoinRequestController::class, 'index']);
Route::post('/store-requests', [StoreJoinRequestController::class, 'store']);
Route::put('/store-requests/{id}', [StoreJoinRequestController::class, 'update']);
Route::delete('/store-requests/{id}', [StoreJoinRequestController::class, 'destroy']);

Route::prefix('stores')->group(function () {
    // Store Join Requests (must be registered BEFORE dynamic /{uuid} parameter route)
    Route::get('/store-requests', [StoreJoinRequestController::class, 'index']);
    Route::post('/store-requests', [StoreJoinRequestController::class, 'store']);
    Route::put('/store-requests/{id}', [StoreJoinRequestController::class, 'update']);
    Route::delete('/store-requests/{id}', [StoreJoinRequestController::class, 'destroy']);

    Route::get('/', [StoreController::class, 'index']);
    Route::post('/', [StoreController::class, 'store']);
    Route::get('/{uuid}', [StoreController::class, 'show']);
    Route::put('/{uuid}', [StoreController::class, 'update']);
    Route::delete('/{uuid}', [StoreController::class, 'destroy']);
});
