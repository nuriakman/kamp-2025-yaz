# Docker Kurulum ve Temel Kullanım Rehberi

Bu belge, eğitim ortamı için Docker ile hızlı, güvenli ve pratik bir geliştirme ortamı kurmanızı sağlar. Tüm işlemler, yerel makinenizi bozmadan, PHP, MySQL, Node.js, Laravel ve Vue/Quasar gibi araçlarla modern bir yazılım geliştirme süreci sunar.

---

## 1. Gereksinimler ve Hazırlık

- Bilgisayarınızda Docker Desktop (Windows/macOS) veya Docker Engine (Linux) kurulu olmalı.
- [Docker Kurulum Rehberi](https://docs.docker.com/get-docker/)
- Geliştirme için bir kod editörü (VS Code önerilir).

---

## 2. Proje Dizin Yapısı ve Volume Eşleştirmeleri

Aşağıdaki tablo ve yapı, dosyalarınızın Docker container'ları ile nasıl eşleştiğini gösterir:

| Yerel Dizin/Kaynak      | Container İçinde           | Açıklama                       |
| ----------------------- | -------------------------- | ------------------------------ |
| ./backend               | /var/www/html              | Laravel backend kodları        |
| ./frontend              | /app                       | Vue.js/Quasar frontend kodları |
| ./php.ini               | /usr/local/etc/php/php.ini | PHP ayar dosyası               |
| db_data (Docker volume) | /var/lib/mysql             | MySQL veritabanı kalıcı verisi |

- `backend` ve `frontend` klasörleri doğrudan host makinede tutulur, değişiklikler anında container'a yansır.
- `db_data` volume'u, veritabanı kayıtlarının silinmeden korunmasını sağlar.

Dizin örneği:

```
Kamp-2025-Yaz/
├── docker-compose.yml
├── php.ini
├── backend/         # Laravel backend kodları
└── frontend/        # Vue/Quasar frontend kodları
└── wordpress_data/  # WordPress veritabanı verileri
```

**Hazırlık:**

```bash
mkdir backend
mkdir frontend
mkdir wordpress_data
touch php.ini
```

---

## 3. docker-compose.yml ile Ortamı Kurma

Aşağıda örnek bir `docker-compose.yml` dosyası bulabilirsiniz. Kendi projenize göre servis isimlerini ve portları değiştirebilirsiniz.

**NOT:** Güncel `docker-compose.yml` dosyasını [docker-compose.yml](docker-compose.yml) dosyasından bulabilirsiniz.

```yaml
services:
  app:
    image: php:8.2-apache
    container_name: egitim_app
    volumes:
      - ./backend:/var/www/html
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
      - ./db_data:/var/lib/mysql
    networks:
      - egitim_net

  adminer:
    image: adminer
    container_name: egitim_adminer
    restart: always
    ports:
      - '8081:8080'
    networks:
      - egitim_net

  wordpress:
    image: wordpress:latest
    container_name: egitim_wordpress
    depends_on:
      - db
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: egitim
      WORDPRESS_DB_PASSWORD: egitim123
      WORDPRESS_DB_NAME: egitim
    volumes:
      - ./wordpress_data:/var/www/html
    ports:
      - '8082:80'
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
  wordpress_data:

networks:
  egitim_net:
```

---

## 4. Başlatma ve İlk Kurulum

### 4.1 Ortamı Başlatma

Proje klasöründe terminal açıp:

```bash
docker compose up -d
```

- `http://localhost:8080` adresinden Laravel uygulamasına erişebilirsiniz.
- MySQL 3306 portu dışarıya açılmıştır (gerekirse değiştirebilirsiniz).

### 4.2 Laravel Kurulumu (İlk Çalıştırmada)

```bash
docker compose exec app bash
cd /var/www/html
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 4.3 Frontend (Vue/Quasar) Kurulumu

```bash
docker compose exec node bash
cd /app
npm install
npm run dev
```

- Frontend geliştirme için ayrı terminalde `npm run dev` komutunu çalıştırabilirsiniz.

---

## 5. Loglar, Hata Ayıklama ve Temizlik

### 5.1 Logları İzleme

```bash
# Laravel loglarını izleme
docker compose exec app tail -f /var/www/html/storage/logs/laravel.log
# Apache hata loglarını izleme
docker compose exec app tail -f /var/log/apache2/error.log
# MySQL hata loglarını izleme
docker compose exec db tail -f /var/log/mysql/error.log
```

### 5.2 Servisleri Yönetme

- Tek bir servisi yeniden başlat:
  ```bash
  docker compose restart app  # veya db, node
  ```
- Tüm servisleri durdur:
  ```bash
  docker compose down
  ```
- Tüm container ve volume'ları silmek için (veri kaybı!):
  ```bash
  docker compose down -v
  ```

---

## 6. Önemli Notlar ve Güvenlik

- **Kodlarınız (src/ ve frontend/ klasörleri)** doğrudan kendi bilgisayarınızda tutulur. Docker containerları silseniz bile bu klasörlerdeki dosyalarınız kaybolmaz.
- **Veritabanı kayıtlarınız (MySQL)** ise `db_data` adlı Docker volume'unda saklanır. Container silinirse bile volume silinmediği sürece verileriniz korunur.
- Veritabanını tamamen silmek istemediğiniz sürece şu komutları kullanmayın:
  - `docker volume rm db_data`
  - `docker compose down -v` (bu komut volume'ları da siler)
- Yalnızca `docker compose down` veya `docker compose rm` ile containerları silerseniz, volume ve kodlarınız korunur.
- Hassas bilgileri (şifreler, API anahtarları) `.env` dosyasında tutun.
- Yedek almak için volume ve kod klasörlerinizi ayrıca kopyalayabilirsiniz.

---

Herhangi bir sorun yaşarsanız logları inceleyin veya containerları yeniden başlatın:

```bash
docker compose logs
```

```bash
docker compose restart
```

---

Bu rehber, Docker ile eğitim ortamınızı hızlıca kurup güvenle kullanmanız için hazırlanmıştır. Daha gelişmiş komutlar ve ileri seviye yapılandırmalar için `docker-pratik-rehber.md` dosyasına başvurabilirsiniz.

---

> **docker-compose.yml** örneği için yukarıdaki ilgili bölüme bakınız.

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

## 6. Docker Komutları

Docker'ın en temel komutlarını içeren özet tablo:

| Komut               | Açıklama                              | Örnek Kullanım                      |
| ------------------- | ------------------------------------- | ----------------------------------- |
| **`docker ps`**     | Çalışan container'ları listeler       | `docker ps -a` (tüm container'lar)  |
| **`docker run`**    | Yeni container başlatır               | `docker run -d -p 8080:80 nginx`    |
| **`docker stop`**   | Container durdurur                    | `docker stop container_id`          |
| **`docker rm`**     | Container siler                       | `docker rm container_id`            |
| **`docker images`** | İndirilen image'ları listeler         | `docker images ls`                  |
| **`docker rmi`**    | Image siler                           | `docker rmi image_id`               |
| **`docker pull`**   | Image indirir                         | `docker pull ubuntu:latest`         |
| **`docker exec`**   | Çalışan container'da komut çalıştırır | `docker exec -it container_id bash` |
| **`docker logs`**   | Container loglarını gösterir          | `docker logs -f container_id`       |
| **`docker build`**  | Dockerfile'dan image oluşturur        | `docker build -t myimage .`         |

### Özel Parametreler:

- `-d` : Detach modda çalıştırır (arkada)
- `-it` : Interactive terminal açar
- `-p` : Port yönlendirme (host:container)
- `-v` : Volume bağlama (`-v /host/path:/container/path`)
- `--name` : Container'a özel isim verir

> 💡 **İpucu**: `docker --help` ile her komutun detaylı kullanımını görebilirsiniz. Örneğin: `docker run --help`
