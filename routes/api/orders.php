<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{uuid}', [OrderController::class, 'show']);
    Route::put('/{uuid}', [OrderController::class, 'update']);
    Route::delete('/{uuid}', [OrderController::class, 'destroy']);
});
