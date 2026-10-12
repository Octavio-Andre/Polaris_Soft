<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Examen;
use App\Models\Habilitacion;
use App\Models\TransparenciaIngreso;
use App\Services\TransparenciaIngresoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VerificacionController extends Controller
{
    public function index()
    {
        $examenes = Examen::with('materia')
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return view('verificacion.index', compact('examenes'));
    }

    public function verificar(Request $request): JsonResponse
    {
        $metodo = strtoupper(trim((string) ($request->input('metodo_verificacion') ?: 'CODIGO_UNIV')));

        $reglas = [
            'id_examen' => ['required', 'integer', 'exists:examenes,id'],
            'metodo_verificacion' => ['required', Rule::in(['CODIGO_UNIV', 'CI'])],
        ];

        if ($metodo === 'CODIGO_UNIV') {
            $reglas['codigo_universitario'] = ['required', 'digits:9'];
        } else {
            $reglas['documento_identidad'] = ['required', 'digits_between:5,10'];
            $reglas['ci_complemento'] = ['nullable', 'max:2'];
        }

        $data = $request->validate($reglas, [
            'id_examen.required' => 'Selecciona un examen antes de verificar.',
            'id_examen.exists' => 'El examen seleccionado no existe.',
            'metodo_verificacion.required' => 'Selecciona un método de verificación.',
            'codigo_universitario.required' => 'Ingresa el código universitario.',
            'codigo_universitario.digits' => 'El código universitario debe tener 9 dígitos.',
            'documento_identidad.required' => 'Ingresa la cédula de identidad.',
            'documento_identidad.digits_between' => 'La cédula debe tener entre 5 y 10 dígitos.',
        ]);

        $examen = Examen::findOrFail($data['id_examen']);

        if ($metodo === 'CODIGO_UNIV') {
            $codigo = trim((string) $data['codigo_universitario']);
            $estudiante = Estudiante::where('codigo_universitario', $codigo)->first();

            if (! $estudiante) {
                return response()->json([
                    'tipo' => 'codigo_no_registrado',
                    'mensaje' => 'No existe un estudiante registrado con ese código universitario.',
                ], 404);
            }
        } else {
            $documento = trim((string) $data['documento_identidad']);
            $complemento = strtoupper(trim((string) ($data['ci_complemento'] ?? '')));

            $query = Estudiante::where('documento_identidad', $documento);
            if ($complemento === '') {
                $query->where(function ($q) {
                    $q->whereNull('ci_complemento')->orWhere('ci_complemento', '');
                });
            } else {
                $query->where('ci_complemento', $complemento);
            }

            $estudiante = $query->first();

            if (! $estudiante) {
                return response()->json([
                    'tipo' => 'codigo_no_registrado',
                    'mensaje' => 'No existe un estudiante registrado con esa cédula de identidad.',
                ], 404);
            }
        }

        if (strtoupper((string) $estudiante->estado) !== 'ACTIVO') {
            return response()->json([
                'tipo' => 'inactivo',
                'mensaje' => 'El estudiante no se encuentra activo para rendir el examen.',
                'estudiante' => $this->resumenEstudiante($estudiante),
            ], 422);
        }

        $ingresoPrevio = TransparenciaIngreso::where('estudiante_id', $estudiante->id_estudiante)
            ->where('examen_id', $examen->id)
            ->latest('fecha_hora_ingreso')
            ->first();

        if ($ingresoPrevio) {
            return response()->json([
                'tipo' => 'ya_ingreso',
                'mensaje' => 'El estudiante ya registró ingreso para este examen.',
                'fecha_hora' => $ingresoPrevio->fecha_hora_ingreso,
                'estudiante' => $this->resumenEstudiante($estudiante),
            ], 409);
        }

        $habilitacion = Habilitacion::where('estudiante_id', $estudiante->id_estudiante)
            ->where('examen_id', $examen->id)
            ->first();

        if (! $habilitacion) {
            return response()->json([
                'tipo' => 'no_habilitado',
                'mensaje' => 'El estudiante no tiene habilitación registrada para este examen.',
                'estudiante' => $this->resumenEstudiante($estudiante),
            ], 422);
        }

        if ($habilitacion->estado !== 'habilitado') {
            return response()->json([
                'tipo' => 'no_habilitado',
                'mensaje' => $habilitacion->motivo?->descripcion ?? 'El estudiante está inhabilitado para este examen.',
                'motivo_inhabilitacion' => $habilitacion->motivo?->descripcion,
                'estudiante' => $this->resumenEstudiante($estudiante),
            ], 422);
        }

        return response()->json([
            'tipo' => 'habilitado',
            'mensaje' => 'Estudiante habilitado para este examen.',
            'estudiante' => $this->resumenEstudiante($estudiante),
            'codigo' => $estudiante->codigo_universitario,
            'id_habilitacion' => $habilitacion->id,
        ], 200);
    }

    public function ingreso(Request $request, TransparenciaIngresoService $servicio): JsonResponse
    {
        $data = $request->validate([
            'id_estudiante' => ['required_without:estudiante_id', 'integer', 'exists:estudiante,id_estudiante'],
            'estudiante_id' => ['required_without:id_estudiante', 'integer', 'exists:estudiante,id_estudiante'],
            'id_examen' => ['required_without:examen_id', 'integer', 'exists:examenes,id'],
            'examen_id' => ['required_without:id_examen', 'integer', 'exists:examenes,id'],
            'codigo_universitario' => ['nullable', 'digits:9'],
            'metodo' => ['nullable', Rule::in(['CODIGO_UNIV', 'CI', 'QR'])],
        ], [
            'id_estudiante.required_without' => 'Falta el identificador del estudiante.',
            'id_examen.required_without' => 'Falta el identificador del examen.',
            'codigo_universitario.digits' => 'El código universitario debe tener 9 dígitos.',
        ]);

        $estudianteId = (int) ($data['id_estudiante'] ?? $data['estudiante_id']);
        $examenId = (int) ($data['id_examen'] ?? $data['examen_id']);
        $estudiante = Estudiante::findOrFail($estudianteId);
        $examen = Examen::findOrFail($examenId);

        if ($request->filled('codigo_universitario')
            && trim((string) $request->codigo_universitario) !== trim((string) $estudiante->codigo_universitario)) {
            return response()->json([
                'mensaje' => 'El código universitario no coincide con el estudiante.',
            ], 422);
        }

        $habilitacion = Habilitacion::where('estudiante_id', $estudianteId)
            ->where('examen_id', $examenId)
            ->first();

        if (! $habilitacion || $habilitacion->estado !== 'habilitado') {
            $detalle = $habilitacion?->motivo?->descripcion;

            return response()->json([
                'mensaje' => $habilitacion
                    ? 'El estudiante está inhabilitado para este examen'.($detalle ? ': '.$detalle.'.' : '.')
                    : 'El estudiante no tiene habilitación registrada para este examen.',
            ], 422);
        }

        if (TransparenciaIngreso::where('estudiante_id', $estudianteId)
            ->where('examen_id', $examenId)->exists()) {
            return response()->json([
                'mensaje' => 'El estudiante ya registró ingreso para este examen.',
            ], 409);
        }

        $registro = $servicio->registrar(
            $estudiante,
            $examen,
            $request->user(),
            $data['metodo'] ?? 'CODIGO_UNIV',
            $request
        );

        return response()->json([
            'mensaje' => 'Ingreso registrado correctamente.',
            'registro' => [
                'id' => $registro->id,
                'fecha_hora' => $registro->fecha_hora_ingreso,
                'estudiante' => $estudiante->nombre_completo,
                'examen' => $registro->examen_descripcion,
            ],
        ], 201);
    }

    private function resumenEstudiante(Estudiante $estudiante): array
    {
        return [
            'id' => $estudiante->id_estudiante,
            'nombres' => $estudiante->nombres,
            'apellidos' => $estudiante->apellidos,
            'codigo' => $estudiante->codigo_universitario,
            'carnet' => $estudiante->carnet_completo,
            'carrera' => $estudiante->carrera,
            'documento_identidad' => $estudiante->documento_identidad,
            'ci_complemento' => $estudiante->ci_complemento,
            'estado' => $estudiante->estado,
        ];
    }
}
