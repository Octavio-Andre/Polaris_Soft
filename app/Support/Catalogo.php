<?php

namespace App\Support;

/** Catálogo de facultades y sus carreras, usado en Estudiantes y la importación masiva. */
class Catalogo
{
    public const FACULTADES = [
        'Ingeniería' => [
            'Ingeniería de Sistemas', 'Ingeniería Civil', 'Ingeniería Industrial',
            'Ingeniería Electrónica', 'Ingeniería Química',
        ],
        'Ciencias Económicas y Financieras' => [
            'Administración de Empresas', 'Contaduría Pública', 'Economía',
        ],
        'Ciencias de la Salud' => [
            'Medicina', 'Enfermería', 'Odontología',
        ],
        'Ciencias Sociales y Humanidades' => [
            'Derecho', 'Psicología', 'Comunicación Social',
        ],
    ];

    /** Carreras que pertenecen a una facultad (vacío si la facultad no existe). */
    public static function carrerasDe(?string $facultad): array
    {
        return self::FACULTADES[$facultad] ?? [];
    }

    /** A qué facultad pertenece una carrera (null si no está en el catálogo). */
    public static function facultadDeCarrera(?string $carrera): ?string
    {
        foreach (self::FACULTADES as $facultad => $carreras) {
            if (in_array($carrera, $carreras, true)) {
                return $facultad;
            }
        }

        return null;
    }
}
