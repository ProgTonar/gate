<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

// Обработка OPTIONS запросов для CORS
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

// PermissionController
Route::post('/create-role', [PermissionController::class, 'createRole'])->middleware('auth:api', 'permission:admin');
Route::post('/delete-role', [PermissionController::class, 'deleteRole'])->middleware('auth:api', 'permission:admin');
Route::post('/sync-role', [PermissionController::class, 'syncRole'])->middleware('auth:api', 'permission:admin');
Route::post('/create-permission', [PermissionController::class, 'createPermission'])->middleware('auth:api', 'permission:admin');
Route::post('/delete-permission', [PermissionController::class, 'deletePermission'])->middleware('auth:api', 'permission:admin');
Route::post('/sync-permission', [PermissionController::class, 'syncPermission'])->middleware('auth:api', 'permission:admin');

Route::post('register', [AuthController::class, 'register']);
Route::post('/api-login', [AuthController::class, 'login']);
Route::post('/registration', [AuthController::class, 'registration']);
Route::get('/redirect', [AuthController::class, 'redirect']);

Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    // Объединенный маршрут для получения ролей и разрешений
    Route::post('/user/roles-and-permissions', [PermissionController::class, 'getUserRolesAndPermissions']);

    // Получение всех ролей и разрешений (для админов)
    Route::get('/roles', [PermissionController::class, 'getAllRoles'])->middleware('permission:admin');
    Route::get('/permissions', [PermissionController::class, 'getAllPermissions'])->middleware('permission:admin');

    //Получение авторизованного пользователя
    Route::get('/auth_user', [PermissionController::class,'getAuthUser']);

    // Получение списка всех пользователей (только для админов)
    Route::get('/users', [PermissionController::class, 'getAllUsers'])->middleware('permission:admin');
    // Маршрут для получения данных пользователя по ID
    Route::get('/users/detail/{id}', [PermissionController::class, 'getUserById'])->middleware('permission:admin');
    Route::post('/update_user/{id}', [UserController::class, 'handleUserUpdate'])->middleware('permission:admin');

    //Отправка новостей в бэкэнд
    Route::post('/news_add', [NewsController::class, 'proxyWithFiles'])->middleware('permission:admin');

    //Смена пароля
    Route::post('/change-password', [UserController::class, 'changePassword']);

});

// Маршрут для получения данных пользователя
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


