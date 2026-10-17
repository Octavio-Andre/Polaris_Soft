<!DOCTYPE html>
<html lang="es">
<body style="font-family:Arial,Helvetica,sans-serif;color:#1f2937;background:#f3f4f6;padding:24px">
    <div style="max-width:520px;margin:auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e5e7eb">
        <h2 style="margin:0 0 4px;color:#1e3a8a">SCIEM</h2>
        <p style="margin:0 0 20px;color:#6b7280;font-size:13px">Sistema de Control de Exámenes Masivos</p>

        <p>Hola <b>{{ $usuario->name }}</b>,</p>

        @if ($esCambio)
            <p>El administrador actualizó la contraseña de tu cuenta. Estos son tus datos de acceso:</p>
        @else
            <p>Se creó tu cuenta en el sistema con el rol de <b>{{ $usuario->rol_etiqueta }}</b>. Estos son tus datos de acceso:</p>
        @endif

        <table style="width:100%;border-collapse:collapse;margin:16px 0;font-size:14px">
            <tr><td style="padding:8px;background:#f9fafb;border:1px solid #e5e7eb;width:35%">Usuario (correo)</td>
                <td style="padding:8px;border:1px solid #e5e7eb"><b>{{ $usuario->email }}</b></td></tr>
            <tr><td style="padding:8px;background:#f9fafb;border:1px solid #e5e7eb">Contraseña</td>
                <td style="padding:8px;border:1px solid #e5e7eb"><b style="font-family:monospace;font-size:15px">{{ $contrasena }}</b></td></tr>
        </table>

        <p style="text-align:center;margin:24px 0">
            <a href="{{ route('login') }}" style="background:#1e3a8a;color:#fff;text-decoration:none;padding:10px 22px;border-radius:8px;font-weight:bold">Ingresar al sistema</a>
        </p>

        <p style="font-size:12px;color:#6b7280">Por seguridad, no compartas esta contraseña y cámbiala cuando puedas. Si no esperabas este correo, avisa a un administrador.</p>
    </div>
</body>
</html>
