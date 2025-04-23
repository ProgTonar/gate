<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{
    // создание ролей
    public function createRole(Request $request)
    {
        try {
            $request->validate([
                'role_name' => 'required|string|min:1|max:255'
            ]);

            $role = Role::where('name', $request->role_name)->first();

            if ($role) {
                return response()->json(['message' => 'Такая роль уже существует']);
            }

            Role::create(['name' => $request->role_name]);

            return response()->json(['message' => 'Роль создана']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"createRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"createRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    // Удаление ролей
    public function deleteRole(Request $request)
    {
        try {
            $request->validate([
                'role_name' => 'required|string|min:1|max:255'
            ]);

            $role = Role::where('name', $request->role_name)->first();

            if (!$role) {
                return response()->json(['message' => 'Роль не найдена'], 204);
            }

            $role->delete();

            return response()->json(['message' => 'Роль удалена'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"deleteRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"deleteRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    // Присвоение ролей
    public function syncRole(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'require|integer|min:1|max:255',
                'role_name' => 'require|string|min:1|max:255'
            ]);

            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json(['message' => 'Пользователь не найден'], 204);
            }

            $role = Role::where('name', $request->role_name);

            if (!$role) {
                return response()->json(['message' => 'Роль не найдена'], 204);
            }

            $user->syncnRole($request->role_name);

            return response()->json(['message' => 'Роль присвоена'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"syncRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"syncRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }
    /**
     * Создание разрешений
     */
    public function createPermission(Request $request)
    {
        try {
            $request->validate([
                'permission_name' => 'required|string|min:1|max:255',
            ]);

            Permission::create(['name' => $request->permission_name]);

            return response()->json(['message' => 'Разрешение создано'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"createPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"createPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function deletePermission(Request $request)
    {
        try {
            $request->validate([
                'permission_name' => 'required|string|min:1|max:255',
            ]);

            $permission = Permission::where(['name' => $request->permission_name])->first();

            if (!$permission) {
                return response()->json(['message' => 'Разрешение удалено'], 204);
            }

            $permission->delete();

            return response()->json(['message' => 'Разрешение удалено'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"deletePermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"deletePermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function syncPermission(Request $request)
    {
        try {
            $request->validate([
                'role_name' => 'required|string|min:1|max:255',
                'permission_name' => 'required|array',
                'permission_name.*' => 'string|min:1|max:255',
            ]);

            $role = Role::where('name', $request->role_name)->first();

            if (!$role) {
                return response()->json(['message' => 'роль не найдена'], 204);
            }

            $permissions = Permission::pluck('name')->toArray();

            $checkPermissions = array_diff($request->permission_name, $permissions);

            if (!empty($checkPermissions)) {
                return response()->json(['message' => 'Некоторые разрешения не найдены'], 204);
            }

            $role->syncPermissions($request->permission_name);

            return response()->json(['message' => 'Разрешение присвоено роли'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"syncPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"syncPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    /**
     * Получение ролей и разрешений текущего пользователя
     */
    public function getUserRolesAndPermissions(Request $request)
    {
        try {
            $user = $request->user();
            Log::info('Пользователь:', ['user' => $user ? $user->toArray() : null]);

            if (!$user) {
                return response()->json(['message' => 'Пользователь не авторизован'], 401);
            }

            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');

            Log::info('Роли и разрешения пользователя:', [
                'roles' => $roles,
                'permissions' => $permissions
            ]);

            return response()->json([
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"getUserRolesAndPermissions\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    /**
     * Проверка наличия роли у текущего пользователя
     */
    public function checkUserRole(Request $request)
    {
        try {
            $request->validate([
                'role' => 'required|string|min:1|max:255',
            ]);

            $user = $request->user();
            $hasRole = $user->hasRole($request->role);

            return response()->json([
                'has_role' => $hasRole
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"checkUserRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    /**
     * Проверка наличия разрешения у текущего пользователя
     */
    public function checkUserPermission(Request $request)
    {
        try {
            $request->validate([
                'permission' => 'required|string|min:1|max:255',
            ]);

            $user = $request->user();
            $hasPermission = $user->can($request->permission);

            return response()->json([
                'has_permission' => $hasPermission
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"checkUserPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    /**
     * Получение всех ролей (для админов)
     */
    public function getAllRoles()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json(['roles' => $roles]);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"getAllRoles\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    /**
     * Получение всех разрешений (для админов)
     */
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::all();
            return response()->json(['permissions' => $permissions]);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"getAllPermissions\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    /**
     * Получение списка всех пользователей с их ролями и разрешениями
     */
    public function getAllUsers()
    {
        try {
            $users = User::with(['roles', 'permissions', 'userType'])->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'last_name' => $user->last_name,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'email' => $user->email,
                    'login' => $user->login,
                    'phone' => $user->phone,
                    'photo' => $user->photo,
                    'active' => $user->active,
                    'user_type_id'=>$user->user_type_id,
                    'user_type' => $user->userType ? $user->userType->rus_name : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name')
                ];
            });

            return response()->json([
                'users' => $users
            ]);
        } catch (Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"getAllUsers\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function getUserById(Request $request, $id)
    {
       try {
           $user = User::with(['roles', 'permissions', 'userType'])
            ->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 404);
            }

            $responseData = [
                'id' => $user->id,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'email' => $user->email,
                'login' => $user->login,
                'phone' => $user->phone,
                'active' => $user->active,
                'user_type_id' => $user->user_type_id,
                'user_type' => $user->userType ? $user->userType->rus_name : null,
                'photo' => $user->photo,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ];

            return response()->json([
                'data' => $responseData
            ]);
        } catch (Exception $e) {
            Log::error('Ошибка при получении пользователя: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении данных пользователя'
            ], 500);
        }
    }


    public function getAuthUser (Request $request) {
        try {
            $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'data'=> $user
        ]);
        } catch (Exception $e) {
            Log::error('Ошибка при получении пользователя: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении данных пользователя'
            ], 500);
        }
    }


}
