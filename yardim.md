# Yardım

```bash
# Sunucuyu başlat
php artisan serve

# Başlatılmış tüm sunucuları kapat
pkill -f "php artisan serve"

# Bu portu hangi uygulama kullanyor?
lsof -i :80 | grep "LISTEN"

# 8000 nolu portu dinleyen uygulamalar hangileridir?
netstat -tulpn|grep "8000"

# laravel sunucusuna localhost dışından da bağlanabilme
php artisan serve --host=0.0.0.0 --port=8000

# PHP dosyalarında yazım hatası (syntax error) var mı? kontrolü
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```
