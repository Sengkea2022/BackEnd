<?php

use App\Http\Controllers\Api\OrderItemController;
use Illuminate\Support\Facades\Route;

Route::prefix('order_items')->group(function () {
    Route::get('/', [OrderItemController::class, 'index']);
    Route::post('/', [OrderItemController::class, 'store']);
    Route::get('/{uuid}', [OrderItemController::class, 'show']);
    Route::put('/{uuid}', [OrderItemController::class, 'update']);
    Route::delete('/{uuid}', [OrderItemController::class, 'destroy']);
});
