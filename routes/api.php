<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/register/users', [AuthController::class, 'register']);
Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth.jwt')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
