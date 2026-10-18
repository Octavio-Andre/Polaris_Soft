<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un estudiante no puede registrar ingreso dos veces al mismo examen (RQ11).
     * Además del chequeo en el controlador, la base lo garantiza aunque lleguen dos clics a la vez.
     * Si ya hubiera duplicados viejos, el índice no se crea (la validación del controlador sigue activa).
     */
    public function up(): void
    {
        try {
            Schema::table('transparencia_ingreso', function (Blueprint $table) {
                $table->unique(['estudiante_id', 'examen_id'], 'transp_estudiante_examen_unique');
            });
        } catch (\Throwable $e) {
            // duplicados previos: se omite
        }
    }

    public function down(): void
    {
        try {
            Schema::table('transparencia_ingreso', function (Blueprint $table) {
                $table->dropUnique('transp_estudiante_examen_unique');
            });
        } catch (\Throwable $e) {
        }
    }
};
