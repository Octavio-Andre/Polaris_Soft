<?php

namespace App\Services;

use App\Models\Estudiante;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Generación y validación de los tokens de los códigos QR de los estudiantes (RQ7).
 *
 * El QR NO contiene el código universitario en claro: contiene un token cifrado con la APP_KEY
 * (AES-256 + firma de Laravel). Si alguien lo altera o lo inventa, no se puede descifrar y se rechaza.
 * El mismo estudiante siempre recibe un token equivalente en significado (el cifrado cambia, el contenido no).
 */
class QrEstudianteService
{
    /** Prefijo para distinguir nuestros QR de cualquier otro código que se escanee por error. */
    private const PREFIJO = 'SCIEM1.';

    public function generarToken(Estudiante $estudiante): string
    {
        return self::PREFIJO.Crypt::encryptString(json_encode([
            'id' => $estudiante->getKey(),
            'cod' => $estudiante->codigo_universitario,
        ]));
    }

    /**
     * Devuelve el estudiante dueño del token, o null si el token es inválido, fue alterado
     * o ya no coincide con los datos del estudiante.
     */
    public function validarToken(string $token): ?Estudiante
    {
        $token = trim($token);

        if (! str_starts_with($token, self::PREFIJO)) {
            return null;
        }

        try {
            $datos = json_decode(Crypt::decryptString(substr($token, strlen(self::PREFIJO))), true);
        } catch (DecryptException $e) {
            return null;
        }

        if (! is_array($datos) || empty($datos['id']) || empty($datos['cod'])) {
            return null;
        }

        $estudiante = Estudiante::find($datos['id']);

        // Si el código del estudiante cambió después de emitir el QR, el QR queda caducado.
        if (! $estudiante || (string) $estudiante->codigo_universitario !== (string) $datos['cod']) {
            return null;
        }

        return $estudiante;
    }
}
