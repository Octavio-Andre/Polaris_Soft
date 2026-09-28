<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Estado de la cuenta: 'activo' | 'inactivo' (un usuario inactivo no puede iniciar sesión). */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('estado')->default('activo')->after('rol');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
