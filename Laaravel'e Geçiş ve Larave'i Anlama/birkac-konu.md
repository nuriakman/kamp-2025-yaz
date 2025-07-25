# Birkaç konu

## Sadece JSON dönen API

**Laravel’in sadece ve sadece JSON cevaplar dönecek şekilde ayarlanması mümkündür.** Bu, API odaklı projeler için çok yaygın bir ihtiyaçtır ve Laravel bu durumu destekler.

---

### 🔧 Amaç:

Laravel uygulaman **her zaman JSON** cevap versin:

- ✅ Başarılı isteklerde `application/json`
- ❌ Hatalı isteklerde bile JSON (örneğin 404, 500, Validation, Auth, vb.)

---

### ✅ Yapılacaklar (Adım Adım)

---

#### 1. `api` middleware grubunu kullan

`routes/api.php` dosyasındaki tüm rotalar zaten `api` middleware grubundadır ve bu grup `Accept: application/json` başlığını kontrol eder. Ama garanti altına almak için…

**Tüm API isteklerinde JSON beklediğini belirt:**

```http
Accept: application/json
```

Ancak bunu istemciye bırakmak istemiyorsan, aşağıdaki gibi Laravel tarafında garanti altına alabilirsin.

---

#### 2. **Exception Handler'da tüm hataları JSON’a çevir**

`app/Exceptions/Handler.php` dosyasını aç ve `render` metodunu override et:

```php
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

public function render($request, Throwable $exception): JsonResponse
{
    // Tüm hataları JSON olarak döndür
    return response()->json([
        'success' => false,
        'message' => $exception->getMessage(),
        'exception' => get_class($exception),
        'code' => $exception->getCode(),
    ], $this->getStatusCode($exception));
}

protected function getStatusCode(Throwable $exception): int
{
    if (method_exists($exception, 'getStatusCode')) {
        return $exception->getStatusCode();
    }

    return 500; // Varsayılan: sunucu hatası
}
```

Bu sayede 404, 403, 500 gibi tüm hatalar JSON dönecek.

---

#### 3. **Validation Exception’ı özel ele al**

Doğrulama (validation) hataları için ayrı dönüş yapmak gerekebilir. `Handler.php` içine şunu ekle:

```php
use Illuminate\Validation\ValidationException;

protected function invalidJson($request, ValidationException $exception)
{
    return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $exception->errors(),
    ], 422);
}
```

---

#### 4. CSRF, session, redirect gibi özelliklerden kaçın

Zaten `api.php` rotaları `web` middleware kullanmaz ama emin ol:

- `web.php` rotası olmasın
- `routes/api.php` dışına çıkma
- `auth:sanctum` gibi bazı middleware'ler bazen redirect dönebilir — test et

---

### 🔍 Test: 404 örneği

İstemciden yanlış bir endpoint çağır:

```bash
GET /api/olmayan-bir-endpoint
```

Beklenen cevap:

```json
{
  "success": false,
  "message": "Not Found",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
  "code": 0
}
```

---

### 🔐 Ekstra: Laravel Passport veya JWT kullanıyorsan

Bazı authentication hataları `redirect` olarak dönebilir. `Handler.php` içine şunu ekleyebilirsin:

```php
use Illuminate\Auth\AuthenticationException;

protected function unauthenticated($request, AuthenticationException $exception)
{
    return response()->json([
        'success' => false,
        'message' => 'Unauthenticated.',
    ], 401);
}
```

---

### 🧩 İsteğe Bağlı: Response helper kullan

Kendi JSON response yapını merkezi bir helper’a taşıyabilirsin. Örneğin:

```php
function api_response($data = [], $message = '', $status = 200)
{
    return response()->json([
        'success' => $status < 400,
        'message' => $message,
        'data' => $data,
    ], $status);
}
```

---

### ✅ Sonuç

Laravel’de sadece JSON cevap döndürmek için:

