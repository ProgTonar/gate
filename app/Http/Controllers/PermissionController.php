<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    // создание ролей
    public function createRole(Request $request)
    {
        try{
            $request->validate([
                'role_name' => 'required|string|min:1|max:255'
            ]);

            $role = Role::where('name', $request->role_name)->first();

            if($role) {
                return response()->json(['message' => 'Такая роль уже существует']);
            }

            Role::create(['name' => $request->role_name]);

            return response()->json(['message' => 'Роль создана']);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->errors()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"createRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"createRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    // Удаление ролей
    public function deleteRole(Request $request)
    {
        try{
            $request->validate([
                'role_name' => 'required|string|min:1|max:255'
            ]);

            $role = Role::where('name', $request->role_name)->first();

            if (!$role) {
                return response()->json(['message' => 'Роль не найдена'], 204);
            }

            $role->delete();

            return response()->json(['message' => 'Роль удалена'], 200);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->errors()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"deleteRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"deleteRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    // Присвоение ролей
    public function syncRole(Request $request)
    {
        try{
            $request->validate([
                'user_id' => 'require|integer|min:1|max:255',
                'role_name' => 'require|string|min:1|max:255'
            ]);

            $user = User::find($request->user_id);

            if(!$user){
                return response()->json(['message' => 'Пользователь не найден'], 204);
            }

            $role = Role::where('name', $request->role_name);

            if(!$role){
                return response()->json(['message' => 'Роль не найдена'], 204);
            }

            $user->syncnRole($request->role_name);

            return response()->json(['message' => 'Роль присвоена'], 200);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->errors()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"syncRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"syncRole\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function createPermission(Request $request)
    {
        try{
            $request->validate([
                'permission_name' => 'required|string|min:1|max:255',
            ]);

            Permission::create(['name' => $request->permission_name]);

            return response()->json(['message' => 'Разрешение создано'], 200);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->errors()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"createPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"createPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function deletePermission(Request $request)
    {
        try{
            $request->validate([
                'permission_name' => 'required|string|min:1|max:255',
            ]);

            $permission = Permission::where(['name' => $request->permission_name])->first();

            if(!$permission){
                return response()->json(['message' => 'Разрешение удалено'], 204);
            }

            $permission->delete();

            return response()->json(['message' => 'Разрешение удалено'], 200);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->errors()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"deletePermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"deletePermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function syncPermission(Request $request)
    {
        try{
            $request->validate([
                'role_name' => 'required|string|min:1|max:255',
                'permission_name' => 'required|array',
                'permission_name.*' => 'string|min:1|max:255',
            ]);

            $role = Role::where('name', $request->role_name)->first();

            if(!$role){
                return response()->json(['message' => 'роль не найдена'], 204);
            }

            $permissions = Permission::pluck('name')->toArray();

            $checkPermissions = array_diff($request->permission_name, $permissions);

            if(!empty($checkPermissions)){
                return response()->json(['message' => 'Некоторые разрешения не найдены'], 204);
            }

            $role->syncPermissions($request->permission_name);

            return response()->json(['message' => 'Разрешение присвоено роли'], 200);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->errors()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"PermissionController\" функция \"syncPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"PermissionController\" функция \"syncPermission\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }
}
