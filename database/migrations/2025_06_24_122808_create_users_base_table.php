<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users_base', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('codigo_liberacao', 6);
            $table->timestamp('codigo_criado_em');
            $table->string('expo_push_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_base');
    }
};
