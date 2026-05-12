#!/bin/sh
set -e

echo "Iniciando Adminova Backend..."

# Esperar a que PostgreSQL esté listo
echo "Esperando a PostgreSQL..."
until php -r "new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
  echo "   PostgreSQL no disponible, reintentando..."
  sleep 2
done
echo "PostgreSQL listo"

# Optimizaciones Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migraciones
php artisan migrate --force

# Storage link
php artisan storage:link || true

echo "Backend listo en puerto 80"

# Iniciar servicios
exec supervisord -c /etc/supervisord.conf
