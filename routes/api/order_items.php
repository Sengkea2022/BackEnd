<?php

use App\Http\Controllers\Api\OrderItemController;
use Illuminate\Support\Facades\Route;

$registerOrderItemRoutes = function () {
    Route::get('/', [OrderItemController::class, 'index']);
    Route::post('/', [OrderItemController::class, 'store']);
    Route::get('/{uuid}', [OrderItemController::class, 'show']);
    Route::put('/{uuid}', [OrderItemController::class, 'update']);
    Route::delete('/{uuid}', [OrderItemController::class, 'destroy']);
};

Route::prefix('order_items')->group($registerOrderItemRoutes);
Route::prefix('order-items')->group($registerOrderItemRoutes);
