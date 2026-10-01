@extends('layouts.app')
@section('title', 'Asignación académica')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/asignacion.css') }}">
@endpush

@section('content')
<div class="asignacion-page">
    <nav class="crumb" aria-label="Ruta">
        <a href="{{ url('/dashboard') }}">Inicio</a> ›
        <a href="{{ route('estudiantes.index') }}">Estudiantes</a> › <b>Asignación académica</b>
    </nav>

    <div class="head">
        <div>
            <h1>Asignar materia, grupo y docente</h1>
            <p>Selecciona la materia y su grupo para asignar el docente correspondiente.</p>
        </div>
    </div>

    <section class="card asignacion-card" aria-label="Formulario de asignación">
        <div class="m-head">
            <span class="ic"><svg class="i" aria-hidden="true"><use href="#i-students"/></svg></span>
            <div>
                <b>{{ $estudiante->nombre_completo }}</b>
                <small>Código universitario: {{ $estudiante->codigo_universitario }}</small>
            </div>
        </div>

        <div id="asignacion-aviso" class="aviso" role="status" aria-live="polite"></div>

        <form id="asignacion-form" method="post" novalidate>
            @csrf
            <div class="asignacion-campos">
                <div class="field">
                    <label for="materia">Materia</label>
                    <select id="materia" name="materia_id" required disabled aria-describedby="materia-help">
                        <option value="">Selecciona una materia</option>
                    </select>
                    <p class="help" id="materia-help">Cargando materias…</p>
                </div>

                <div class="row2">
                    <div class="field">
                        <label for="grupo">Grupo</label>
                        <select id="grupo" name="grupo_id" required disabled aria-describedby="grupo-help">
                            <option value="">Selecciona un grupo</option>
                        </select>
                        <p class="help" id="grupo-help">Elige primero una materia.</p>
                    </div>

                    <div class="field">
                        <label for="docente">Docente</label>
                        <select id="docente" name="docente_id" required disabled aria-describedby="docente-help">
                            <option value="">Selecciona un docente</option>
                        </select>
                        <p class="help" id="docente-help">Elige primero un grupo.</p>
                    </div>
                </div>
            </div>

            <div class="m-foot">
                <a class="btn ghost" href="{{ route('estudiantes.index') }}">Cancelar</a>
                <button class="btn" type="submit" disabled>Guardar asignación</button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
    window.asignacionFuente = (function (catalogo) {
        const filtrar = (lista, campo, id) => lista.filter(item => String(item[campo]) === String(id));
        return {
            materias: async () => catalogo.materias,
            grupos: async (materiaId) => filtrar(catalogo.grupos, 'materia_id', materiaId),
            docentes: async (grupoId) => filtrar(catalogo.docentes, 'grupo_id', grupoId),
            guardar: async () => {
                throw new Error('El guardado estará disponible al integrar la API de asignación.');
            },
        };
    })(@json($catalogo));
</script>
<script src="{{ asset('js/asignacion.js') }}"></script>
@endpush
