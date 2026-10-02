<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Grados;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /** Letras, espacios, acentos y ñ (sin números ni símbolos). */
    private const REGEX_ALFABETICO = '/^[\pL\s]+$/u';

    /**
     * Dominios de correo personales/gratuitos no permitidos: el correo debe ser institucional.
     * Ajusta esta lista o cámbiala por un dominio fijo (p. ej. terminar en "@sciem.edu")
     * según lo que defina tu universidad.
     */
    private const DOMINIOS_NO_INSTITUCIONALES = [
        'gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com',
        'live.com', 'icloud.com', 'protonmail.com', 'msn.com',
    ];

    public function index(Request $request)
    {
        $usuarios = User::query()
            ->when($request->filled('buscar'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('name', 'like', "%{$request->buscar}%")
                  ->orWhere('email', 'like', "%{$request->buscar}%");
            }))
            ->orderBy('name')
            ->get();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => User::ROLES,
            'grados' => Grados::OPCIONES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['name'] = trim($data['nombre'].' '.$data['apellido']);

        User::create($data);

        return redirect()->route('usuarios.index')->with('status', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $data = $this->validar($request, $usuario);
        $data['name'] = trim($data['nombre'].' '.$data['apellido']);

        if (empty($data['password'])) {
            unset($data['password']); // no cambiar la contraseña si se deja vacía
        }

        if ($usuario->is($request->user()) && ($data['estado'] !== 'activo' || $data['rol'] !== 'administrador')) {
            return back()->withInput()->withErrors([
                'estado' => 'No puedes desactivar ni quitarte el rol de administrador a ti mismo.',
            ]);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('status', 'Usuario actualizado correctamente.');
    }

    /** Desactivar / reactivar cuenta. */
    public function cambiarEstado(Request $request, User $usuario)
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['estado' => $usuario->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('status', $usuario->estado === 'activo' ? 'Usuario reactivado.' : 'Usuario desactivado.');
    }

    private function validar(Request $request, ?User $actual = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'regex:'.self::REGEX_ALFABETICO],
            'apellido' => ['required', 'string', 'max:80', 'regex:'.self::REGEX_ALFABETICO],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($actual?->id),
                function ($attribute, $value, $fail) {
                    $dominio = strtolower((string) substr(strrchr($value, '@'), 1));
                    if (in_array($dominio, self::DOMINIOS_NO_INSTITUCIONALES, true)) {
                        $fail('Debe usar un correo institucional, no una cuenta personal como Gmail, Hotmail, etc.');
                    }
                },
            ],
            'password' => [$actual ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', Rule::in(array_keys(User::ROLES))],
            'grado' => ['required', Rule::in(array_keys(Grados::OPCIONES))],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras.',
            'apellido.regex' => 'El apellido solo puede contener letras.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);
    }
}
