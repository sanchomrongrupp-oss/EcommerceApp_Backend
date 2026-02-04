<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupProductController;
use App\Http\Controllers\ProductController;

// Public routes
Route::post('/register/users', [AuthController::class, 'register']);
Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes for all authenticated users
Route::middleware('auth.jwt')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Group Products Read Access
    Route::get('/group_products', [GroupProductController::class, 'index']);
    Route::get('/group_products/{id}', [GroupProductController::class, 'show']);

    // Products Read Access
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

// Admin-only routes for GroupProduct Management
Route::middleware(['auth.jwt', 'admin'])->prefix('admin')->group(function () {
    // Group Products Management (CUD)
    Route::post('/group_products', [GroupProductController::class, 'store']);
    Route::put('/group_products/{id}', [GroupProductController::class, 'update']);
    Route::patch('/group_products/{id}', [GroupProductController::class, 'update']);
    Route::delete('/group_products/{id}', [GroupProductController::class, 'destroy']);
    
    // Products Management (CUD)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::patch('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
    // Legacy endpoint (backward compatibility)
    Route::get('/addgrouppd', [GroupProductController::class, 'addgrouppd']);
});
