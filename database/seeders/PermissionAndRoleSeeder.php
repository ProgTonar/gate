<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionAndRoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'my_tonar',
            'logistics_management',
            'permission_manipulation'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Создание ролей
        $roles = [
            'admin' => [
                'permissions' => ['my_tonar', 'logistics_management', 'permission_manipulation'],
            ],
            'management' => [
                'permissions' => ['my_tonar', 'logistics_management'],
            ],
            'user' => [
                'permissions' => ['my_tonar'],
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'api']);

            // Назначение разрешений роли
            if (!empty($roleData['permissions'])) {
                $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
                $role->givePermissionTo($permissions);
            }
        }
    }
}
