#!/bin/bash

echo "Iniciando la aplicación en Railway..."

# Copiar el archivo de entorno
cp .env.railway .env

# Generar clave de aplicación si no existe
php artisan key:generate --force

# Limpiar caché
php artisan optimize:clear

# Ejecutar migraciones solo si la base de datos está disponible
php artisan migrate --force || true

# Iniciar el servidor
php artisan serve --host=0.0.0.0 --port=$PORT
