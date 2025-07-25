# Sadece API için Laravel

Laravel’i sadece **API** olarak kullanmak istiyorsan (yani sadece veri sunan bir backend olarak), bazı yapılandırma, paket ve güvenlik konularına özellikle dikkat etmen gerekir. Aşağıda **Laravel ile sadece API geliştirmek için dikkat edilmesi gerekenleri** detaylı ve mantıklı gruplar halinde anlatıyorum:

---

## 🔧 1. **Kurulum ve Başlangıç Yapılandırması**

### ✅ `--api` seçeneği ile kurulum önerilir:

```bash
laravel new proje-adi --api
```

Bu, gereksiz Blade, session, CSRF gibi web özelliklerini devre dışı bırakır.

### ✅ Veya `.env` ve `config/session.php`, `config/auth.php` gibi ayarları API moduna göre düzenle.

---

## 🧱 2. **Route Ayarları (`api.php`)**

### ✅ Sadece `routes/api.php` kullan:

```php
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());
```

### ✅ Web routes (`web.php`) kullanma, session, view vs. zaten gerekmez.

---

## 🔐 3. **Kimlik Doğrulama (Authentication)**

### ✅ En yaygın yöntemler:

- **Laravel Sanctum** → SPA & Mobil için ideal.
- **Laravel Passport** → OAuth2 isteyen sistemlerde.

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Middleware’e `auth:sanctum` eklersin.

---

## 🧪 4. **API Response Standartı**

### ✅ Tek tip JSON çıktı formatı kullan (tüm API’lerde tutarlılık):

```json
{
  "status": "success",
  "data": { ... },
  "message": "İşlem başarılı"
}
```

Bunu kolaylaştırmak için `ApiResponse` helper veya trait yazabilirsin.

---

## 🧯 5. **Exception Handling & Validation**

### ✅ `app/Exceptions/Handler.php` içinde `render` fonksiyonunu özelleştir:

```php
public function render($request, Throwable $exception)
{
    if ($request->is('api/*')) {
        return response()->json([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ], 500);
    }

    return parent::render($request, $exception);
}
```

### ✅ Validation:

```php
$request->validate([
    'email' => 'required|email',
]);
```

veya FormRequest sınıfları kullan.

---

## 🔒 6. **Rate Limiting (Oran Sınırlama)**

### ✅ `api` middleware grubunda varsayılan olarak var (`throttle:api`):

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/veri', 'ApiController@get');
});
```

Bu, **dakikada 60 istek** anlamına gelir.

---

## 🌍 7. **CORS (Cross-Origin Resource Sharing)**

Frontend başka bir domain'deyse (örneğin Quasar veya Vue/React), CORS ayarlarını yapmalısın.

```bash
composer require fruitcake/laravel-cors
```

veya Laravel 7+ sürümlerinde `app/Http/Middleware/HandleCors.php` ve `config/cors.php` kullan.

---

## 🚀 8. **Performans ve Cache**

- **Route cache:** `php artisan route:cache`
- **Config cache:** `php artisan config:cache`
- **Response cache:** Bazı sabit cevapları `Cache::remember()` ile tutabilirsin.
- **Eager Loading:** İlişkili modelleri önceden yükle (`with()`), N+1 sorununu önle.

---

## 🔄 9. **JSON Resource ve Resource Collection**

API çıktısını daha düzenli hale getirmek için:

```bash
php artisan make:resource UserResource
```

```php
return new UserResource($user);
```

İlişkili modelleri sadeleştirmek, veri saklamak ve dönüşleri tekleştirmek için çok faydalıdır.

---

## 📄 10. **API Dokümantasyonu (Swagger/OpenAPI)**

Gerçek API projelerinde vazgeçilmezdir:

```bash
composer require "darkaonline/l5-swagger"
```

veya `scribe`, `openapi`, `stoplight` gibi araçlar kullan.

---

## 👮‍♂️ 11. **Middleware Kullanımı**

- JWT kontrolü
- IP sınırlama
- Rate limit
- JSON istek zorlaması
- Loglama

---

## ✅ Özet Tablo

| Konu              | Açıklama                                  |
| ----------------- | ----------------------------------------- |
| Blade, Web Routes | Kullanılmaz                               |
| Middleware        | `api`, `auth:sanctum`, `throttle`         |
| Session, Cookie   | Gerekmez (stateless yapı)                 |
| Auth yöntemi      | `sanctum` veya `passport`                 |
| CORS              | Önemli (özellikle frontend farklıysa)     |
| Response Formatı  | JSON, tek tip                             |
| Loglama           | JSON ile loglamak veya veritabanına       |
| Hata Yönetimi     | API uyumlu `try/catch` + exception render |
| Rate Limit        | throttle middleware ile                   |
| Dökümantasyon     | Swagger, Scribe gibi paketlerle           |

---
