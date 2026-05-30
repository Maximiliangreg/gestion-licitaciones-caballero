<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\Users\UserController;

// SPA principal
Route::get('/', [SpaController::class, 'index'])->name('spa.index');

// Login (pantalla)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Crear usuario (pantalla)
Route::get('/users/create', function () {
    return view('users.create');
})->name('users.create');


