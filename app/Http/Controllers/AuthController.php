<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     * La vista (resources/views/auth/login.blade.php) la crea Carlos.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el intento de login.
     * Espera del formulario: email, password, remember (checkbox opcional).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Solo pueden ingresar cuentas activas
        if (Auth::attempt($credentials + ['estado' => 'activo'], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return match (Auth::user()->rol) {
                'administrador' => redirect()->intended('/admin/dashboard'),
                'docente' => redirect()->intended('/docente/dashboard'),
                default => redirect()->intended('/control/dashboard'),
            };
        }

        return back()->withErrors([
            'email' => 'Usuario o contraseña incorrectos, o la cuenta está inactiva.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario autenticado.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
