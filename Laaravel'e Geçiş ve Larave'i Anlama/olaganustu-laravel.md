# Laravel'ın Olganüstü Özellikleri

Laravel’in zengin yapısı sayesinde birçok güçlü özellik var. Şimdiye kadar konuştuğumuz konular şunlardı:

- Migration, Seeder, Factory
- Helper, Cache, Redis
- composer dump-autoload
- Replication ve okuma/yazma ayrımı

Bunlara **benzeyen, ilgini çekecek** ve seni yazılım geliştirme sürecinde bir üst seviyeye taşıyabilecek bazı Laravel özelliklerini gruplar halinde aşağıda listeliyorum:

## Genel Özellikler Listesi

---

### 🧩 1. **Event & Listener Sistemi**

Laravel’in olay tabanlı yapısı ile, uygulamandaki aksiyonlara bağlı olarak işlem tetikleyebilirsin.

- `UserRegistered` gibi bir event tetiklenince → `SendWelcomeEmail` listener’ı çalışabilir.
- Kodun daha **modüler, izole ve test edilebilir** olur.
- Özellikle loglama, e-posta, bildirim gibi **arka plan işlemleri** için ideal.

---

### ⚙️ 2. **Queues (Kuyruklar)**

Zaman alan işlemleri **arka planda** çalıştırmak için kullanılır.

- Örn: Mail gönderimi, dış API çağrısı, büyük rapor işlemleri
- Desteklenen driver'lar: database, Redis, Amazon SQS
- `php artisan queue:work` ile arkaplan işlemleri yönetilir.

---

### 📥 3. **Jobs**

Queue'larla birlikte çalışır ama tek başına da kullanılabilir.

- Her işlem (iş mantığı) bir **Job sınıfına** yazılır.
- Örn: `ProcessInvoice`, `SendSMS`, `GenerateReport`

---

### 🧠 4. **Service Provider**

Tüm Laravel uygulamasının **başlangıç noktası**.
Özel sınıfları uygulamaya tanıtmak ve bağımlılıkları bağlamak için kullanılır.

---

### 🧱 5. **Service Container (IoC Container)**

- Laravel’in en temel altyapılarından biri.
- Sınıflar arası **bağımlılıkların çözülmesini** ve otomatik injection yapılmasını sağlar.

---

### 📚 6. **Policy & Gate – Yetkilendirme**

Roller, kullanıcı izinleri, detaylı yetkilendirme işlemleri için.

- `Gate::define()` → basit kontrol
- `Policy` sınıfları → model bazlı izin denetimi (örneğin `UserPolicy`, `PostPolicy`)

---

### 🛡️ 7. **Middleware**

Gelen istekleri filtrelemek için.

- Örn: authentication, role check, istek loglama
- Senin tanımladığın custom middleware’lerle iş akışını özelleştirebilirsin.

---

### 📊 8. **Eloquent Accessor & Mutator**

Model verileri okurken (`get`) ya da yazarken (`set`) değiştirmek için kullanılır.

- Örn: isim baş harfini büyük yapmak

```php
public function getNameAttribute($value) {
    return ucfirst($value);
}
```

---

### 🔐 9. **Rate Limiting**

API ya da kullanıcı işlemlerine **saniyede X istekten fazlasına** izin verme.

- Basit throttle middleware ile yapılabilir.
- Kullanıcı bazlı sınırlama tanımlanabilir.

---

### 🧬 10. **Custom Artisan Komutları**

Kendine özel `php artisan` komutları tanımlayabilirsin.

```bash
php artisan make:command GenerateReport
```

---

### 🧪 11. **Telescope (Geliştirici Paneli)**

Uygulamanın içini canlı izlemek için Laravel’in geliştirme aracı.

- Gelen/giden istekler
- Query logları
- Exception’lar
- Kullanıcılar, event’ler, jobs

---

### 🧩 12. **Livewire / Inertia.js (SPA Tabanlı Geliştirme)**

Vue ya da React bilmeden bile **modern etkileşimli arayüz** geliştirmene olanak tanır.

- Livewire → PHP temelli dinamik bileşen sistemi
- Inertia → Vue.js + Laravel birlikte çalışır

---

### 📑 13. **Form Request – Doğrulama ve Yetkilendirme**

Karmaşık validation kurallarını controller yerine ayrı sınıflarda yönetmeni sağlar.

```bash
php artisan make:request StoreUserRequest
```

---

### 📂 14. **File Storage (Dosya Yükleme)**

Laravel’in `Storage` sınıfı ile:

