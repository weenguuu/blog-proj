<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// без авторизации
Route::get('/post', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'show']);
// авторизация
Route::post('/user/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/post', [PostController::class, 'store']);
    Route::put('/post/{id}', [PostController::class, 'update']);
    Route::delete('/post/{id}', [PostController::class, 'destroy']);
});






