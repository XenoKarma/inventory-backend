<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'permissions');
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function (Request $request) {
            return response()->json([
                'message' => 'Welcome Admin!',
                'user' => $request->user()->load('roles', 'permissions'),
            ]);
        });

        Route::get('/admin/users', function (Request $request) {
            return response()->json([
                'message' => 'User management - Admin only',
                'users' => \App\Models\User::with('roles')->get(),
            ]);
        });
    });

    // Staff only routes
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', function (Request $request) {
            return response()->json([
                'message' => 'Welcome Staff!',
                'user' => $request->user()->load('roles', 'permissions'),
            ]);
        });
    });

    // Permission-based routes
    Route::middleware('permission:manage users')->group(function () {
        Route::get('/manage-users', function (Request $request) {
            return response()->json([
                'message' => 'You can manage users',
            ]);
        });
    });

    // Shared routes (both admin & staff)
    Route::middleware('permission:view inventory')->group(function () {
        Route::get('/inventory', function (Request $request) {
            return response()->json([
                'message' => 'View inventory - accessible by both admin & staff',
            ]);
        });
    });

    // Resource routes
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('product-stocks', ProductStockController::class)->only(['index', 'show', 'update']);
    Route::apiResource('stock-movements', StockMovementController::class)->except(['update', 'destroy']);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working '
    ]);
});