| Yapılacak                                                 | Açıklama                                      |
| --------------------------------------------------------- | --------------------------------------------- |
| `api.php` rotalarını kullan                               | `web.php` ile karışma                         |
| `Handler.php` içinde `render` override et                 | Hataları JSON döndür                          |
| `invalidJson`, `unauthenticated` gibi özel durumları işle | Laravel’in varsayılan redirect'lerini engelle |
| Test et: 404, 500, auth, validation                       | Tüm hatalarda JSON geliyor mu?                |

## jwt kullanacağım, sanctum u projeden komple kaldırsam nasıl olur

JWT kullanacaksan ve Laravel Sanctum'u **hiç kullanmayacaksan**, projeden tamamen **kaldırman mümkündür ve temiz bir tercih olur.** Aşağıda adım adım nasıl kaldıracağını anlatıyorum:

---

### 🧼 1. Sanctum Paketini Kaldır

Eğer `composer.json` içinde `laravel/sanctum` varsa, şu komutla kaldır:

```bash
composer remove laravel/sanctum
```

> Bu, `vendor` klasöründen da siler ve autoload dosyalarını günceller.

---

### 🧽 2. `config/sanctum.php` dosyasını sil

```bash
rm config/sanctum.php
```

> Bu dosya artık işe yaramaz ve kaldırılabilir.

---

### 🧼 3. Middleware'den Sanctum'u Kaldır

#### `app/Http/Kernel.php` içinde şu satırı bul ve sil:

```php
\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
```

Bu genellikle `api` middleware grubunda görünürse:

```php
'api' => [
    // bunu sil:
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

### 🧼 4. Sanctum Migration’larını Sil

Eğer `database/migrations` klasöründe şöyle bir dosya varsa:

```php
2020_XX_XX_000000_create_personal_access_tokens_table.php
```

> Sil veya migration'ı hiç çalıştırmadıysan bırak gitsin. Çalıştırmışsan:

Veritabanından tabloyu da kaldır:

```sql
DROP TABLE personal_access_tokens;
```

---

### 🔍 5. Kodlardan `auth:sanctum` Kullanımlarını Kaldır

Örneğin şu şekilde kullanılmış middleware'leri **jwt ile değiştir**:

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

→ Değiştir:

```php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

> JWT kullanıyorsan, `auth:api` genelde JWT guard'dır.

---

### ✅ JWT Kullanımı İçin Hazırlık

JWT paketi için en çok kullanılan çözüm:

```bash
composer require tymon/jwt-auth
```

Ardından:

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

Ve `config/auth.php` içinde `api` guard'ını `jwt` olarak ayarla:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

### 🎯 Sonuç

| İşlem                                | Açıklama                    |
| ------------------------------------ | --------------------------- |
| `composer remove laravel/sanctum`    | Paketi kaldır               |
| `Kernel.php` ve `auth.php` temizliği | Middleware ve guard temizle |
| Migration ve config dosyaları        | Sil                         |
| `auth:sanctum` → `auth:api`          | Güncelle                    |

Böylece Laravel'de **sadece JWT ile kimlik doğrulama yapan**, tamamen **Sanctum’suz bir API** yapısına geçebilirsin.

## Laravel projesini yayına almak (production deployment)

Laravel projesini **yayına almak (production deployment)** birçok adımı içerir. Bu adımlar, projenin güvenli, hızlı ve hatasız çalışmasını sağlar. Aşağıda Laravel projesini yayına alma sürecini detaylı ve adım adım anlattım:

---

### 1. Sunucu Seçimi ve Hazırlığı

#### ✔️ Sunucu Türü:

- Paylaşımlı hosting (Laravel için uygun olmayan)
- VPS / Dedicated Server (Ubuntu, CentOS gibi Linux dağıtımları önerilir)
- Bulut Sunucuları (DigitalOcean, AWS, Linode, Google Cloud, vs.)

#### ✔️ Gerekli Yazılımlar:

- PHP (Laravel sürümüne uygun, örn. PHP 8.1+)
- Web Server (Nginx veya Apache)
- Database (MySQL, MariaDB, PostgreSQL vb.)
- Composer
- Git (opsiyonel, deploy için)
- Node.js + npm/yarn (frontend asset için, eğer frontend build yapılacaksa)

---

### 2. Proje Dosyalarının Sunucuya Aktarımı

#### Yöntemler:

