<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de estudiantes (RQ1). Si ya existe en tu base (creada antes a mano
     * o por otra rama), no se vuelve a crear.
     */
    public function up(): void
    {
        if (Schema::hasTable('estudiante')) {
            return;
        }

        Schema::create('estudiante', function (Blueprint $table) {
            $table->id('id_estudiante');
            $table->string('codigo_universitario', 30)->unique();
            $table->string('documento_identidad', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('carrera', 120)->nullable();
            $table->string('correo_institucional', 150)->nullable();
            $table->string('estado', 20)->default('ACTIVO'); // ACTIVO | OBSERVADO | INACTIVO
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante');
    }
};
