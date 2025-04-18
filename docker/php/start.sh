#!/bin/bash

# Establecer permisos
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Crear enlace simbólico para storage
cd /var/www && php artisan storage:link || echo "No se pudo crear el enlace simbólico"

# Cambiar al usuario www-data para el resto de operaciones
exec php-fpm
