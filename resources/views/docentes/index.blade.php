@extends('layouts.app')
@section('title', 'Docentes')

@section('content')
    <nav class="crumb" aria-label="Ruta"><a href="{{ url('/dashboard') }}">Inicio</a> › <b>Docentes</b></nav>

    <div class="head">
        <div>
            <h1>Gestión de docentes</h1>
            <p>Registrar a los docentes que pueden ser asignados a grupos y estudiantes.</p>
        </div>
        <button type="button" class="btn" id="btn-nuevo"><svg class="i"><use href="#i-user-plus"/></svg> Nuevo docente</button>
    </div>

    @include('partials.flash')

    <section class="card" style="margin-top:20px">
        <div class="scroll">
            <table>
                <thead><tr><th>Nombre completo</th><th>Grado</th><th>CI</th><th>Grupos a cargo</th><th style="text-align:right">Acciones</th></tr></thead>
                <tbody>
                @forelse ($docentes as $d)
                    @php
                        $payload = $d->only(['nombre', 'apellido', 'grado', 'ci']);
                        $url = route('docentes.update', $d);
                    @endphp
                    <tr>
                        <td class="strong">{{ $d->nombre_completo }}</td>
                        <td>{{ $d->grado }}</td>
                        <td class="num">{{ $d->ci }}</td>
                        <td class="num">{{ $d->grupos_count }}</td>
                        <td>
                            <div class="actions">
                                <button type="button" class="icon-btn" title="Editar" aria-label="Editar"
                                        data-edit="{{ json_encode($payload) }}" data-url="{{ $url }}" data-id="{{ $d->id }}">
                                    <svg class="i"><use href="#i-edit"/></svg></button>
                                <form method="POST" action="{{ $url }}" onsubmit="return confirm('¿Eliminar a este docente? Sus grupos quedarán sin docente.')">
                                    @csrf @method('DELETE')
                                    <button class="icon-btn danger" type="submit" title="Eliminar" aria-label="Eliminar"><svg class="i"><use href="#i-trash"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Aún no hay docentes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="foot"><span>{{ $docentes->count() }} docentes registrados</span></div>
    </section>

    <dialog id="modal-docente">
        <form method="POST" action="{{ route('docentes.store') }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" disabled>
            <input type="hidden" name="_form" value="docente">
            <input type="hidden" name="_editing" value="{{ old('_editing') }}">
            <div class="m-head">
                <span class="ic"><svg class="i"><use href="#i-user-plus"/></svg></span>
                <div><b data-title>Registrar docente</b><small>Datos del docente</small></div>
                <button type="button" class="icon-btn x" aria-label="Cerrar" onclick="this.closest('dialog').close()"><svg class="i"><use href="#i-x"/></svg></button>
            </div>
            <div class="m-body">
                <div class="row2">
                    <div class="field">
                        <label for="d-nombre">Nombre <span class="req">*</span></label>
                        <input id="d-nombre" name="nombre" value="{{ old('nombre') }}" placeholder="María" pattern="[\p{L}\s]+" required>
                        @error('nombre')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="d-apellido">Apellido <span class="req">*</span></label>
                        <input id="d-apellido" name="apellido" value="{{ old('apellido') }}" placeholder="Ramos" pattern="[\p{L}\s]+" required>
                        @error('apellido')<p class="err">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="field">
                    <label for="d-grado">Grado académico <span class="req">*</span></label>
                    <select id="d-grado" name="grado" data-default="Licenciado(a)" required>
                        <option value="">Seleccione el grado…</option>
                        @foreach ($grados as $g)
                            <option value="{{ $g }}" @selected(old('grado') === $g)>{{ $g }}</option>
                        @endforeach
                    </select>
                    @error('grado')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label for="d-ci">CI <i>Solo números, 5 a 10 dígitos</i></label>
                    <input id="d-ci" name="ci" value="{{ old('ci') }}" placeholder="4521367" inputmode="numeric" pattern="[0-9]{5,10}" minlength="5" maxlength="10" required>
                    @error('ci')<p class="err">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="m-foot">
                <button type="button" class="btn ghost" onclick="this.closest('dialog').close()">Cancelar</button>
                <button type="submit" class="btn" data-submit><svg class="i sm"><use href="#i-check"/></svg> Guardar docente</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
    const MODAL = 'modal-docente';
    document.getElementById('btn-nuevo').addEventListener('click', () =>
        abrirForm(MODAL, { url: '{{ route('docentes.store') }}', method: 'POST', title: 'Registrar docente' }));
    document.querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () =>
        abrirForm(MODAL, { url: b.dataset.url, method: 'PUT', editing: b.dataset.id, values: JSON.parse(b.dataset.edit), title: 'Editar docente' })));
    @if ($errors->any() && old('_form') === 'docente')
        abrirForm(MODAL, { keep: true,
            url: '{{ old('_editing') ? url('docentes/'.old('_editing')) : route('docentes.store') }}',
            method: '{{ old('_editing') ? 'PUT' : 'POST' }}',
            title: '{{ old('_editing') ? 'Editar docente' : 'Registrar docente' }}' });
    @endif
</script>
@endpush
