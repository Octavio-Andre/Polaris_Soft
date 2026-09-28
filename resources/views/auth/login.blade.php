<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · SCIEM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sciem.css') }}">
</head>
<body>
@include('partials.icons')

<div class="login-wrap">
    <form class="login" method="POST" action="{{ url('/login') }}" novalidate>
        @csrf

        <div class="lg">
            <span class="logo">S</span>
            <div><b>SCIEM</b><small>Control de Exámenes</small></div>
        </div>

        <h1>Sistema de Control de Exámenes</h1>
        <p class="lead">Acceso al sistema</p>

        <div class="notice">
            <svg class="i sm"><use href="#i-info"/></svg>
            <span>Ingrese con su cuenta institucional. Por seguridad, el acceso está restringido a personal autorizado.</span>
        </div>

        <div class="field">
            <label for="email">Usuario / correo institucional</label>
            <div class="icon-in">
                <svg class="i"><use href="#i-mail"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       placeholder="usuario@universidad.edu" autocomplete="username" required autofocus>
            </div>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <div class="icon-in">
                <svg class="i"><use href="#i-lock"/></svg>
                <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                <button type="button" class="toggle" aria-label="Mostrar contraseña"
                        onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password'">
                    <svg class="i sm"><use href="#i-eye"/></svg>
                </button>
            </div>
        </div>

        <div class="opts">
            <label><input type="checkbox" name="remember" value="1"> Recordarme</label>
            <a href="#">¿Olvidó su contraseña?</a>
        </div>

        <button class="btn block" type="submit">
            <svg class="i sm"><use href="#i-logout"/></svg> Iniciar sesión
        </button>

        @if ($errors->any())
            <div class="alert" role="alert">
                <svg class="i"><use href="#i-alert"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif
    </form>

    <p class="login-foot">
        Proyecto ISW · Gestión 2026<br>
        SCIEM — Sistema de Control de Exámenes Masivos
    </p>
</div>
</body>
</html>
