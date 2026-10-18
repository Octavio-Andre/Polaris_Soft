<?php

namespace App\Support;

/** Utilidades para dejar los textos con formato uniforme antes de guardarlos. */
class Texto
{
    /** Palabras que en un nombre se escriben en minúscula salvo que sean la primera (de la Cruz, Pérez del Castillo…). */
    private const PARTICULAS = ['de', 'del', 'la', 'las', 'los', 'y', 'e', 'da', 'do', 'dos', 'das', 'van', 'von', 'di', 'el'];

    /**
     * "gUeVaRa  pérez" -> "Guevara Pérez"; "MARÍA DE LA CRUZ" -> "María de la Cruz".
     * También quita espacios sobrantes al inicio, al final y entre palabras.
     */
    public static function nombrePropio(?string $texto): string
    {
        $texto = trim((string) preg_replace('/\s+/u', ' ', (string) $texto));
        if ($texto === '') {
            return '';
        }

        $palabras = explode(' ', mb_strtolower($texto, 'UTF-8'));

        foreach ($palabras as $i => $palabra) {
            if ($i > 0 && in_array($palabra, self::PARTICULAS, true)) {
                continue;
            }
            $palabras[$i] = mb_convert_case($palabra, MB_CASE_TITLE, 'UTF-8');
        }

        return implode(' ', $palabras);
    }
}
