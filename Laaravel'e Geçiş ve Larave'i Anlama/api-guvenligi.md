# API geliştirirken güvenli kod yazmak

Laravel ile **API geliştirirken güvenli kod yazmak**, hem kullanıcı verilerini korumak hem de sistemin istikrarını sağlamak açısından çok önemlidir.

Aşağıda, Laravel API'lerinde **güvenlik açısından dikkat edilmesi gereken konuları** sade ve sistematik şekilde açıklıyorum:

---

## 🔐 1. Kimlik Doğrulama (Authentication)

### ✅ JWT / Sanctum / Passport

- **JWT** (JSON Web Token): Mobil uygulamalar ve frontend (Vue/React) için uygun. Stateless.
- **Sanctum**: Laravel’in dahili paketi, SPA ve mobil için hafif, kullanımı kolay.
- **Passport**: OAuth2 tabanlı, büyük ve kapsamlı projeler için.

> 👇 Öneri: Eğer microservice veya mobil API geliştiriyorsan **JWT** tercih etmen mantıklı.

---

## 🔒 2. Yetkilendirme (Authorization)

- `Gate` veya `Policy` ile hangi kullanıcı ne yapabilir kontrol et.
- Role/Permission yapısı oluştur (örneğin `spatie/laravel-permission` paketi).

```php
$this->authorize('delete', $post);
```

---

## 🧾 3. Rate Limiting (Hız Sınırı Koymak)

Kötü niyetli isteklerin önüne geçmek için `throttle` middleware'ini kullan.

```php
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('/user', 'UserController@info');
});
```

> Bu örnek: Her IP 1 dakika içinde en fazla 60 istek atabilir.

---

## 🧼 4. Girdi Temizleme ve Doğrulama

- `Request` sınıfında validation kuralları belirle.
- `strip_tags()`, `htmlspecialchars()` gibi yöntemlerle zararlı kodları temizle.
- Gerekirse `mews/purifier` gibi XSS temizleme paketi kullan.

```php
$request->validate([
    'name' => 'required|string|max:255',
]);
```

---

## 🔐 5. CSRF Koruması

- **API rotaları genellikle `api` middleware grubunda olduğu için CSRF koruması gerekmez**.
- Ancak frontend ile beraber (örneğin SPA) çalışıyorsa, CSRF token ile koruma sağlayabilirsin.

---

## 🔏 6. HTTPS Zorunluluğu

- Tüm API iletişimi **HTTPS üzerinden** olmalı.
- `.env` dosyasında `APP_URL=https://yourdomain.com` olmalı.
- Gerekirse `AppServiceProvider` içinde HTTPS zorunluluğu ekle:

```php
if (env('APP_ENV') === 'production') {
    \URL::forceScheme('https');
}
```

---

## 🧾 7. Özel Hata Mesajları ve Logging

- Hatalarda kullanıcıya teknik detay değil, sade mesaj ver.
- Gerçek hata log'larını `storage/logs/` altında tut.
- Saldırı şüphesi varsa `log()` ile kayıt al.

---

## 🧠 8. Açıkta Kalan Endpoint’leri Engelle

- Gereksiz `index`, `create`, `show`, `update` rotalarını kapat.
- `apiResource` kullanıyorsan `only` ve `except` ile sınırla:

```php
Route::apiResource('users', UserController::class)->only(['index', 'store']);
```

---

## 🧱 9. CORS (Cross-Origin Resource Sharing)

- API'ye başka domain’lerden erişilecekse, **izin verilen domainleri sınırla**.

```bash
composer require fruitcake/laravel-cors
```

`config/cors.php`:

```php
'allowed_origins' => ['https://frontend-app.com'],
```

---

## 🕵️ 10. Loglama ve İzleme

- Laravel log sistemini aktif kullan.
- Giriş denemeleri, şüpheli hareketler, veri silme gibi işlemleri kaydet.
- Gerekirse `Sentry`, `Bugsnag`, `Logflare` gibi log izleme sistemleri entegre et.

---

## ✅ Ekstra Güvenlik Önlemleri

| Önlem                 | Açıklama                                                           |
| --------------------- | ------------------------------------------------------------------ |
| **API Key**           | Uç noktalara erişim için sabit token doğrulaması                   |
| **Request Signature** | İsteğin hash ile imzalanması (örneğin webhook kontrolü)            |
| **Input Size Limit**  | Büyük dosya / istek boyutu sınırla                                 |
| **Firewall**          | WAF veya Laravel taraflı IP sınırlama                              |
| **Security Headers**  | Response başlıklarıyla XSS, iframe, mime türü vb. korumaları artır |

---

## 📌 Sonuç

**Laravel API güvenliği** için dikkat etmen gereken başlıca noktalar şunlardır:

1. Kimlik doğrulama (JWT/Sanctum)
2. Yetkilendirme (Policy / Permission)
3. Hız sınırı (Rate Limiting)
4. Girdi doğrulama ve filtreleme
5. HTTPS zorunluluğu
6. CORS ayarları
7. Güvenli hata mesajları
8. Gereksiz endpoint’leri engelle
9. Loglama ve saldırı izleme

---
