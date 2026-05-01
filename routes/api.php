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
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()->load('roles', 'permissions'),
                'roles' => $request->user()->getRoleNames(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            ]
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Welcome Admin!',
                'data' => $request->user()->load('roles', 'permissions'),
            ]);
        });

        Route::get('/admin/users', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => \App\Models\User::with('roles', 'permissions')->get(),
            ]);
        });
    });

    // Staff only routes
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Welcome Staff!',
                'data' => $request->user()->load('roles', 'permissions'),
            ]);
        });
    });

    // Permission-based routes
    Route::middleware('permission:manage users')->group(function () {
        Route::get('/manage-users', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'You can manage users',
            ]);
        });
    });

    // Shared routes (both admin & staff)
    Route::middleware('permission:view inventory')->group(function () {
        Route::get('/inventory', function (Request $request) {
            return response()->json([
                'success' => true,
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

    // Stock history is a custom endpoint
    Route::get('products/{product}/stock-history', [StockMovementController::class, 'stockHistory']);
});

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working '
    ]);
});
