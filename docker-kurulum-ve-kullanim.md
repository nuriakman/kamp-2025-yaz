# Eğitim İçin Docker Konfigürasyonu ve Kullanım Notları

Bu dosya, farklı işletim sistemlerinden katılan katılımcıların bilgisayar ayarlarını bozmadan, eğitim ortamını hızlıca kurup kullanabilmeleri için Docker tabanlı bir çözüm sunar. Ana geliştirme ortamı Ubuntu tabanlıdır ve PHP, MySQL, Composer, Node.js, NPM, Laravel, Vue.js, Quasar gibi araçları içerir.

---

## 1. Docker Gereksinimleri

- Katılımcıların bilgisayarında Docker Desktop (Windows/macOS) veya Docker Engine (Linux) kurulu olmalıdır.
- [Docker Kurulum Rehberi](https://docs.docker.com/get-docker/)

---

## Docker Volume ve Dizin Eşleştirmeleri

Aşağıdaki tablo, host (yerel) makinedeki klasör/dosya ile Docker container içindeki karşılıklarını gösterir:

| Yerel Dizin/Kaynak      | Container İçinde           | Açıklama                       |
| ----------------------- | -------------------------- | ------------------------------ |
| ./src                   | /var/www/html              | Laravel backend kodları        |
| ./frontend              | /app                       | Vue.js/Quasar frontend kodları |
| ./php.ini               | /usr/local/etc/php/php.ini | PHP ayar dosyası               |
| db_data (Docker volume) | /var/lib/mysql             | MySQL veritabanı kalıcı verisi |

- `src` ve `frontend` klasörleri doğrudan host makinede tutulur, değişiklikler anında container'a yansır.
- `db_data` volume'u, veritabanı kayıtlarının silinmeden korunmasını sağlar.

---

```yaml
version: '3.8'
services:
  app:
    image: php:8.2-apache
    container_name: egitim_app
    volumes:
      - ./src:/var/www/html
      - ./php.ini:/usr/local/etc/php/php.ini
    ports:
      - '8080:80'
    depends_on:
      - db
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html/public
    networks:
      - egitim_net

  db:
    image: mysql:8.0
    container_name: egitim_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: egitim
      MYSQL_USER: egitim
      MYSQL_PASSWORD: egitim123
    ports:
      - '3306:3306'
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - egitim_net

  node:
    image: node:20
    container_name: egitim_node
    working_dir: /app
    volumes:
      - ./frontend:/app
    tty: true
    networks:
      - egitim_net

volumes:
  db_data:

networks:
  egitim_net:
```

---

## 3. Ek Dosyalar

### php.ini (Örnek)

```
[PHP]
display_errors=On
memory_limit=512M
upload_max_filesize=64M
post_max_size=64M
```

---

## 4. Kullanım Talimatları

### 4.1 Klasör Yapısı

```
Kamp-2025-Yaz/
├── docker-compose.yml
├── php.ini
├── src/         # Laravel backend kodları
└── frontend/    # Vue/Quasar frontend kodları
```

### 4.2 Başlatma

Terminalde proje klasöründe:

```bash
docker compose up -d
```

- `http://localhost:8080` adresinden Laravel uygulamasına erişebilirsiniz.
- MySQL 3306 portu dışarıya açılmıştır (dilerseniz portu değiştirebilirsiniz).

### 4.3 Laravel Kurulumu (İlk Çalıştırmada)

```bash
docker compose exec app bash
cd /var/www/html
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 4.4 Frontend (Vue/Quasar) Kurulumu

```bash
docker compose exec node bash
cd /app
npm install
npm run dev
```

- Geliştirme sırasında frontend için ayrı terminalde `npm run dev` komutunu çalıştırabilirsiniz.

### 4.5 Veritabanı Yönetimi

- Dilerseniz [Adminer](https://hub.docker.com/_/adminer) veya phpMyAdmin servisi de ekleyebilirsiniz.

---

## 5. Notlar ve İpuçları

- Katılımcıların kendi bilgisayarlarında hiçbir ayar değiştirmesine gerek yoktur.
- Tüm geliştirme dosyaları host makinede tutulur, containerlar silinse bile kodlar kaybolmaz.
- Farklı işletim sistemlerinde Docker ile aynı ortam sağlanır.
- Gerekirse volume ve port ayarlarını değiştirebilirsiniz.

---

Herhangi bir sorun yaşanırsa Docker loglarını inceleyin veya containerları yeniden başlatın:

```bash
docker compose logs
```

```bash
docker compose restart
```

---

## Önemli: Kodlarınız ve Veritabanı Kayıtlarınız Güvende!

- **Kodlarınız (src/ ve frontend/ klasörleri)** doğrudan kendi bilgisayarınızda tutulur. Docker containerları silseniz bile bu klasörlerdeki dosyalarınız kaybolmaz.
- **Veritabanı kayıtlarınız (MySQL)** ise `db_data` adlı Docker volume'unda saklanır. Container silinirse bile volume silinmediği sürece verileriniz korunur.
- Veritabanını tamamen silmek istemediğiniz sürece şu komutları kullanmayın:
  - `docker volume rm db_data`
  - `docker compose down -v` (bu komut volume'ları da siler)
- Yalnızca `docker compose down` veya `docker compose rm` ile containerları silerseniz, volume ve kodlarınız korunur.

Herhangi bir sorun veya veri kaybı endişeniz olursa, volume'ları ve kod klasörlerinizi ayrıca yedekleyebilirsiniz.

---
