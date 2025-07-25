# Middleware Nedir?

Laravel’in en güçlü özelliklerinden biri olan middleware kavramını detaylıca inceleyelim.

---

## ✅ **MIDDLEWARE NEDİR?**

Middleware, bir HTTP isteği uygulamaya ulaşmadan **önce** ya da uygulamadan çıktıktan **sonra** araya giren yazılım katmanıdır.

Genellikle şu amaçlarla kullanılır:

- Kimlik doğrulama (authentication)
- Yetkilendirme (authorization)
- IP filtreleme
- Giriş loglama
- CORS başlıkları ekleme
- Rate limiting

---

## 🧠 **Basit Tanım**

Bir HTTP isteği sunucuya gelir → Middleware kontrol eder → Uygulama çalışır → Middleware çıkışta tekrar devreye girebilir.

---

## 🧪 **Basit Örnek**

```php
public function handle($request, Closure $next)
{
    if (!Auth::check()) {
        return redirect('login');
    }

    return $next($request);
}
```

Bu örnekte kullanıcı giriş yapmamışsa `login` sayfasına yönlendiriliyor. Giriş yapılmışsa istek devam ediyor.

---

## 🛠️ **MIDDLEWARE NASIL OLUŞTURULUR?**

```bash
php artisan make:middleware CheckAdmin
```

```php
// app/Http/Middleware/CheckAdmin.php

public function handle($request, Closure $next)
{
    if (auth()->user()?->is_admin !== true) {
        abort(403, 'Yetkisiz erişim');
    }

    return $next($request);
}
```

---

## 🔗 **MIDDLEWARE NASIL KULLANILIR?**

### 1. **Route Üzerinden**

```php
Route::get('/panel', function () {
    return 'Yönetim Paneli';
})->middleware('check.admin');
```

### 2. **Controller Üzerinden**

```php
public function __construct()
{
    $this->middleware('check.admin');
}
```

### 3. **Kayıt Etme (kernel.php)**

```php
// app/Http/Kernel.php

protected $routeMiddleware = [
    'check.admin' => \App\Http\Middleware\CheckAdmin::class,
];
```

---

## 🧱 **LARAVEL’DE GELEN HAZIR MIDDLEWARE’LER**

| Middleware adı | Açıklama                                  |
| -------------- | ----------------------------------------- |
| `auth`         | Giriş yapılmış mı kontrol eder            |
| `guest`        | Giriş yapmamış kullanıcıları kontrol eder |
| `throttle`     | Hız sınırlama sağlar (rate limit)         |
| `verified`     | Email doğrulama yapılmış mı               |
| `signed`       | URL imzalanmış mı kontrol eder            |
| `csrf`         | CSRF koruması sağlar                      |

---

## 📌 **Gelişmiş Kullanım: Middleware ile Loglama**

```php
public function handle($request, Closure $next)
{
    Log::info('Gelen istek: ' . $request->path());

    $response = $next($request);

    Log::info('İstek sonucu: ' . $response->status());

    return $response;
}
```

---

## 🧩 **Middleware Zincirleme (Stacking)**

Bir route’a birden fazla middleware atanabilir:

```php
Route::get('/gizli', function () {
    return 'Gizli Sayfa';
})->middleware(['auth', 'verified', 'check.admin']);
```

---

## 📍 Middleware Kullanım Alanları (Gerçek Senaryolar)

| Kullanım Amacı           | Middleware adı / örnek              |
| ------------------------ | ----------------------------------- |
| Giriş kontrolü           | `auth`, `guest`                     |
| Admin sayfalarına erişim | `check.admin` (kendi middleware’in) |
| Mobil istemci kontrolü   | `CheckMobileClient`                 |
| API isteği sınırlandırma | `throttle:60,1`                     |
| Bakım modu kontrolü      | `PreventRequestsDuringMaintenance`  |

---
