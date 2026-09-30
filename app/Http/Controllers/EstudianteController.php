<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    /** Carreras sugeridas en el formulario (se suman las que ya existan en la base). */
    private const CARRERAS = [
        'Ingeniería de Sistemas', 'Ingeniería Civil', 'Ingeniería Industrial',
        'Ingeniería Electrónica', 'Ingeniería Química',
    ];

    public function index(Request $request)
    {
        $estudiantes = Estudiante::query()
            ->buscar($request->string('buscar')->toString())
            ->when($request->filled('carrera'), fn ($q) => $q->where('carrera', $request->carrera))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', strtoupper($request->estado)))
            ->orderBy('apellidos')
            ->paginate(5)
            ->withQueryString();

        $stats = [
            'total' => Estudiante::count(),
            'habilitados' => Estudiante::where('estado', 'ACTIVO')->count(),
            'inactivos' => Estudiante::where('estado', '<>', 'ACTIVO')->count(),
        ];

        $carreras = collect(self::CARRERAS)
            ->merge(Estudiante::whereNotNull('carrera')->distinct()->pluck('carrera'))
            ->unique()->sort()->values();

        return view('estudiantes.index', compact('estudiantes', 'stats', 'carreras'));
    }

    public function store(Request $request)
    {
        Estudiante::create($this->validar($request));

        return redirect()->route('estudiantes.index')->with('status', 'Estudiante registrado correctamente.');
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $estudiante->update($this->validar($request, $estudiante));

        return redirect()->route('estudiantes.index')->with('status', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();

        return redirect()->route('estudiantes.index')->with('status', 'Estudiante eliminado.');
    }

    private function validar(Request $request, ?Estudiante $actual = null): array
    {
        $id = $actual?->getKey();

        $data = $request->validate([
            'codigo_universitario' => ['required', 'string', 'max:30',
                Rule::unique('estudiante', 'codigo_universitario')->ignore($id, 'id_estudiante')],
            'documento_identidad' => ['required', 'string', 'max:20',
                Rule::unique('estudiante', 'documento_identidad')->ignore($id, 'id_estudiante')],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'carrera' => ['required', 'string', 'max:120'],
            'correo_institucional' => ['nullable', 'email', 'max:150'],
            'estado' => ['required', Rule::in(['ACTIVO', 'OBSERVADO', 'INACTIVO'])],
        ], [
            'codigo_universitario.unique' => 'Ya existe un estudiante con ese código.',
            'documento_identidad.unique' => 'Ya existe un estudiante con ese CI / DNI.',
        ]);

        $data['estado'] = strtoupper($data['estado']);

        return $data;
    }
}
