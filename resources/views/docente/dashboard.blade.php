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
            <small>Este módulo estará disponible en los próximos sprints.</small></div>
    </div>
@endsection
