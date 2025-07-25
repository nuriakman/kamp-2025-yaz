FROM php:8.2-fpm

# Sistem araçları ve Laravel için gerekli PHP uzantıları
RUN apt-get update && apt-get install -y \
  git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev libjpeg-dev libfreetype6-dev \
  && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

# Composer kurulumu
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Çalışma dizini
WORKDIR /var/www/html

# Varsayılan olarak artisan kullanılabilir
CMD ["php-fpm"]
