<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'showCurrent']);
    Route::put('/update', [UserController::class, 'updateCurrent']);
});
