<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'showCurrent']);
    Route::put('/update', [UserController::class, 'updateCurrent']);
    Route::get('/assignable-personnel', [UserController::class, 'getAssignablePersonnel']);
    Route::get('/store-owners', [UserController::class, 'getStoreOwners']);
    Route::get('/store-staff', [UserController::class, 'indexStoreStaff']);
    Route::post('/{uuid}/remove-store', [UserController::class, 'removeStore']);
});
