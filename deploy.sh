#!/usr/bin/env bash
# Script de despliegue de SCIEM en el servidor del Laboratorio TIS (RQ28).
#   Primera vez / actualización:   ./deploy.sh            (entorno de pruebas)
#                                   ./deploy.sh produccion (sin seeders de ejemplo, APP_DEBUG debe ser false)
# Requisitos del servidor: PHP 8.1+, MySQL, Apache o Nginx, Composer y Git.
set -euo pipefail

ENTORNO="${1:-pruebas}"
cd "$(dirname "$0")"

echo "==> Desplegando SCIEM ($ENTORNO)"

if [ ! -f .env ]; then
    echo "No existe .env. Copia .env.example a .env y completa DB_* y MAIL_* antes de desplegar."
    exit 1
fi

if [ "$ENTORNO" = "produccion" ] && grep -q '^APP_DEBUG=true' .env; then
    echo "ERROR: en producción APP_DEBUG debe ser false."
    exit 1
fi

# Si es un repositorio git, se trae la última versión.
if [ -d .git ]; then
    git pull --ff-only
fi

if [ "$ENTORNO" = "produccion" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
else
    composer install --optimize-autoloader --no-interaction
fi

# La llave de la aplicación cifra los QR de los estudiantes: se genera UNA sola vez.
# Si cambia, los QR ya enviados dejan de ser válidos.
if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

php artisan migrate --force

if [ "$ENTORNO" != "produccion" ]; then
    # Datos de ejemplo solo en pruebas (no borra nada: usa firstOrCreate / updateOrCreate).
    php artisan db:seed --force
fi

# Permisos de escritura
chmod -R ug+rwx storage bootstrap/cache

# Cachés (más rápido bajo picos de ingreso)
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Listo. Verifica en el navegador: $(grep '^APP_URL=' .env | cut -d= -f2-)/login"
