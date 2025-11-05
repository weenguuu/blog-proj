<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Страница регистрации
Route::get('/register', function () {
    return view('register');
})->name('register');
