<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('s_t_s', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('inn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_t_s');
    }
};
