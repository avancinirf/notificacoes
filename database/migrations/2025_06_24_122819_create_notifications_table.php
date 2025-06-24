<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_base')->nullable();
            $table->string('titulo');
            $table->text('descricao');
            $table->boolean('status')->default(0); // 0 = inativo, 1 = ativo
            $table->integer('lembrar_em')->default(1);
            $table->integer('total_envios')->default(0);
            $table->integer('falha_envios')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
