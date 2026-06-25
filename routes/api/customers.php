<?php

use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::get('/{uuid}', [CustomerController::class, 'show']);
    Route::put('/{uuid}', [CustomerController::class, 'update']);
    Route::delete('/{uuid}', [CustomerController::class, 'destroy']);
});
