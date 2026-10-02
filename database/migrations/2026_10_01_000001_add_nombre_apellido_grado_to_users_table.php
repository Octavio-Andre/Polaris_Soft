<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Divide el nombre completo en nombre + apellido, y agrega el grado académico. */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre', 80)->nullable()->after('name');
            $table->string('apellido', 80)->nullable()->after('nombre');
            $table->string('grado', 40)->nullable()->after('apellido');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'apellido', 'grado']);
        });
    }
};
