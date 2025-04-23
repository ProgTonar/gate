<?php

use App\Http\Controllers\Auth\CustomPKCE;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view(view: 'welcome');
})->name('index');

Route::get('/redirect', [AuthController::class, 'redirect'])->name('redirect');

Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
Route::get('/registration', [PageController::class, 'registrationPage'])->name('registrationPage');

// Страница логина
Route::get('/login', [PageController::class, 'loginPage'])->name('loginPage');

// Обработка формы логина
Route::post('/process-login', [AuthController::class, 'processLogin'])->name('processLogin');

// Обработка OPTIONS запросов для CORS
Route::options('/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
})->where('any', '.*');


//Дашбоард
Route::get('/dashboard', [PageController::class, 'dashboardPage']);
