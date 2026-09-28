<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamenController extends Controller
{
    public function index(Request $request)
    {
        $examenes = Examen::with(['materia' => fn ($q) => $q->withCount('asignaciones')])
            ->buscar($request->string('buscar')->toString())
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('ambiente'), fn ($q) => $q->where('ambiente', $request->ambiente))
            ->orderBy('fecha')->orderBy('hora_inicio')
            ->paginate(5)
            ->withQueryString();

        $stats = [
            'programados' => Examen::where('estado', '<>', 'finalizado')->count(),
            'ambientes' => Examen::where('estado', '<>', 'finalizado')->distinct('ambiente')->count('ambiente'),
        ];

        $materias = Materia::orderBy('nombre')->get();
        $ambientes = Examen::distinct()->orderBy('ambiente')->pluck('ambiente');

        return view('examenes.index', compact('examenes', 'stats', 'materias', 'ambientes'));
    }

    public function store(Request $request)
    {
        Examen::create($this->validar($request));

        return redirect()->route('examenes.index')->with('status', 'Examen registrado correctamente.');
    }

    public function update(Request $request, Examen $examen)
    {
        $examen->update($this->validar($request));

        return redirect()->route('examenes.index')->with('status', 'Examen actualizado correctamente.');
    }

    public function destroy(Examen $examen)
    {
        $examen->delete();

        return redirect()->route('examenes.index')->with('status', 'Examen eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'materia_id' => ['required', 'exists:materias,id'],
            'carrera' => ['nullable', 'string', 'max:120'],
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'duracion_min' => ['required', 'integer', 'min:15', 'max:480'],
            'ambiente' => ['required', 'string', 'max:100'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:5000'],
            'estado' => ['required', Rule::in(array_keys(Examen::ESTADOS))],
            'normas_admision' => ['nullable', 'string', 'max:1000'],
            'normas_salida' => ['nullable', 'string', 'max:1000'],
        ], [
            'materia_id.required' => 'Selecciona una asignatura.',
            'hora_inicio.date_format' => 'La hora debe tener el formato HH:MM.',
        ]);
    }
}
