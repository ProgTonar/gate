<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateTypUser extends Seeder
{
    public function run(): void
    {
        $types = [
            [
               'name' => 'tonar',
               'rus_name' => 'Сотрудник ТОНАР',
               'short_rus_name' => 'Тонар'
            ],
            [
               'name' => 'individual',
               'rus_name' => 'Физическое лицо',
               'short_rus_name' => 'Физ. лицо'
            ],
            [
                'name' => 'legal',
                'rus_name' => 'Юридическое лицо',
                'short_rus_name' => 'Юр. лицо'
            ],
            [
                'name' => 'st',
                'rus_name' => 'Индивидуальный предприниматель',
                'short_rus_name' => 'ИП'
            ]
        ];

        foreach ($types as $type) {
            UserType::create($type);
        }
    }
}
