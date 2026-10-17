<?php

namespace App\Http\Controllers;

use App\Mail\QrEstudianteMail;
use App\Models\Estudiante;
use App\Models\Examen;
use App\Models\Habilitacion;
use App\Services\QrEstudianteService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * RQ7: código QR de cada estudiante.
 *  - ver():            el administrador ve / imprime el QR de un estudiante.
 *  - publico():        página que abre el estudiante desde el enlace firmado del correo (sin login).
 *  - enviarHabilitados(): envía por correo el enlace a su QR a todos los habilitados de un examen.
 */
class EstudianteQrController extends Controller
{
    public function ver(Estudiante $estudiante, QrEstudianteService $qr)
    {
        return view('estudiantes.qr', [
            'estudiante' => $estudiante,
            'token' => $qr->generarToken($estudiante),
            'publico' => false,
        ]);
    }

    /** Ruta con firma (middleware "signed"): solo funciona con el enlace que llegó por correo. */
    public function publico(Estudiante $estudiante, QrEstudianteService $qr)
    {
        return view('estudiantes.qr', [
            'estudiante' => $estudiante,
            'token' => $qr->generarToken($estudiante),
            'publico' => true,
        ]);
    }

    public function enviarHabilitados(Examen $examen)
    {
        $estudiantes = Estudiante::whereIn(
            'id_estudiante',
            Habilitacion::where('examen_id', $examen->id)->where('estado', 'habilitado')->pluck('estudiante_id')
        )->get();

        if ($estudiantes->isEmpty()) {
            return back()->with('error', 'Todavía no hay estudiantes habilitados para enviarles su QR.');
        }

        $enviados = 0;
        $fallidos = 0;

        foreach ($estudiantes as $estudiante) {
            if (! $estudiante->correo_institucional) {
                $fallidos++;
                continue;
            }

            // El enlace vale 30 días: alcanza para el examen sin dejar el QR abierto para siempre.
            $enlace = URL::temporarySignedRoute('qr.publico', now()->addDays(30), ['estudiante' => $estudiante->getKey()]);

            try {
                Mail::to($estudiante->correo_institucional)->send(new QrEstudianteMail($estudiante, $examen, $enlace));
                $enviados++;
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar el QR a '.$estudiante->correo_institucional.': '.$e->getMessage());
                $fallidos++;
            }
        }

        $mensaje = "Se envió el QR a {$enviados} estudiante(s) habilitado(s).";

        return $fallidos
            ? back()->with('error', $mensaje." No se pudo enviar a {$fallidos} (sin correo o error de envío; revisa MAIL_* en el .env).")
            : back()->with('status', $mensaje);
    }
}
