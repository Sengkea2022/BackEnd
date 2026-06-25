<?php

use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::post('/', [StoreController::class, 'store']);
    Route::get('/{uuid}', [StoreController::class, 'show']);
    Route::put('/{uuid}', [StoreController::class, 'update']);
    Route::delete('/{uuid}', [StoreController::class, 'destroy']);
});
