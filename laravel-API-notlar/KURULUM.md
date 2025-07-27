# Kurulum Notları (LAMP Stack - Ubuntu)

Bu belge, Laravel API projesini standart bir LAMP (Linux, Apache, MySQL, PHP) sunucusu üzerinde nasıl kuracağınızı ve çalıştıracağınızı adım adım açıklamaktadır.

## 1. Gereksinimler

Başlamadan önce Ubuntu sunucunuzda aşağıdaki servislerin ve araçların kurulu olduğundan emin olun:

- **Apache2**
- **MySQL** (veya MariaDB)
- **PHP** (Proje PHP 8.1 ve üzerini tavsiye eder)
- Gerekli PHP Eklentileri: `php-cli`, `php-mbstring`, `php-xml`, `php-curl`, `php-mysql`, `php-zip`
- **Composer** (PHP paket yöneticisi)
- **Git**

## 2. Kurulum

Eğer sunucunuzda LAMP ve Composer kurulu değilse, aşağıdaki komutlarla kurabilirsiniz:

```bash
# Paket listesini güncelle
sudo apt update

# Apache, MySQL ve PHP kur
sudo apt install -y apache2 mysql-server php php-cli php-mbstring php-xml php-curl php-mysql php-sqlite3 php-zip unzip
sudo phpenmod sqlite3
sudo a2enmod rewrite
sudo systemctl restart apache2

# Composer'ı kur
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

Laravel çalışabilmek için gerekli temel kurulum tamamlandı. Kontrol edelim:

```bash
# PHP versiyonunu kontrol edin
php --version
# Örnek çıktı: PHP 8.3.23 (cli) (built: Jul  3 2025 16:11:22) (NTS)

# Composer'ın kurulu olduğunu kontrol edin
composer --version
# Örnek çıktı: Composer version 2.8.8 2025-04-04 16:56:46

# MySQL'in çalıştığını kontrol edin (MySQL kullanıyorsanız)
mysql --version
# Örnek çıktı: mysql  Ver 15.1 Distrib 10.11.13-MariaDB
```

## 3. Yeni Laravel Projesi Oluşturma

Apache'nin web kök dizinine (`/var/www/html`) gidin ve Composer kullanarak yeni bir Laravel projesi oluşturun. Proje adını `laravel_api` olarak belirleyebilirsiniz.

```bash
cd /var/www/html
composer create-project --prefer-dist laravel/laravel laravel_api
cd laravel_api
```

**`composer create-project --prefer-dist laravel/laravel laravel_api` Komutunun Açıklaması**

Bu komut, yeni bir Laravel projesi oluşturmak için kullanılan standart bir Composer komutudur. Adım adım ne yaptığını inceleyelim:

**Komutun Parçaları ve Anlamları:**

1. **`composer`**: PHP'nin bağımlılık yöneticisi
2. **`create-project`**: Yeni bir proje oluşturma komutu
3. **`--prefer-dist`**: Paketlerin kurulum şeklini belirtir
   - Kaynak kod yerine sıkıştırılmış arşivleri (zip) tercih eder
   - Daha hızlı kurulum sağlar
   - Geliştirme yapmayacağınız bağımlılıklar için idealdir
4. **`laravel/laravel`**: Kurulacak paketin adı
   - Laravel'in resmi başlangıç projesi
   - Packagist'te kayıtlı bir paket
5. **`laravel_api`**: Oluşturulacak proje klasörünün adı
   - Bu isimde yeni bir dizin oluşturur
   - İstediğiniz ismi verebilirsiniz (örneğin: `my_project`)

**Komutun Tam Olarak Yaptıkları:**

1. **Yeni bir proje oluşturur**:
   - `laravel_api` adında yeni bir dizin açar
   - Laravel'in en son stabil sürümünü kurar
2. **Tüm bağımlılıkları yükler**:
   - `vendor` dizinini oluşturur
   - `composer.json`'da belirtilen tüm paketleri kurar
3. **Temel Laravel yapısını hazırlar**:
   - App, config, database, resources gibi temel dizinleri oluşturur
   - `.env` dosyasını örnekler (`cp .env.example .env`)
4. **Temel konfigürasyonları ayarlar**:
   - Uygulama anahtarını oluşturur (`php artisan key:generate`)

Bu komut, Laravel ile yeni bir projeye başlamanın en standart ve güvenilir yoludur. Her yeni Laravel projesinde bu komut veya benzeri bir varyasyonu kullanabilirsiniz.

## DİKKAT!!!

<blockquote>
ŞU AŞAMADA, `cd laravel_api` KOMUTU İLE PROJE DİZİNİNE GEÇTİĞİNİZİ DÜŞÜNÜYORUZ.
<br><br>

ŞU ANDAN İTİBAREN BU EĞİTİMDE KULLANILACAK TÜM KOMUTLAR, PROJE DİZİNİNDE OLDUĞUNUZ DÜŞÜNCESİ İLE DOKÜMANTE EDİLMİŞTİR!
</blockquote>


## 4. Laravel Projesini Yapılandırma

ÖNEMLİ: `composer` komutu ile proje başlattıysanız, `.env` dosyanız hazırdır. Bir sonraki başlıktan devam edebilirsiniz.

**`.env` Dosyasını Oluşturun:** Örnek yapılandırma dosyasını kopyalayarak kendi ortam dosyanızı oluşturun.

```bash
cp .env.example .env
```

**Uygulama Anahtarı Oluşturun:** Laravel projeniz için güvenli bir anahtar oluşturun.

```bash
php artisan key:generate
```

---

## 5. Veritabanı Oluşturun ve Yapılandırın

Laravel `composer` ile kurulduğunda varsayılan olarak SQLite veritabanını kullanır. Bu veritabanı `./database/database.sqlite` dosyasına baglanmaktadır. 

Projeyi oluşturduğunuzda gelen bu hazır yapı ile ilerlemek isterseniz, bu bölümü atlayıp bir sonraki maddeden devam edebilirsiniz.

---

### Seçenek 1: MySQL Veritabanı

MySQL, yüksek ölçekli üretim ortamları için önerilen bir veritabanı yönetim sistemidir. Birden fazla sunucu üzerinde çalışabilir ve verileri güvenli bir şekilde saklar. MySQL kullanamk için önce bir veritabanı oluşturmamız, sonra bir kullanıcı oluşturup bu veritabanına yetkiler vermemiz gerekiyor.

**DB ve User Oluşturma:**

```sql
-- MySQL'e giriş yapın
sudo mysql

