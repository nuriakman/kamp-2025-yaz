# Laravel Docker Komutları

Bu doküman, Docker konteynerleri içinde çalışan bir Laravel uygulamasında sık kullanılan komutları içerir.

## Temel Komutlar

### 1. Migration İşlemleri

```bash
# Tüm migration'ları çalıştır
docker-compose exec backend php artisan migrate

# Migration'ları geri al (son batch'i geri alır)
docker-compose exec backend php artisan migrate:rollback

# Tüm migration'ları sıfırla (tüm tabloları siler)
docker-compose exec backend php artisan migrate:reset

# Tüm migration'ları yeniden çalıştır (reset + migrate)
docker-compose exec backend php artisan migrate:refresh

# Migration'ları yeniden çalıştır ve veritabanını seed et
docker-compose exec backend php artisan migrate:refresh --seed

# Migration durumunu göster
docker-compose exec backend php artisan migrate:status
```

### 2. Route İşlemleri

```bash
# Tüm route'ları listele
docker-compose exec backend php artisan route:list

# Route cache'ini temizle
docker-compose exec backend php artisan route:clear

# Route'ları cache'e al (üretim ortamı için)
docker-compose exec backend php artisan route:cache
```

### 3. Cache ve Optimizasyon

```bash
# Tüm cache'leri temizle
docker-compose exec backend php artisan cache:clear

# Config cache'ini temizle
docker-compose exec backend php artisan config:clear

# View cache'ini temizle
docker-compose exec backend php artisan view:clear

# Tüm önbellekleri temizle (cache, route, config, view)
docker-compose exec backend php artisan optimize:clear

# Uygulamayı optimize et (üretim öncesi)
docker-compose exec backend php artisan optimize

# Config cache'ini oluştur
docker-compose exec backend php artisan config:cache

# View cache'ini oluştur
docker-compose exec backend php artisan view:cache
```

### 4. Veritabanı İşlemleri

```bash
# Veritabanı seed çalıştır
docker-compose exec backend php artisan db:seed

# Belirli bir seeder'ı çalıştır
docker-compose exec backend php artisan db:seed --class=UsersTableSeeder

# Migration ve seed (taze bir kurulum için)
docker-compose exec backend php artisan migrate:fresh --seed
```

### 5. Diğer Yararlı Komutlar

```bash
# Tinker ile etkileşimli kabuk
docker-compose exec backend php artisan tinker

# Storage linki oluştur
docker-compose exec backend php artisan storage:link

# Uygulama anahtarı oluştur
docker-compose exec backend php artisan key:generate

# Tüm komutları listele
docker-compose exec backend php artisan list

# Yeni bir model oluştur
docker-compose exec backend php artisan make:model Example

# Yeni bir controller oluştur
docker-compose exec backend php artisan make:controller ExampleController

# Yeni bir middleware oluştur
docker-compose exec backend php artisan make:middleware CheckAge
```

## Örnek Kullanım Senaryoları

### Yeni Bir Özellik Eklerken

```bash
# 1. Yeni bir migration oluştur
docker-compose exec backend php artisan make:migration create_examples_table

# 2. Migration dosyasını düzenle (database/migrations/..._create_examples_table.php)

# 3. Migration'ları çalıştır
docker-compose exec backend php artisan migrate

# 4. Model ve Controller oluştur
docker-compose exec backend php artisan make:model Example -mcr

# 5. Route tanımlarını yap (routes/web.php veya routes/api.php)

# 6. Controller'ı düzenle

# 7. View dosyalarını oluştur

# 8. Önbellekleri temizle
docker-compose exec backend php artisan optimize:clear
```

### Üretim Öncesi Hazırlık

```bash
# 1. Uygulama anahtarını oluştur
docker-compose exec backend php artisan key:generate

# 2. Config cache'ini oluştur
docker-compose exec backend php artisan config:cache

# 3. Route cache'ini oluştur
docker-compose exec backend php artisan route:cache

# 4. View cache'ini oluştur
docker-compose exec backend php artisan view:cache

# 5. Uygulamayı optimize et
docker-compose exec backend php artisan optimize
```

## Sorun Giderme

### Migration Sırasında Hata Alırsanız

```bash
# Migration durumunu kontrol et
docker-compose exec backend php artisan migrate:status

# Migration'ları sıfırla ve yeniden dene
docker-compose exec backend php artisan migrate:refresh

# Migration dosyalarını yeniden yükle (cache'i temizle)
docker-compose exec backend php artisan migrate:fresh
```

### Cache Sorunları İçin

```bash
# Tüm cache'leri temizle
docker-compose exec backend php artisan cache:clear

# Config cache'ini temizle
docker-compose exec backend php artisan config:clear

# View cache'ini temizle
docker-compose exec backend php artisan view:clear

# Route cache'ini temizle
docker-compose exec backend php artisan route:clear

# Tüm önbellekleri temizle
docker-compose exec backend php artisan optimize:clear
```

## İpuçları

1. Komutları daha kısa yazmak için bir alias oluşturabilirsiniz:
   ```bash
   # ~/.bashrc veya ~/.zshrc dosyanıza ekleyin
   alias art='docker-compose exec backend php artisan'
   
   # Kullanımı:
   # art migrate
   # art cache:clear
   ```

2. Tinker ile veritabanı işlemleri yaparken model sınıflarınızı import etmeyi unutmayın:
   ```php
   use App\Models\User;
   User::all();
   ```

3. Üretim ortamında asla `php artisan serve` kullanmayın, bunun yerine bir web sunucusu (Nginx, Apache) kullanın.

## Daha Fazla Bilgi İçin

- [Laravel Dokümantasyonu](https://laravel.com/docs)
- [Laravel Artisan Komutları](https://laravel.com/docs/artisan)
