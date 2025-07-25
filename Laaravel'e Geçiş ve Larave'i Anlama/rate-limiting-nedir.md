# Rate Limit Nedir?

## ✅ **RATE LIMITING NEDİR?**

Rate limiting, bir kullanıcıya belirli bir zaman dilimi içinde **belirli sayıda istek hakkı tanımlamak** anlamına gelir.

Amaç:

- Sunucuyu aşırı yüklenmeye karşı korumak
- API kötüye kullanımını önlemek
- Brute force saldırılarına karşı koruma sağlamak

---

## 🔐 **KULLANIM ALANLARI**

- API uç noktalarında erişimi sınırlamak
- Giriş formu gibi hassas alanlarda deneme sayısını kontrol altına almak
- IP başına kısıtlama getirmek

---

## ⚙️ **LARAVEL’DE KULLANIM**

### 📌 Örnek: Route Üzerinden

```php
Route::middleware('throttle:60,1')->get('/api/data', function () {
    return 'Veri';
});
```

> Bu örnekte: `60` istek / `1` dakika

---

## 🔧 **RATE LIMIT MANTIKLI BİR ŞEKİLDE NASIL ÇALIŞIR?**

1. Laravel, kullanıcıyı **IP adresine** veya **kimlik bilgisine** göre tanımlar.
2. Her istek yapıldığında bir sayaç artar.
3. Sayaç belirtilen süre sonunda sıfırlanır.

---

## 📁 **KÜRESEL AYARLAR (GLOBAL LIMITS)**

`app/Providers/RouteServiceProvider.php` içinde API rate limit varsayılanı tanımlanabilir:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

> Bu örnekte:
>
> - Giriş yapılmışsa kullanıcı ID’ye göre
> - Giriş yapılmamışsa IP adresine göre sınır koyar

---

## 🧪 **ÖRNEK: BRUTE FORCE KORUMASI**

```php
RateLimiter::for('login', function (Request $request) {
    $email = (string) $request->email;

    return Limit::perMinute(5)->by($email . $request->ip());
});
```

---

## 🧩 **ROUTE'DA KULLANIM (isimle)**

```php
Route::post('/login', function () {
    // ...
})->middleware('throttle:login');
```

---

## ⏳ **İSTEK LİMİTİ AŞILIRSA NE OLUR?**

Laravel otomatik olarak `429 Too Many Requests` HTTP hatasını döner ve şunu içerir:

- Retry-After başlığı (kaç saniye sonra yeniden denenebilir)
- JSON hata mesajı

---

## ⚠️ **DİKKAT EDİLECEKLER**

| Durum                                       | Çözüm                                         |
| ------------------------------------------- | --------------------------------------------- |
| Gerçek kullanıcılar da engellenmesin        | Limitler makul olmalı (örneğin 100/1dk)       |
| Kullanıcıya bilgi verilmeli                 | 429 durum kodu loglanabilir                   |
| Admin ya da VIP kullanıcılar muaf tutulmalı | RateLimiter’da kullanıcı kontrolü yapılabilir |

---

## 🧱 **ÖZET**

| Özellik       | Açıklama                        |
| ------------- | ------------------------------- |
| `throttle`    | Middleware ile hız sınırlama    |
| `RateLimiter` | Programatik kontrol             |
| `429`         | Limit aşımında dönen durum kodu |

---