-- Veritabanı oluşturun
CREATE DATABASE laravel_api_db;

-- Kullanıcı oluşturun ve şifre belirleyin (güçlü bir şifre kullanın)
CREATE USER 'api_user'@'localhost' IDENTIFIED BY 'password';

-- Kullanıcıya yetkileri verin
GRANT ALL PRIVILEGES ON laravel_api_db.* TO 'api_user'@'localhost';

-- Yetkileri yenileyin ve çıkın
FLUSH PRIVILEGES;
EXIT;
```

**.env Dosyasını Düzenleme:**

```bash
vi .env
```

`.env` dosyasındaki ilgili satırları aşağıdaki gibi düzenleyin:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_api_db
DB_USERNAME=api_user
DB_PASSWORD=password
```

---

### Seçenek 2: SQLite Veritabanı

SQLite, geliştirme ortamları için hafif ve kullanımı kolay bir veritabanı çözümüdür. MySQL gibi ayrı bir veritabanı sunucusu gerektirmez ve tüm veriler tek bir dosyada saklanır. Ancak, yüksek ölçekli üretim ortamları için önerilmez. Kolay kurulum yönüyle öğrenim aşamasında Laravel uygulamalarında sıklıkla kullanılır.

**Veritabanı Dosyasını Oluşturun:**

```bash
touch database/database.sqlite
chmod 777 database/database.sqlite
```

**.env Dosyasını Düzenleme:**

```ini
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite
```

## 6. Veritabanında Tabloları Oluşturun (Migrations)

Tanımlı tabloları veritabanında oluşturmak için migrate komutunu çalıştırın. Böylece, Laravel'in varsayılan tabloları veritabanında oluşturulur.

```bash
php artisan migrate
```

## 7. Dosya İzinlerini Ayarlama

Laravel'in `storage` ve `bootstrap/cache` dizinlerine yazabilmesi için web sunucusuna gerekli izinleri verin.

```bash
sudo chown -R www-data:www-data ./storage
sudo chown -R www-data:www-data ./bootstrap/cache
sudo chmod -R 775 ./storage
sudo chmod -R 775 ./bootstrap/cache
```

## 8. Geliştirme Sunucusunu Başlatın

Projeyi geliştirme sunucusu olarak başlatın.

```bash
php artisan serve
```

Artık API'nizi geliştirmeye hazırsınız! İyi kodlamalar!

---
