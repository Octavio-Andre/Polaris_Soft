<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla `verificacion` (HU-008/009/010): cada vez que el personal de control verifica una identidad
     * queda una fila con el tipo (CODIGO_UNIVERSITARIO | DOCUMENTO_IDENTIDAD | CODIGO_QR), el dato verificado y el resultado.
     * Sin claves foráneas, igual que las demás bitácoras: no debe bloquearse si se borra un examen o estudiante.
     */
    public function up(): void
    {
        Schema::create('verificacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id')->nullable();   // null si el dato no corresponde a nadie
            $table->unsignedBigInteger('examen_id');
            $table->unsignedBigInteger('controlador_id');              // users.id
            $table->string('tipo', 30);                                 // CODIGO_UNIVERSITARIO | DOCUMENTO_IDENTIDAD | CODIGO_QR
            $table->string('dato_verificado', 255);                     // lo que se digitó o escaneó (el QR se guarda recortado)
            $table->string('resultado', 30);                            // HABILITADO | NO_HABILITADO | NO_REGISTRADO | INACTIVO | YA_INGRESO | QR_INVALIDO
            $table->timestamp('fecha_hora')->useCurrent();

            $table->index(['examen_id', 'fecha_hora'], 'verif_examen_fecha_idx');
            $table->index('estudiante_id', 'verif_estudiante_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verificacion');
    }
};
