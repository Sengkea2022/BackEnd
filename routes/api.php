<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Backend API is connected.',
        'status' => 'ok test',
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
