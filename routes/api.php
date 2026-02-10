<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantsControllers;
use App\Http\Controllers\WishlistsControllers;
use App\Http\Controllers\CartsControllers;
use App\Http\Controllers\CartItemControllers;
use App\Http\Controllers\OrdersControllers;
use App\Http\Controllers\OrderItemsControllers;

// Public routes
// Route::post('/register/user', [AuthController::class, 'registerUser']);
Route::post('/register/users', [AuthController::class, 'registerUser']); // Alias for plural
Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes for all authenticated users
Route::middleware('auth.jwt')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Categories Read Access
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Products Read Access
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Product Variants Read Access
    Route::get('/variants', [ProductVariantsControllers::class, 'index']);
    Route::get('/variants/{id}', [ProductVariantsControllers::class, 'show']);
    Route::get('/products/{productId}/variants', [ProductVariantsControllers::class, 'getProductVariants']);

    // User Resources (Full CRUD for authenticated users)
    Route::apiResource('wishlists', WishlistsControllers::class);
    Route::apiResource('carts', CartsControllers::class);
    Route::apiResource('cart-items', CartItemControllers::class);
    Route::apiResource('orders', OrdersControllers::class);
    Route::apiResource('order-items', OrderItemsControllers::class);
});

// Admin-only routes for Management
Route::middleware(['auth.jwt', 'admin'])->prefix('admin')->group(function () {
    // Categories Management (CUD)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::patch('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    
    // Products Management (CUD)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::patch('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Product Variants Management (CUD)
    Route::post('/variants', [ProductVariantsControllers::class, 'store']);
    Route::put('/variants/{id}', [ProductVariantsControllers::class, 'update']);
    Route::patch('/variants/{id}', [ProductVariantsControllers::class, 'update']);
    Route::delete('/variants/{id}', [ProductVariantsControllers::class, 'destroy']);

    // Orders Management (Admins might need to view/manage all orders) - Optional but good practice
    // Route::apiResource('orders', OrdersControllers::class)->only(['index', 'update', 'destroy']);
});
