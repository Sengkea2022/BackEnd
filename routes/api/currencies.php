<?php

use App\Http\Controllers\Api\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index']);
    Route::post('/', [CurrencyController::class, 'store']);
    Route::get('/{uuid}', [CurrencyController::class, 'show']);
    Route::put('/{uuid}', [CurrencyController::class, 'update']);
    Route::delete('/{uuid}', [CurrencyController::class, 'destroy']);
});
