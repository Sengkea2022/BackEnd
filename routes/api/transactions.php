<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/{uuid}', [TransactionController::class, 'show']);
    Route::put('/{uuid}', [TransactionController::class, 'update']);
    Route::delete('/{uuid}', [TransactionController::class, 'destroy']);
});
