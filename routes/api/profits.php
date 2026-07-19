<?php

use App\Http\Controllers\Api\ProfitController;
use Illuminate\Support\Facades\Route;

Route::prefix('profits')->group(function () {
    Route::get('/', [ProfitController::class, 'index']);
    Route::post('/', [ProfitController::class, 'store']);
    Route::get('/{uuid}', [ProfitController::class, 'show']);
    Route::put('/{uuid}', [ProfitController::class, 'update']);
    Route::delete('/{uuid}', [ProfitController::class, 'destroy']);
});
