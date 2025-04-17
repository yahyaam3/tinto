FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring exif pcntl bcmath gd fileinfo

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Obtener último Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar configuración personalizada de PHP
COPY conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Configurar permisos para www-data
RUN mkdir -p /home/www-data/.composer && \
    chown -R www-data:www-data /home/www-data

# Establecer directorio de trabajo
WORKDIR /var/www

USER www-data 