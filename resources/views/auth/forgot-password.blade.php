<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña · SCIEM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sciem.css') }}">
</head>
<body>
@include('partials.icons')

<div class="login-wrap">
    <form class="login" method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="lg">
            <span class="logo">S</span>
            <div><b>SCIEM</b><small>Control de Exámenes</small></div>
        </div>

        <h1>Recuperar contraseña</h1>
        <p class="lead">Le enviaremos un enlace para restablecerla</p>

        @if (session('status'))
            <div class="status" role="status">
                <svg class="i sm"><use href="#i-check"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div class="notice">
            <svg class="i sm"><use href="#i-info"/></svg>
            <span>Ingrese el correo institucional de su cuenta. El enlace es válido por 60 minutos.</span>
        </div>

        <div class="field">
            <label for="email">Correo institucional</label>
            <div class="icon-in">
                <svg class="i"><use href="#i-mail"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       placeholder="usuario@universidad.edu" autocomplete="username" required autofocus>
            </div>
            @error('email')
                <div class="err">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn block" type="submit">
            <svg class="i sm"><use href="#i-mail"/></svg> Enviar enlace de recuperación
        </button>

        <a class="back" href="{{ route('login') }}">Volver al inicio de sesión</a>
    </form>

    <p class="login-foot">
        Proyecto ISW · Gestión 2026<br>
        SCIEM — Sistema de Control de Exámenes Masivos
    </p>
</div>
</body>
</html>
