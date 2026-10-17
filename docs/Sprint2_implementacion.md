# Sprint 2 · Qué se implementó (y cómo probarlo)

| RQ | Estado | Dónde |
|---|---|---|
| RQ3 Habilitar estudiantes | Ya estaba; ahora **también el docente** | `HabilitacionController`, `/examenes/{id}/habilitados`, `/docente/examenes` |
| RQ4 Motivos de inhabilitación | Ya estaba; ahora **también el docente** | mismo módulo |
| RQ5 Verificación por código | Ya estaba; ahora guarda bitácora | `VerificacionController::verificar` |
| RQ6 Verificación por CI | Ya estaba; ahora guarda bitácora e **intentos** | tablas `verificacion` e `intento_ingreso` |
| RQ7 Verificación por QR | **Nuevo** | `QrEstudianteService`, `EstudianteQrController`, pantalla `/verificacion` |
| RQ8 Transparencia | Ya estaba; se agregó índice único (RQ11) | `transparencia_ingreso` |
| RQ28 Despliegue | **Nuevo** `deploy.sh` y `.env.example` | raíz del proyecto |

## Extras pedidos
- **Correo con credenciales:** al crear un usuario (o cambiarle la contraseña) se le envía un correo con su acceso. `app/Mail/CredencialesUsuario.php`.
- **Docente habilita/inhabilita:** menú "Habilitar estudiantes" para el rol docente.
- **Facultad en materias:** columna, formulario (la carrera se filtra según la facultad) y listado.

## QR (RQ7)
- El QR contiene un token **cifrado con APP_KEY**, no el código en claro. Si se altera, se rechaza ("Código QR no válido o alterado").
- Si cambia el código universitario del estudiante, su QR viejo queda caducado.
- **Si se cambia APP_KEY, todos los QR ya enviados dejan de servir.**
- Envío: en *Exámenes → Habilitados* hay un botón "Enviar QR por correo a los habilitados". Cada estudiante recibe un enlace firmado (30 días) a su QR.
- El administrador puede ver/imprimir el QR de cualquiera desde *Estudiantes* (ícono de carnet).
- Escaneo: cámara (celular/tablet/webcam), lector USB (escribe en el campo) o digitando el token. Beeps de éxito/error con Web Audio.
- La cámara del celular exige **HTTPS** (o localhost).

## Pruebas manuales sugeridas
1. `php artisan migrate` (crea `verificacion`, `intento_ingreso`, `facultad` en materias, índice único).
2. Con `MAIL_MAILER=log`, crear un usuario y revisar `storage/logs/laravel.log`: debe estar el correo con la contraseña.
3. Entrar como docente (maria.ramos@sciem.edu / password123) → "Habilitar estudiantes" → habilitar e inhabilitar con motivo.
4. Como administrador: Estudiantes → ícono QR → copiar el contenido y pegarlo en Verificación con método "Código QR".
5. Verificar un código inexistente y un estudiante inhabilitado y mirar la tabla `intento_ingreso`.
6. Registrar ingreso dos veces: la segunda debe mostrar "ya registró ingreso".

## Pendiente / decisiones
- RQ3: la "nota de excepción" al asociar a mano un estudiante que no es de la materia (HU-006) no se implementó.
- El docente ve **todos** los exámenes: no hay vínculo entre la cuenta de usuario y la ficha de `docentes`, por eso no se filtra por "sus" materias.
- RQ28: el despliegue real en WEBTIS depende de acceso al servidor; aquí queda el script y la guía.
