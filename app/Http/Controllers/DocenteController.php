<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Support\Grados;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    /** Letras, espacios, acentos y ñ (sin números ni símbolos). */
    private const REGEX_ALFABETICO = '/^[\pL\s]+$/u';

    /** Carnet de identidad: solo dígitos, entre 5 y 10. */
    private const REGEX_CARNET = '/^[0-9]{5,10}$/';

    public function index()
    {
        $docentes = Docente::withCount('grupos')->orderBy('nombre')->get();

        return view('docentes.index', ['docentes' => $docentes, 'grados' => Grados::OPCIONES]);
    }

    public function store(Request $request)
    {
        Docente::create($this->validar($request));

        return back()->with('success', 'Docente registrado correctamente.');
    }

    public function update(Request $request, Docente $docente)
    {
        $docente->update($this->validar($request, $docente));

        return back()->with('success', 'Docente actualizado correctamente.');
    }

    public function destroy(Docente $docente)
    {
        $docente->delete();
        return back()->with('success', 'Docente eliminado.');
    }

    private function validar(Request $request, ?Docente $actual = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'regex:'.self::REGEX_ALFABETICO],
            'apellido' => ['required', 'string', 'max:80', 'regex:'.self::REGEX_ALFABETICO],
            'grado' => ['required', Rule::in(array_keys(Grados::OPCIONES))],
            'ci' => [
                'required', 'regex:'.self::REGEX_CARNET,
                Rule::unique('docentes', 'ci')->ignore($actual?->id),
            ],
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras.',
            'apellido.regex' => 'El apellido solo puede contener letras.',
            'ci.regex' => 'El carnet debe ser numérico, entre 5 y 10 dígitos.',
        ]);
    }
}
