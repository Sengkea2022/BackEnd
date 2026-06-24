<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Backend API is connected.',
        'status' => 'ok test',
    ]);
});

// ✅ Handle CORS preflight requests
Route::options('/{any}', function () {
    return response('')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('any', '.*');

// ✅ All other routes here
Route::middleware('api')->group(function () {
    Route::middleware('auth:sanctum')->get('/auth/me', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });
});

require __DIR__.'/api/auth.php';
require __DIR__.'/api/user.php';
