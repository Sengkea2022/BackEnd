<?php

use App\Http\Controllers\Api\ExchangeRateController;
use Illuminate\Support\Facades\Route;

$registerExchangeRateRoutes = function () {
    Route::get('/', [ExchangeRateController::class, 'index']);
    Route::post('/', [ExchangeRateController::class, 'store']);
    Route::get('/{uuid}', [ExchangeRateController::class, 'show']);
    Route::put('/{uuid}', [ExchangeRateController::class, 'update']);
    Route::delete('/{uuid}', [ExchangeRateController::class, 'destroy']);
};

Route::prefix('exchange_rates')->group($registerExchangeRateRoutes);
Route::prefix('exchange-rates')->group($registerExchangeRateRoutes);
