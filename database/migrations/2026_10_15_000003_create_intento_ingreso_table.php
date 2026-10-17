<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla `intento_ingreso` (RQ6 / HU-008): intentos fallidos de ingreso (código o CI no registrado,
     * estudiante no habilitado, inactivo, QR inválido…). Base del reporte de RQ12 y RQ19 en sprints siguientes.
     */
    public function up(): void
    {
        Schema::create('intento_ingreso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id')->nullable();
            $table->unsignedBigInteger('examen_id');
            $table->unsignedBigInteger('controlador_id');
            $table->string('tipo', 30);
            $table->string('dato_verificado', 255);
            $table->string('motivo', 255);
            $table->timestamp('fecha_hora')->useCurrent();

            $table->index(['examen_id', 'fecha_hora'], 'intento_examen_fecha_idx');
            $table->index('estudiante_id', 'intento_estudiante_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intento_ingreso');
    }
};
