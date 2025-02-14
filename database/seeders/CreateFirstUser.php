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
            'last_name' => 'Админский',
            'first_name' => 'Админ',
            'middle_name' => 'Админович',
            'type' => 1,
            'login' => 'admin',
            'email' => 'uchevatkin.a@tonar.info',
            'active' => true,
            'password' => Hash::make('test1234'),
        ]);

        if($user){
            $this->command->info('Пользователь создан');
        } else {
            $this->command->error('Что-то не так с созданием пользователей');
        }

        $role = Role::where('name','admin')->first();

        if($role){
            $this->command->info('Роль найдена');
        } else {
            $this->command->error('Что-то не так с поиском роли');
        }

        $user->assignRole('admin');
    }
}
