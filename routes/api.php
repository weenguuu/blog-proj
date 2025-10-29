<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// Protected routes
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

// Authentication
Route::post('/user/login', function () {
    return response()->json(['message' => 'Login endpoint']);
});

// Test route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API работает!',
        'data' => ['test' => 'value', 'time' => now()->format('d.m.Y H:i')]
    ]);
});
