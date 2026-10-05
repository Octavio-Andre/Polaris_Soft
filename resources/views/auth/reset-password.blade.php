<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva contraseña · SCIEM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sciem.css') }}">
</head>
<body>
@include('partials.icons')

<div class="login-wrap">
    <form class="login" method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="lg">
            <span class="logo">S</span>
            <div><b>SCIEM</b><small>Control de Exámenes</small></div>
        </div>

        <h1>Nueva contraseña</h1>
        <p class="lead">Escriba la nueva contraseña de su cuenta</p>

        <div class="field">
            <label for="email">Correo institucional</label>
            <div class="icon-in">
                <svg class="i"><use href="#i-mail"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}"
                       placeholder="usuario@universidad.edu" autocomplete="username" required>
            </div>
            @error('email')
                <div class="err">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">Nueva contraseña <i>Mínimo 8 caracteres</i></label>
            <div class="icon-in">
                <svg class="i"><use href="#i-lock"/></svg>
                <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="new-password" required autofocus>
                <button type="button" class="toggle" aria-label="Mostrar contraseña"
                        onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password'">
                    <svg class="i sm"><use href="#i-eye"/></svg>
                </button>
            </div>
            @error('password')
                <div class="err">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmar contraseña</label>
            <div class="icon-in">
                <svg class="i"><use href="#i-lock"/></svg>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" autocomplete="new-password" required>
            </div>
        </div>

        <button class="btn block" type="submit">
            <svg class="i sm"><use href="#i-check"/></svg> Guardar contraseña
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
