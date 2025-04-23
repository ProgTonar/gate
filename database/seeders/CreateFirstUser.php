<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateFirstUser extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'last_name' => 'Артем',
            'first_name' => 'Артем',
            'middle_name' => 'Артем',
            'login' => 'admin',
            'email' => 'uchevatkin.a@tonar.info',
            'active' => true,
            'password' => Hash::make('test1234'),
            'user_type_id' => 2
        ]);

        $role = Role::where('name', 'admin')->first();

        $user->assignRole($role);
    }
}
