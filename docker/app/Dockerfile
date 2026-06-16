FROM php:7.4-apache

# --- System libs for PHP extensions ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev libgmp-dev \
        zlib1g-dev unzip git \
    && rm -rf /var/lib/apt/lists/*

# --- PHP extensions the app uses (mysqli, gd, zip, intl, mbstring, exif, bcmath) ---
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        mysqli pdo_mysql gd zip intl mbstring exif bcmath gmp

# --- Composer (for api/vendor) ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Apache: doc root = project root, enable rewrite + .htaccess ---
RUN a2enmod rewrite
RUN sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# OPcache off so PHP edits are live (matches the project's hot-reload intent)
RUN echo "opcache.enable=0" > /usr/local/etc/php/conf.d/zz-no-opcache.ini \
    && echo "memory_limit=512M\nupload_max_filesize=64M\npost_max_size=64M\nmax_execution_time=300" \
        > /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html
