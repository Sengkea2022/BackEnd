<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{uuid}', [CategoryController::class, 'show']);
    Route::put('/{uuid}', [CategoryController::class, 'update']);
    Route::delete('/{uuid}', [CategoryController::class, 'destroy']);
});
