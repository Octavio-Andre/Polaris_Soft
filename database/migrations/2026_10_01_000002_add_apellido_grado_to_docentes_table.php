<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A partir de ahora 'nombre' guarda solo el nombre de pila del docente;
     * 'apellido' y 'grado' son nuevos.
     */
    public function up(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('apellido', 80)->nullable()->after('nombre');
            $table->string('grado', 40)->nullable()->after('apellido');
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn(['apellido', 'grado']);
        });
    }
};
