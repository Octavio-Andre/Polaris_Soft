<?php

use App\Services\InscripcionAsignaturaService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bug 2-1 del QA: el desplegable "Carrera" de Registrar examen salía vacío.
     * Las carreras salen de las inscripciones (estudiante_asignatura) y en bases existentes nadie las había creado.
     * Esta migración las genera a partir de las asignaciones que ya existen (no borra nada y no duplica).
     */
    public function up(): void
    {
        foreach (['asignaciones', 'materias', 'asignatura', 'estudiante_asignatura', 'estudiante'] as $tabla) {
            if (! Schema::hasTable($tabla)) {
                return;
            }
        }

        app(InscripcionAsignaturaService::class)->sincronizarTodo();
    }

    public function down(): void
    {
        // Datos derivados: no se deshacen.
    }
};
