# Ubuntu 22.04 LTS temel imajını kullan
FROM ubuntu:22.04

# Kurulum sırasında interaktif diyalogları ve saat dilimi uyarılarını önle
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Etc/UTC

# Gerekli araçları, PPA ve Apache'yi kur
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    apache2 \
    git \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# PHP 8.3 için PPA (Personal Package Archive) ekle ve PHP'yi kur
RUN add-apt-repository ppa:ondrej/php && apt-get update && apt-get install -y \
    php8.3 \
    php8.3-cli \
    php8.3-xml \
    php8.3-zip \
    php8.3-sqlite3 \
    php8.3-mbstring \
    php8.3-curl \
    php8.3-bcmath \
    libapache2-mod-php8.3 \
    && rm -rf /var/lib/apt/lists/*

# Composer'ı global olarak kur
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Apache için mod_rewrite'ı etkinleştir
RUN a2enmod rewrite

# Apache'nin sanal sunucu yapılandırmasını kopyala
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Oluşturduğumuz php.ini dosyasını hem Apache hem de CLI için PHP yapılandırma dizinlerine kopyala
COPY php.ini /etc/php/8.3/apache2/conf.d/99-custom.ini
COPY php.ini /etc/php/8.3/cli/conf.d/99-custom.ini

# Çalışma dizinini ayarla
WORKDIR /var/www/html
