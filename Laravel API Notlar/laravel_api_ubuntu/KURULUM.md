# Kurulum Notları (LAMP Stack - Ubuntu)

Bu belge, Laravel API projesini standart bir LAMP (Linux, Apache, MySQL, PHP) sunucusu üzerinde nasıl kuracağınızı ve çalıştıracağınızı adım adım açıklamaktadır.

## Gereksinimler

Başlamadan önce Ubuntu sunucunuzda aşağıdaki servislerin ve araçların kurulu olduğundan emin olun:

- **Apache2**
- **MySQL** (veya MariaDB)
- **PHP** (Proje PHP 8.1 ve üzerini tavsiye eder)
- Gerekli PHP Eklentileri: `php-cli`, `php-mbstring`, `php-xml`, `php-curl`, `php-mysql`, `php-zip`
- **Composer** (PHP paket yöneticisi)
- **Git**

---

### Adım 1: Gerekli Paketleri Kurma

Eğer sunucunuzda LAMP ve Composer kurulu değilse, aşağıdaki komutlarla kurabilirsiniz:

```bash
# Paket listesini güncelle
sudo apt update

# Apache, MySQL ve PHP kur
sudo apt install -y apache2 mysql-server php php-cli php-mbstring php-xml php-curl php-mysql php-zip unzip

# Composer'ı kur
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

---

### Adım 2: Yeni Laravel Projesi Oluşturma

#### 1. **Yeni Proje Oluşturun:** Apache'nin web kök dizinine (`/var/www/html`) gidin ve Composer kullanarak yeni bir Laravel projesi oluşturun. Proje adını `laravel_api` olarak belirleyebilirsiniz.

```bash
cd /var/www/html
composer create-project --prefer-dist laravel/laravel laravel_api
cd laravel_api
```

---

### Adım 3: Laravel Projesini Yapılandırma

#### 1. **`.env` Dosyasını Oluşturun:** Örnek yapılandırma dosyasını kopyalayarak kendi ortam dosyanızı oluşturun.

```bash
cp .env.example .env
```

#### 2. **Uygulama Anahtarı Oluşturun:** Laravel projeniz için güvenli bir anahtar oluşturun.

```bash
php artisan key:generate
```

#### 3. **Veritabanı Oluşturun ve Yapılandırın:**

##### MySQL Veritabanı İle Çalışma

- MySQL'e giriş yapın ve proje için bir veritabanı ve kullanıcı oluşturun.

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

- `.env` dosyasını açıp veritabanı bilgilerini güncelleyin.

```bash
vi .env
```

Dosyadaki ilgili satırları aşağıdaki gibi düzenleyin:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_api_db
DB_USERNAME=api_user
DB_PASSWORD=password
```

##### MySQL Yerine SQLite Kullanmak için:

1. **SQLite Kurulumu:**

```bash
sudo apt-get install php-sqlite3
```

2. **Veritabanı Dosyasını Oluşturun:**

```bash
touch database/database.sqlite
chmod 777 database/database.sqlite
```

3. **.env Dosyasını Düzenleyin:**

```ini
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/laravel_api/database/database.sqlite
```

Diğer veritabanı ayarlarını yorum satırı yapabilirsiniz.

4. **SQLite PHP Eklentisini Etkinleştirin:**

```bash
sudo phpenmod sqlite3
sudo systemctl restart apache2
```

SQLite, geliştirme ortamları için hafif ve kullanımı kolay bir veritabanı çözümüdür. MySQL gibi ayrı bir veritabanı sunucusu gerektirmez ve tüm veriler tek bir dosyada saklanır. Ancak, yüksek ölçekli üretim ortamları için önerilmez. Ders

#### 4. **Veritabanı Göçlerini (Migrations) Çalıştırın:** Tanımlı tabloları veritabanında oluşturmak için migrate komutunu çalıştırın.

```bash
php artisan migrate
```

---

### OPSIYONEL Adım : Apache Sanal Konak (Virtual Host) Ayarları

Virtual Host, bir web sunucusunun birden fazla web sitesini barındırabilmesini sağlayan bir yapıdır.

Projenizin `localhost` yerine `laravel-api.test` gibi daha temiz bir URL ile çalışması için bir sanal konak oluşturalım.

#### 1. **Yeni Konfigürasyon Dosyası Oluşturun:**

```bash
sudo vi /etc/apache2/sites-available/laravel_api.conf
```

#### 2. **Dosya İçeriğini Doldurun:**

Aşağıdaki yapılandırmayı dosyaya yapıştırın. Bu yapılandırma, istekleri projenin `public` dizinine yönlendirir.

**`/etc/apache2/sites-available/laravel_api.conf` dosyası içeriği:**

```apache
<VirtualHost *:80>
    ServerName laravel-api.test
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/laravel_api/public

    <Directory /var/www/html/laravel_api/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

**`hosts` dosyasına ekleme:**

```bash
sudo vi /etc/hosts
```

```ini
127.0.0.1   laravel-api.test
```

#### 3. **Sanal Host'u Aktifleştirin ve Gerekli Modülleri Açın:**

```bash
sudo a2ensite laravel_api.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

##### OPSIYONEL: Yeni bir başka site eklemek için

1. Yeni bir konfigürasyon dosyası oluşturun
   (örneğin: `/etc/apache2/sites-available/cv-projem.conf`)
2. Aynı yapılandırmayı kullanın, ancak şu değerleri güncelleyin:
   - `ServerName` (örneğin: `cv-projem.test`)
   - `DocumentRoot` (yeni projenizin public dizini)
   - `<Directory>` içindeki yol
3. Yeni domain adını `/etc/hosts` dosyasına ekleyin
4. Yeni siteyi etkinleştirin:
   ```bash
   sudo a2ensite cv-projem.conf
   sudo systemctl restart apache2
   ```

**Not:** Her yeni site için benzersiz bir domain adı ve belge kök dizini kullanmalısınız.

---

### Adım 4: Dosya İzinlerini Ayarlama

Laravel'in `storage` ve `bootstrap/cache` dizinlerine yazabilmesi için web sunucusuna gerekli izinleri verin.

```bash
sudo chown -R www-data:www-data /var/www/html/laravel_api/storage
sudo chown -R www-data:www-data /var/www/html/laravel_api/bootstrap/cache
sudo chmod -R 775 /var/www/html/laravel_api/storage
sudo chmod -R 775 /var/www/html/laravel_api/bootstrap/cache
```

### Adım 5: Ortamı Doğrulayın

Eğer sanal konak ayarladıysanız, `laravel-api.test` gibi bir adresi yerel makinenizin `hosts` dosyasına eklemeniz gerekebilir.

- **Linux/macOS:** `sudo vi /etc/hosts`
- **Windows:** `C:\Windows\System32\drivers\etc\hosts`

Dosyaya şu satırı ekleyin:
`127.0.0.1   laravel-api.test`

Tarayıcınızı açın ve `http://laravel-api.test` adresine gidin. Ekranda Laravel başlangıç sayfasını görüyorsanız, kurulum başarıyla tamamlanmış demektir.

Artık API'nizi geliştirmeye hazırsınız! İyi kodlamalar!
