<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /** Formulario para pedir el enlace de recuperación. */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía el enlace de recuperación al correo.
     * El mensaje es el mismo exista o no la cuenta, para no revelar qué correos están registrados.
     */
    public function sendLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $mensaje = 'Si el correo está registrado y la cuenta está activa, recibirá las instrucciones para restablecer su contraseña.';

        $usuario = User::where('email', $data['email'])->first();

        // Solo se envía a cuentas existentes y activas.
        if (! $usuario || $usuario->estado !== 'activo') {
            return back()->with('status', $mensaje);
        }

        $estado = Password::sendResetLink(['email' => $data['email']]);

        if ($estado === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'Ya se envió un enlace hace poco. Espere un minuto antes de volver a solicitarlo.',
            ])->onlyInput('email');
        }

        return back()->with('status', $mensaje);
    }

    /** Formulario para escribir la nueva contraseña (llega por el enlace del correo). */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /** Guarda la nueva contraseña. */
    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $estado = Password::reset(
            $data,
            function (User $usuario, string $password) {
                $usuario->forceFill([
                    'password' => $password, // el modelo la encripta (cast "hashed")
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            }
        );

        if ($estado === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Contraseña actualizada correctamente. Ya puede iniciar sesión.');
        }

        return back()->withErrors([
            'email' => 'El enlace de recuperación no es válido o ya venció. Solicite uno nuevo.',
        ])->onlyInput('email');
    }
}
