# Escolha a versão do PHP que você precisa
FROM php:8.3-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Instalar extensões do PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

RUN cp .env.production .env

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar e definir o diretório de trabalho
WORKDIR /var/www

# Copiar o projeto Laravel para o contêiner
COPY . .

# Dar permissões ao diretório de cache e storage
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache
