<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/', [NotificationController::class, 'store']);
    Route::get('/{uuid}', [NotificationController::class, 'show']);
    Route::put('/{uuid}', [NotificationController::class, 'update']);
    Route::delete('/{uuid}', [NotificationController::class, 'destroy']);
});
