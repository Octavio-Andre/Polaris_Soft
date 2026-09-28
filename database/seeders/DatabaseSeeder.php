<?php

namespace Database\Seeders;

use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Examen;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos de ejemplo para desarrollo (php artisan migrate:fresh --seed).
     * Contraseña de todos los usuarios de prueba: password123
     */
    public function run(): void
    {
        // ---- Usuarios (uno por rol + un inactivo) ----
        foreach ([
            ['Lic. Carlos Choque', 'carlos.choque@sciem.edu', 'administrador', 'activo'],
            ['Dra. María Ramos', 'maria.ramos@sciem.edu', 'docente', 'activo'],
            ['Ing. Jorge Torrez', 'jorge.torrez@sciem.edu', 'control', 'activo'],
            ['Lic. Andrea Paredes', 'andrea.paredes@sciem.edu', 'docente', 'inactivo'],
        ] as [$name, $email, $rol, $estado]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name, 'rol' => $rol, 'estado' => $estado, 'password' => 'password123',
            ]);
        }

        // ---- Docentes ----
        $docentes = collect([
            ['Dra. María Ramos', '4521367'], ['Ing. Luis Fernández', '5123890'], ['Lic. Sonia Vargas', '6034512'],
            ['Ing. Pablo Mendoza', '4876123'], ['Dr. Ricardo Salinas', '5567431'], ['Lic. Andrea Paredes', '6789012'],
        ])->map(fn ($d) => Docente::firstOrCreate(['ci' => $d[1]], ['nombre' => $d[0]]));

        // ---- Materias + un grupo por materia ----
        $materias = collect([
            ['Cálculo Multivariable', 'MAT-201', 'Ingeniería de Sistemas'],
            ['Física General I', 'FIS-101', 'Ingeniería Civil'],
            ['Estructura de Datos', 'INF-210', 'Ingeniería de Sistemas'],
            ['Química Orgánica', 'QUI-220', 'Ingeniería Química'],
            ['Álgebra Lineal', 'MAT-102', 'Ingeniería Industrial'],
            ['Base de Datos I', 'INF-240', 'Ingeniería de Sistemas'],
        ])->map(function ($m, $i) use ($docentes) {
            $materia = Materia::firstOrCreate(['sigla' => $m[1]], ['nombre' => $m[0]]);
            Grupo::firstOrCreate(
                ['materia_id' => $materia->id, 'nombre' => 'Grupo A'],
                ['docente_id' => $docentes[$i % $docentes->count()]->id]
            );
            $materia->carrera_demo = $m[2];

            return $materia;
        });

        // ---- Estudiantes ----
        $carreras = ['Ingeniería de Sistemas', 'Ingeniería Civil', 'Ingeniería Industrial', 'Ingeniería Química', 'Ingeniería Electrónica'];
        $nombres = [
            ['Gabriel Fernando', 'Romero Silva'], ['Salomé', 'Vargas Quispe'], ['Rodrigo', 'Mamani Choque'],
            ['Carolina', 'Flores Ticona'], ['Marco Antonio', 'Ortuño Condori'], ['Valeria', 'Gutiérrez Rojas'],
            ['Diego Alejandro', 'Cussi Nina'], ['Daniela', 'Arce Vásquez'], ['Sebastián', 'Limachi Poma'],
            ['Andrea Paola', 'Choque Mendoza'], ['Luis Fernando', 'Terrazas Peña'], ['Camila', 'Zambrana Rivero'],
        ];
        foreach ($nombres as $i => [$nom, $ape]) {
            $codigo = sprintf('2024-%05d', 34 + $i * 7);
            $est = Estudiante::firstOrCreate(['codigo_universitario' => $codigo], [
                'documento_identidad' => (string) (7100000 + $i * 1379),
                'nombres' => $nom,
                'apellidos' => $ape,
                'carrera' => $carreras[$i % count($carreras)],
                'correo_institucional' => Str::lower(Str::ascii(explode(' ', $nom)[0].'.'.explode(' ', $ape)[0])).'@estudiantes.edu',
                'estado' => $i === 4 ? 'OBSERVADO' : ($i === 9 ? 'INACTIVO' : 'ACTIVO'),
            ]);

            // Cada estudiante queda asignado a 2 materias (alimenta "Capacidad / Asign.")
            foreach ([$i % 6, ($i + 2) % 6] as $k) {
                $materia = $materias[$k];
                Asignacion::firstOrCreate(
                    ['estudiante_id' => $est->getKey(), 'materia_id' => $materia->id],
                    ['grupo_id' => $materia->grupos()->first()->id, 'docente_id' => $materia->grupos()->first()->docente_id]
                );
            }
        }

        // ---- Exámenes (fechas relativas a hoy para que el dashboard siempre tenga próximos) ----
        if (Examen::count() === 0) {
            $normasAdm = 'Presentar CI físico original y Credencial Universitaria vigente 15 minutos antes de la hora señalada.';
            $normasSal = 'Se permite el uso de calculadora científica no programable. Prohibido el uso de celulares.';

            foreach ([
                [0, 5, '08:00', 120, 'Edificio Central 102A', 120, 'abierto'],
                [1, 9, '10:30', 90, 'Auditorio Central', 200, 'programado'],
                [2, 12, '14:00', 90, 'Lab. Cómputo 1 y 2', 60, 'programado'],
                [3, 16, '08:00', 90, 'Pabellón B - Aula 304', 80, 'programado'],
                [4, 20, '09:00', 90, 'Edificio Sur 201', 90, 'programado'],
                [5, -12, '15:00', 90, 'Lab. Cómputo 3', 40, 'finalizado'],
            ] as [$m, $dias, $hora, $dur, $amb, $cap, $estado]) {
                Examen::create([
                    'materia_id' => $materias[$m]->id,
                    'carrera' => $materias[$m]->carrera_demo,
                    'fecha' => now()->addDays($dias)->toDateString(),
                    'hora_inicio' => $hora,
                    'duracion_min' => $dur,
                    'ambiente' => $amb,
                    'capacidad' => $cap,
                    'estado' => $estado,
                    'normas_admision' => $normasAdm,
                    'normas_salida' => $normasSal,
                ]);
            }
        }
    }
}
