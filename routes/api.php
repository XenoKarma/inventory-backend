<?php

use App\Http\Controllers\API\AuthController;
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
});



// API test route
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin-only', function () {
        return "Only Admin";
    });
});
Route::middleware(['auth:sanctum', 'role:staff'])->group(function () {
    Route::get('/staff-only', function () {
        return "Only Staff";
    });
});
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working 🚀'
    ]);
});
