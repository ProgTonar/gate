<?php

use App\Http\Controllers\Auth\CustomPKCE;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;


Route::get('/', [PageController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('authLogin');
Route::get('/oauth/redirect', [AuthController::class, 'redirect'])->name('redirect');
Route::get('/oauth/callback', [AuthController::class, 'callback'])->name('callback');

// Переназначение стандартной функции для pcke авторизации
// Route::get('/oauth/authorize', [CustomPKCE::class, 'authorize'])->middleware(['web', 'auth']);

// Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
Route::get('/registration', [PageController::class, 'registrationPage'])->name('registrationPage');
