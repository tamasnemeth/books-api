FROM php:8.3-fpm

# Argumentumok
ARG user=books
ARG uid=1000

# Rendszer függőségek telepítése
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composer telepítése
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Felhasználó létrehozása
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Munkamappa beállítása
WORKDIR /var/www/html

# Felhasználó váltása
USER $user