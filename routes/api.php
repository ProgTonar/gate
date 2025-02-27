<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AuthController;

// PermissionController
Route::post('/create-role', [PermissionController::class, 'createRole'])->middleware('auth:api', 'permission:admin');
Route::post('/delete-role', [PermissionController::class, 'deleteRole'])->middleware('auth:api', 'permission:admin');
Route::post('/sync-role', [PermissionController::class, 'syncRole'])->middleware('auth:api', 'permission:admin');
Route::post('/create-permission', [PermissionController::class, 'createPermission'])->middleware('auth:api', 'permission:admin');
Route::post('/delete-permission', [PermissionController::class, 'deletePermission'])->middleware('auth:api', 'permission:admin');
Route::post('/sync-permission', [PermissionController::class, 'syncPermission'])->middleware('auth:api', 'permission:admin');
