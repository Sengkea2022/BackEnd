<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

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
    // Route::post('/auth/login', [AuthController::class, 'login']);
    // ... rest of your routes
});

require __DIR__.'/api/auth.php';
require __DIR__.'/api/user.php';
