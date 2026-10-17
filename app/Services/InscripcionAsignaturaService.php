<?php

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Materia;
use Illuminate\Support\Facades\DB;

/**
 * Mantiene al día las tablas `asignatura` y `estudiante_asignatura`, de las que sale el listado de carreras
 * y asignaturas del formulario de Exámenes (bug 2-1 del QA: "Carrera" salía vacío porque nada las llenaba).
 *
 * Regla: cada Materia es una asignatura (código = sigla) y cada Asignacion (estudiante + materia) es una inscripción.
 */
class InscripcionAsignaturaService
{
    /** Inscribe al estudiante en la asignatura de la materia (la crea si no existe). Se puede repetir sin duplicar. */
    public function inscribir(int $estudianteId, Materia|int $materia): void
    {
        $materia = $materia instanceof Materia ? $materia : Materia::find($materia);
        if (! $materia) {
            return;
        }

        $idAsignatura = $this->asignaturaDe($materia);

        $yaExiste = DB::table('estudiante_asignatura')
            ->where('id_estudiante', $estudianteId)
            ->where('id_asignatura', $idAsignatura)
            ->exists();

        if (! $yaExiste) {
            DB::table('estudiante_asignatura')->insert([
                'id_estudiante' => $estudianteId,
                'id_asignatura' => $idAsignatura,
                'gestion' => (string) now()->year,
                'estado' => 'ACTIVO',
            ]);
        }
    }

    /** Quita la inscripción (al eliminar la asignación del estudiante a esa materia). */
    public function desinscribir(int $estudianteId, ?Materia $materia): void
    {
        if (! $materia) {
            return;
        }

        $asignatura = DB::table('asignatura')->where('codigo', $materia->sigla)->first();
        if (! $asignatura) {
            return;
        }

        DB::table('estudiante_asignatura')
            ->where('id_estudiante', $estudianteId)
            ->where('id_asignatura', $asignatura->id_asignatura)
            ->delete();
    }

    /** Pone al día todo lo que ya existe (para bases con asignaciones viejas). Seguro de repetir. */
    public function sincronizarTodo(): void
    {
        foreach (Asignacion::with('materia')->get() as $asignacion) {
            if ($asignacion->materia) {
                $this->inscribir((int) $asignacion->estudiante_id, $asignacion->materia);
            }
        }
    }

    private function asignaturaDe(Materia $materia): int
    {
        $existente = DB::table('asignatura')->where('codigo', $materia->sigla)->first();

        return $existente
            ? (int) $existente->id_asignatura
            : (int) DB::table('asignatura')->insertGetId(
                ['codigo' => $materia->sigla, 'nombre' => $materia->nombre],
                'id_asignatura'
            );
    }
}
