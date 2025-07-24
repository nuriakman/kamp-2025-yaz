# Kurulum

```bash
docker compose up -d
docker exec -it ubuntu-nuri bash
```

bash'de şu komutları çalıştır:

```bash
apt update && apt install libxml2-dev zip unzip git curl apache2 php php-cli php-mbstring php-xml php-curl php-mysql php-zip php-intl php-xml sqlite3 php-sqlite3 vim -y

echo "ServerName localhost" >> /etc/apache2/apache2.conf
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

chown -R www-data:www-data /var/www/html/laravel_api/
chmod -R 775 /var/www/html/laravel_api/

service apache2 restart

```

İşlem tamamlandığında `exit` komutu ile çıkabilirsiniz

IDE içinde F1 tuşuna basıp "Attach to Running Container" seçeneğini seçin.
Artık IDE içinde Ubuntu sunucunuzda çalışabilirsiniz.
IDE içindeki terminali açarak `php artisan` gibi komutları çalıştırabilirsiniz.
