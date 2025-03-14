<?php

use App\Http\Controllers\Auth\CustomPKCE;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;


Route::get('/', [PageController::class, 'loginPage'])->name('index');
Route::post('/login', [AuthController::class, 'login'])->name('authLogin');
Route::get('/redirect', [AuthController::class, 'redirect'])->name('redirect');

Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
Route::get('/registration', [PageController::class, 'registrationPage'])->name('registrationPage');
