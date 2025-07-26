# OPSIYONEL Adım : Apache Sanal Konak (Virtual Host) Ayarları

Virtual Host, bir web sunucusunun birden fazla web sitesini barındırabilmesini sağlayan bir yapıdır.

**NOT:** Aşağıdaki örnek, üzerinde çalıştığımız projenin `var/www/html/laravel-api` klasöründe oluşturulduğunu düşünerek hazırlanmıştır. Yeni siteleri/projeleri oluştururken, bu örnekteki `laravel-api` yazan yerlere projenizin dizininin adını yazmalısınız.

Projenizin `localhost` yerine `laravel-api.test` gibi daha temiz bir URL ile çalışması için bir sanal konak oluşturalım.

## 1. Yeni Apache Sanal Konak (Virtual Host) Dosyası Oluşturun:

```bash
sudo vi /etc/apache2/sites-available/laravel_api.conf
```

## 2. Dosya İçeriğini Doldurun:

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

**`/etc/hosts` dosyasına `laravel-api.test` adını ekleme:**

```bash
sudo vi /etc/hosts
```

```ini
127.0.0.1   laravel-api.test
```

## 3. Sanal Host'u Aktifleştirin ve Gerekli Modülleri Açın:

```bash
sudo a2ensite laravel_api.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## OPSIYONEL: Yeni bir başka site eklemek için

- Yeni bir konfigürasyon dosyası oluşturun
  (örneğin: `/etc/apache2/sites-available/cv-projem.conf`)

- Aynı yapılandırmayı kullanın, ancak şu değerleri güncelleyin:

  - `ServerName` (örneğin: `cv-projem.test`)
  - `DocumentRoot` (yeni projenizin public dizini)
  - `<Directory <yeni dizin>>` içindeki yol

- Yeni domain adını `/etc/hosts` dosyasına ekleyin

- Yeni siteyi etkinleştirin:
  ```bash
  sudo a2ensite cv-projem.conf
  sudo systemctl restart apache2
  ```

**Not:** Her yeni site için benzersiz bir domain adı ve belge kök dizini kullanmalısınız.

---

**Ortamı Doğrulayın**

Eğer sanal konak (virtual host) ayarladıysanız, `laravel-api.test` gibi bir adresi yerel makinenizin `hosts` dosyasına eklemeniz gerekebilir.

- **Linux/macOS:** `sudo vi /etc/hosts`
- **Windows:** `C:\Windows\System32\drivers\etc\hosts`

`/etc/hosts` dosyasına şu satırı ekleyin:
`127.0.0.1   laravel-api.test`

**Laravel Uygulamasını Test Edin:**

Tarayıcınızı açın ve `http://laravel-api.test` adresine gidin. Ekranda Laravel başlangıç sayfasını görüyorsanız, kurulum başarıyla tamamlanmış demektir.