- Git ile çekme (SSH erişimi varsa en pratik)
- FTP/SFTP ile dosya yükleme
- CI/CD sistemleri ile otomatik deploy (GitHub Actions, GitLab CI, vs.)

---

### 3. Proje Ortam Ayarları (`.env`)

- `.env` dosyasını **prod ortamına göre düzenle:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prod_db
DB_USERNAME=prod_user
DB_PASSWORD=secret

CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailprovider.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
```

- `.env` dosyasını kesinlikle **gizli tut** ve sürüm kontrolüne dahil etme.

---

### 4. Composer Bağımlılıklarının Kurulumu

Sunucuda proje klasöründe:

```bash
composer install --optimize-autoloader --no-dev
```

- `--no-dev`: Geliştirme bağımlılıklarını yüklemez.
- `--optimize-autoloader`: Performans artırır.

---

### 5. Cache ve Config Optimizasyonu

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Bu komutlar uygulamanın konfigürasyon, route ve view dosyalarını önbelleğe alır, hızlandırır.

---

### 6. Veritabanı Migrasyonları ve Seed

```bash
php artisan migrate --force
#php artisan db:seed --force  # seed varsa
```

- `--force` üretim ortamında migration için zorunludur.

---

### 7. Dosya İzinleri

- `storage` ve `bootstrap/cache` dizinlerine yazma izinleri verilmeli:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

(`www-data` Nginx/Apache kullanıcı adı olabilir, sunucuya göre değişir)

---

### 8. Web Server Ayarları

#### Nginx Örnek Konfigürasyon

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/yourproject/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        #fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # PHP versiyonuna göre
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### Apache için `.htaccess` Laravel içinde zaten var, ekstra ayar genelde gerekmez.

---

### 9. HTTPS Sertifikası

- Let's Encrypt ile ücretsiz SSL sertifikası kur:

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

- HTTPS’yi zorunlu kılmak için Laravel içinde:

```php
// AppServiceProvider.php içindeki boot() methodunda:
if ($this->app->environment('production')) {
    \URL::forceScheme('https');
}
```

---

### 10. Queue (İş Kuyruğu) ve Scheduler (Zamanlayıcı)

- Kuyruk kullanıyorsan (örn. mail gönderme), Supervisor ile queue worker’ı ayarla.
- `crontab -e` ile Laravel Scheduler’ı ekle:

```cron
* * * * * cd /var/www/yourproject && php artisan schedule:run >> /dev/null 2>&1
```

---

### 11. Log Yönetimi

- Log dosyalarının boyutunu yönet (logrotate ile)
- Hataları hızlı görebilmek için Sentry, Bugsnag gibi servisler entegre edilebilir.

---

### 12. Performans ve Güvenlik Kontrolleri

- `APP_DEBUG=false` kesinlikle aktif olsun
- `APP_KEY` `.env` dosyasında mutlaka ayarlı olsun (`php artisan key:generate`)
- Laravel versiyonunu güncel tut
- Sunucu güvenlik duvarı, SSH erişimi gibi konuları düzenle

---

### Özet Tablo

| Aşama                | Komut / İşlem                                     |
| -------------------- | ------------------------------------------------- |
| Dosya yükle          | Git/FTP/CI ile                                    |
| Bağımlılık yükle     | `composer install --no-dev --optimize-autoloader` |
| Çevresel değişkenler | `.env` dosyası ayarla                             |
| İzinleri ayarla      | `storage`, `bootstrap/cache` izinleri             |
| Migrasyon çalıştır   | `php artisan migrate --force`                     |
| Cache oluştur        | `config:cache`, `route:cache`, `view:cache`       |
| Web server ayarları  | Nginx/Apache konfigürasyon                        |
| SSL Sertifikası      | Let's Encrypt / Diğer                             |
| Kuyruk ve Scheduler  | Supervisor, Crontab ayarları                      |

---

İstersen **sunucu türüne göre (DigitalOcean, AWS, vs.) detaylı deploy adımlarını**, **CI/CD entegrasyonlarını** veya **Docker ile Laravel deploy örneği** verebilirim. Yardımcı olayım mı?
