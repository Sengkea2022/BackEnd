<?php

use App\Http\Controllers\Api\PriceController;
use Illuminate\Support\Facades\Route;

Route::prefix('prices')->group(function () {
    Route::get('/', [PriceController::class, 'index']);
    Route::post('/', [PriceController::class, 'store']);
    Route::get('/{uuid}', [PriceController::class, 'show']);
    Route::put('/{uuid}', [PriceController::class, 'update']);
    Route::delete('/{uuid}', [PriceController::class, 'destroy']);
});
