<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users_base', function (Blueprint $table) {
            $table->timestamp('ultimo_codigo_liberacao')->nullable()->after('codigo_criado_em');
        });
    }

    public function down(): void
    {
        Schema::table('users_base', function (Blueprint $table) {
            $table->dropColumn('ultimo_codigo_liberacao');
        });
    }
};

