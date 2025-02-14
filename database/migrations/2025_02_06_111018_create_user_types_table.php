<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('rus_name');
            $table->string('short_rus_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('type')->constrained('user_types')->onDelete('cascade');

        });

        DB::table('user_types')->insert([
            ['name' => 'tonar', 'rus_name' => 'Сотрудник ТОНАР', 'short_rus_name' => 'ТОНАР', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'individual', 'rus_name' => 'Физическое лицо', 'short_rus_name' => 'Физ. лицо', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'legal', 'rus_name' => 'Юридическое лицо', 'short_rus_name' => 'Юр. лицо', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'st', 'rus_name' => 'Индивидуальный предприниматель', 'short_rus_name' => 'ИП', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['type']);
            $table->dropColumn('type');
        });
        Schema::dropIfExists('user_types');
    }
};
