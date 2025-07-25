# Laravel + Quasar + MySQL Docker Geliştirme Ortamı

Bu proje, Laravel (Backend), Quasar (Frontend) ve MySQL veritabanı içeren tam bir geliştirme ortamı sağlar. Tüm bileşenler Docker konteynerleri içinde çalıştırılmaktadır.

## 📋 Özellikler

- 🐘 PHP 8.2 ile Laravel 10+
- 🎨 Quasar Framework ile Vue.js 3 tabanlı Frontend
- 🗄️ MySQL 8.0 veritabanı
- 🖥️ Adminer ile veritabanı yönetimi
- 🔄 Sıcak yeniden yükleme (Hot Reload) desteği
- 🔒 Güvenli yapılandırma

## 🚀 Hızlı Başlangıç

### Gereksinimler

- [Docker](https://www.docker.com/get-started/) (Docker Desktop veya Docker Engine)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git (isteğe bağlı)

### Kurulum

1. Projeyi klonlayın:

   ```bash
   git clone https://github.com/kullaniciadiniz/proje-adi.git
   cd proje-adi/docker
   ```

2. Docker konteynerlerini başlatın:

   ```bash
   docker-compose up -d
   ```

3. Backend bağımlılıklarını yükleyin:

   ```bash
   docker-compose exec backend composer install
   ```

4. .env dosyasını oluşturun ve uygulama anahtarı üretin:

   ```bash
   docker-compose exec backend cp .env.example .env
   docker-compose exec backend php artisan key:generate
   ```

5. Frontend bağımlılıklarını yükleyin:

   ```bash
   docker-compose exec frontend npm install
   ```

6. Veritabanı tablolarını oluşturun ve örnek verileri ekleyin:
   ```bash
   docker-compose exec backend php artisan migrate --seed
   ```

## 🌐 Erişim Bilgileri

- **Laravel Uygulaması**: [http://localhost:8000](http://localhost:8000)
- **Quasar Frontend**: [http://localhost:9000](http://localhost:9000)
- **Adminer (Veritabanı Yönetimi)**: [http://localhost:8080](http://localhost:8080)
  - Sunucu: `db`
  - Kullanıcı: `laravel`
  - Şifre: `laravel123`
  - Veritabanı: `laravel`

## 🛠 Kullanışlı Komutlar

### Konteyner Yönetimi

```bash
# Tüm konteynerleri başlat
docker-compose up -d

# Konteynerleri durdur
docker-compose down

# Logları görüntüle
docker-compose logs -f

# Tüm konteynerlerin durumunu kontrol et
docker-compose ps
```

### Backend Komutları

```bash
# Composer paketlerini güncelle
docker-compose exec backend composer update

# Yeni bir controller oluştur
docker-compose exec backend php artisan make:controller ExampleController

# Tüm önbellekleri temizle
docker-compose exec backend php artisan optimize:clear
```

### Frontend Komutları

```bash
# Bağımlılıkları güncelle
docker-compose exec frontend npm update

# Geliştirme sunucusunu başlat
docker-compose exec frontend quasar dev

# Üretim için derle
docker-compose exec frontend quasar build
```

## 📁 Proje Yapısı

```
docker/
├── backend/           # Laravel uygulama dosyaları
├── frontend/          # Quasar proje dosyaları
├── db_data/           # MySQL veritabanı dosyaları
├── .env.example       # Örnek çevre değişkenleri
└── docker-compose.yml # Docker yapılandırması
```

## 🔧 Geliştirme

### Yeni Paket Ekleme

**Backend (PHP) için:**

```bash
docker-compose exec backend composer require vendor/package
```

**Frontend (Node.js) için:**

```bash
docker-compose exec frontend npm install package-name --save
```

### Debug Yapılandırması

Xdebug ile debug yapmak için IDE'nizi aşağıdaki gibi yapılandırın:

- Host: localhost
- Port: 9003
- IDE Key: PHPSTORM

## 🐛 Sorun Giderme

### Port Çakışmaları

Eğer portlar başka bir uygulama tarafından kullanılıyorsa, `docker-compose.yml` dosyasındaki port numaralarını değiştirebilirsiniz.

### İzin Sorunları

Laravel storage ve bootstrap/cache dizinlerine yazma izni verin:

```bash
docker-compose exec backend chmod -R 777 storage bootstrap/cache
```

### Veritabanı Bağlantı Sorunları

`.env` dosyanızın aşağıdaki gibi yapılandırıldığından emin olun:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel123
```

## 🔄 Güncellemeler

```bash
# Tüm konteyner imajlarını güncelle
docker-compose pull

docker-compose down
docker-compose up -d
```

## 📜 Lisans

Bu proje [MIT lisansı](https://opensource.org/licenses/MIT) altında lisanslanmıştır.
