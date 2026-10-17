@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <nav class="crumb" aria-label="Ruta"><a href="{{ url('/dashboard') }}">Inicio</a> › <b>Dashboard</b></nav>
    <div class="head">
        <div>
            <h1>Panel del docente</h1>
            <p>Consulta de tus materias, grupos y estudiantes asignados.</p>
        </div>
    </div>
    <div class="banner">
        <span class="ic"><svg class="i"><use href="#i-info"/></svg></span>
        <div><b>Bienvenido, {{ auth()->user()->name }}</b>
            <small>Desde aquí puedes habilitar o inhabilitar estudiantes para cada examen.</small></div>
    </div>
    <p style="margin-top:16px"><a class="btn" href="{{ route('docente.examenes') }}">Ir a habilitar estudiantes</a></p>
@endsection
