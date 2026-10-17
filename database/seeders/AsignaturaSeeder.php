<?php

namespace Database\Seeders;

use App\Services\InscripcionAsignaturaService;
use Illuminate\Database\Seeder;

/**
 * Llena las tablas "asignatura" y "estudiante_asignatura" (que usa el formulario de Exámenes)
 * a partir de las materias y asignaciones que ya existen. Se puede ejecutar varias veces.
 *
 *   php artisan db:seed --class=AsignaturaSeeder
 */
class AsignaturaSeeder extends Seeder
{
    public function run(): void
    {
        app(InscripcionAsignaturaService::class)->sincronizarTodo();
    }
}
