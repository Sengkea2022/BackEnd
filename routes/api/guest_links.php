<?php

use App\Http\Controllers\Api\GuestLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('guest_links')->group(function () {
    Route::get('/', [GuestLinkController::class, 'index']);
    Route::post('/', [GuestLinkController::class, 'store']);
    Route::get('/{uuid}', [GuestLinkController::class, 'show']);
    Route::put('/{uuid}', [GuestLinkController::class, 'update']);
    Route::delete('/{uuid}', [GuestLinkController::class, 'destroy']);
});
