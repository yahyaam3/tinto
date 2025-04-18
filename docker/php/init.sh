#!/bin/bash
set -e

# Configurar permisos correctos
echo "Configurando permisos..."
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Esperar a que MySQL esté disponible
echo "Esperando a que MySQL esté disponible..."
until nc -z -v -w30 db 3306; do
  echo "Esperando a que MySQL se inicie..."
  sleep 3
done
echo "MySQL está disponible!"

# Esperar que Redis esté disponible
echo "Esperando a que Redis esté disponible..."
until nc -z -v -w30 redis 6379; do
  echo "Esperando a que Redis se inicie..."
  sleep 2
done
echo "Redis está disponible!"

cd /var/www

# Generar clave de aplicación si no existe
if [ -z "$(grep -E '^APP_KEY=' .env | grep -v base64)" ]; then
  echo "Generando clave de aplicación..."
  php artisan key:generate --no-interaction --force
fi

# Crear enlace simbólico para storage
echo "Creando enlace simbólico para storage..."
php artisan storage:link --no-interaction || echo "No se pudo crear el enlace simbólico"

# Configurar la base de datos
echo "Configurando la base de datos..."
php artisan migrate --force || echo "No se pudieron ejecutar las migraciones"

# Iniciar PHP-FPM
echo "Iniciando PHP-FPM..."
exec docker-php-entrypoint php-fpm
