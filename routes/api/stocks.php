<?php

use App\Http\Controllers\Api\StockController;
use Illuminate\Support\Facades\Route;

Route::prefix('stocks')->group(function () {
    Route::get('/', [StockController::class, 'index']);
    Route::post('/', [StockController::class, 'store']);
    Route::get('/{uuid}', [StockController::class, 'show']);
    Route::put('/{uuid}', [StockController::class, 'update']);
    Route::delete('/{uuid}', [StockController::class, 'destroy']);
});
