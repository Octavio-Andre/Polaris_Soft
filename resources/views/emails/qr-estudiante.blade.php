<!DOCTYPE html>
<html lang="es">
<body style="font-family:Arial,Helvetica,sans-serif;color:#1f2937;background:#f3f4f6;padding:24px">
    <div style="max-width:520px;margin:auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e5e7eb">
        <h2 style="margin:0 0 4px;color:#1e3a8a">SCIEM</h2>
        <p style="margin:0 0 20px;color:#6b7280;font-size:13px">Sistema de Control de Exámenes Masivos</p>

        <p>Hola <b>{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</b>,</p>
        <p>Estás <b>habilitado</b> para rendir:</p>

        <p style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;font-size:14px">
            <b>{{ $examen->asignatura->nombre ?? $examen->materia->nombre ?? 'Examen' }}</b><br>
            {{ $examen->fecha->format('d/m/Y') }} · {{ $examen->hora }} · {{ $examen->ambiente }}
        </p>

        <p>Abre este enlace desde tu celular y muestra el código QR al personal de control en la puerta:</p>

        <p style="text-align:center;margin:24px 0">
            <a href="{{ $enlace }}" style="background:#1e3a8a;color:#fff;text-decoration:none;padding:10px 22px;border-radius:8px;font-weight:bold">Ver mi código QR</a>
        </p>

        <p style="font-size:12px;color:#6b7280">Lleva también tu carnet de identidad. El código QR es personal: no lo compartas.</p>
    </div>
</body>
</html>