- Amazon S3, FTP, local disk gibi farklı sistemlere yükleme
- URL üretme
- Güvenli dosya erişimi

---

### 🧭 15. **Localization (Çok Dilli Destek)**

`resources/lang/tr/messages.php` gibi dosyalarla uygulamayı çok dillendirmek mümkün.

```php
__('messages.welcome') // Türkçe’de: “Hoş geldiniz”
```

---

Harika bir seçim yaptın! Bu dört konu Laravel’de uygulama kalitesini ve güvenliğini artırmak için çok önemli. Her birini **sırasıyla ve sade** bir şekilde anlatıyorum:

---

## KISACA: 1️⃣ LOGGING (Loglama)

**Amaç:**
Hataları, işlemleri ve özel olayları dosyaya, veritabanına ya da harici sistemlere yazmak.

### Laravel’de Loglama Nasıl Çalışır?

Laravel, Monolog kütüphanesini kullanır. Ayarlar `config/logging.php` dosyasındadır.

### En Yaygın Kullanım:

```php
use Illuminate\Support\Facades\Log;

Log::info('Kullanıcı giriş yaptı.', ['user_id' => 5]);
Log::warning('Yavaş sorgu tespit edildi');
Log::error('Veritabanı bağlantı hatası');
```

### Log Seviyeleri:

- `emergency` (sistem kullanılamaz)
- `alert`
- `critical`
- `error`
- `warning`
- `notice`
- `info`
- `debug`

### Log Nereye Kaydedilir?

Varsayılan olarak:
📂 `storage/logs/laravel.log`

### Diğer Log Driver'ları:

- `single`, `daily`, `slack`, `syslog`, `stderr`, `stack`

---

## KISACA: 2️⃣ MIDDLEWARE

**Amaç:**
İstek (request) uygulamaya ulaşmadan önce ya da cevap (response) dönerken **filtreleme** yapmak.

### Örnek Kullanım Alanları:

- Giriş yapmış mı?
- Admin mi?
- IP adresi engellenmiş mi?
- Token geçerli mi?

### Mevcut Middleware'lar:

Laravel'de zaten gelenler:

- `auth` → kullanıcı girişi gerekli
- `guest` → sadece çıkış yapmış kullanıcılar
- `throttle` → rate limit
- `verified` → e-posta onayı gerekli

### Kendi Middleware'ini Oluştur:

```bash
php artisan make:middleware CheckUserStatus
```

```php
public function handle($request, Closure $next)
{
    if (auth()->user()->banned) {
        abort(403, 'Hesabınız engellenmiş.');
    }

    return $next($request);
}
```

→ `app/Http/Kernel.php` içinde kaydedilir.

---

## KISACA: 3️⃣ RATE LIMITING (İstek Sınırlama)

**Amaç:**
API ya da genel rota üzerinden yapılan istekleri **belirli aralıklarla** sınırlandırmak. DDoS, spam ve kötüye kullanım risklerini azaltır.

### Laravel'de Kolay Kullanım:

```php
Route::middleware('throttle:60,1')->group(function () {
    // dakikada 60 istek
    Route::get('/api/products', 'ProductController@index');
});
```

### Yeni Laravel Rate Limiter Sistemi:

`RouteServiceProvider` içinden özelleştirilebilir:

```php
RateLimiter::for('custom-limit', function (Request $request) {
    return Limit::perMinute(20)->by($request->ip());
});
```

Ve kullanımı:

```php
Route::middleware('throttle:custom-limit')->get('/api/comments', 'CommentController@index');
```

---

## KISACA: 4️⃣ LOCALIZATION (Çok Dilli Destek)

**Amaç:**
Uygulamanın **birden fazla dile** destek vermesini sağlar. Genellikle `lang` klasörü üzerinden yönetilir.

### Klasör Yapısı:

```
resources
 └── lang
      ├── en
      │    └── messages.php
      └── tr
           └── messages.php
```

### Dosya Örneği:

**tr/messages.php**

```php
return [
    'welcome' => 'Hoş geldiniz!',
    'login_success' => 'Giriş başarılı.',
];
```

### Kullanımı:

```php
__('messages.welcome')  // "Hoş geldiniz!"
@lang('messages.login_success')
```

### Dil Değiştirme:

```php
App::setLocale('tr');
```

Genellikle kullanıcı oturumuna göre ya da URL’ye göre belirlenir:

```php
Route::get('/{locale}/anasayfa', function ($locale) {
    App::setLocale($locale);
    return view('home');
});
```
