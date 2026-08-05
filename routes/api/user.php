<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'showCurrent']);
    Route::put('/update', [UserController::class, 'updateCurrent']);
    Route::get('/assignable-personnel', [UserController::class, 'getAssignablePersonnel']);
    Route::get('/shop-owners', [UserController::class, 'getShopOwners']);
    Route::get('/store-departments', [UserController::class, 'getStoreDepartments']);
    Route::get('/shop-departments', [UserController::class, 'getStoreDepartments']);
    Route::get('/store-staff', [UserController::class, 'indexStoreStaff']);
    Route::get('/shop-staff', [UserController::class, 'indexStoreStaff']);
    Route::put('/{uuid}/staff', [UserController::class, 'updateStaff']);
    Route::post('/{uuid}/remove-store', [UserController::class, 'removeStore']);
    Route::post('/{uuid}/remove-shop', [UserController::class, 'removeStore']);
});
