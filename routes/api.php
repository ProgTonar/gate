<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// AuthController
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// PermissionController
Route::post('/create-role', [PermissionController::class, 'createRole'])->middleware('auth:api', 'permission:permission_manipulation');
Route::post('/delete-role', [PermissionController::class, 'deleteRole'])->middleware('auth:api', 'permission:permission_manipulation');
Route::post('/sync-role', [PermissionController::class, 'syncRole'])->middleware('auth:api', 'permission:permission_manipulation');
Route::post('/create-permission', [PermissionController::class, 'createPermission'])->middleware('auth:api', 'permission:permission_manipulation');
Route::post('/delete-permission', [PermissionController::class, 'deletePermission'])->middleware('auth:api', 'permission:permission_manipulation');
Route::post('/sync-permission', [PermissionController::class, 'syncPermission'])->middleware('auth:api', 'permission:permission_manipulation');
