# JWT Kullanımı

Laravel projende **JWT (JSON Web Token)** kullanarak kimlik doğrulama yapabilirsin — özellikle sadece **API tabanlı** bir yapı geliştiriyorsan bu yöntem son derece uygundur. Aşağıda Laravel'de JWT ile kimlik doğrulamanın detaylarını bulabilirsin.

---

## ✅ **JWT Nedir?**

JWT, kullanıcı oturumlarını **stateless** (sunucuda session tutmadan) yönetmek için kullanılan bir kimlik doğrulama yöntemidir. Her başarılı girişte bir token verilir, bu token her istekle birlikte sunucuya gönderilir. Sunucu token’ı doğrular ve kullanıcıyı tanır.

---

## 🛠️ Laravel’de JWT Kullanımı (Adım Adım)

### 1. 📦 Paket Kurulumu

En yaygın kullanılan JWT paketi: [`tymon/jwt-auth`](https://github.com/tymondesigns/jwt-auth)

```bash
composer require tymon/jwt-auth
```

### 2. 🧩 Servis Yayınla ve Config Dosyasını Al

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

Bu komut `config/jwt.php` dosyasını oluşturur.

---

### 3. 🔐 Anahtar Oluştur

JWT token imzalamak için gerekli:

```bash
php artisan jwt:secret
```

Bu `.env` dosyasına `JWT_SECRET` satırını ekler.

---

### 4. 🧑 User Modelini Ayarla

`app/Models/User.php` dosyasına `JWTSubject` interface'ini uygula:

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return $this->getKey(); // genellikle 'id'
    }

    public function getJWTCustomClaims()
    {
        return []; // ekstra veri eklemek istersen buraya
    }
}
```

---

### 5. 🛣️ API Route ve AuthController

#### 👉 `routes/api.php` içine örnek:

```php
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->get('/profile', [AuthController::class, 'me']);
```

#### 👉 Basit `AuthController`

```php
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Geçersiz giriş'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
```

---

### 6. 🛡️ `config/auth.php` Ayarı

```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

## 🔄 Token Yenileme, Çıkış Yapma vs.

- `auth()->refresh()` → Token yeniler
- `auth()->logout()` → Token geçersiz hale getirir

---

## 🎯 Avantajları

| Özellik      | Açıklama                                        |
| ------------ | ----------------------------------------------- |
| Stateless    | Sunucu tarafında session tutmaz, ölçeklenebilir |
| Mobil uyumlu | Mobil API’lerde yaygın kullanılır               |
| Güvenli      | İyi yapılandırılırsa güvenlidir                 |
| Hızlı        | Tek token ile doğrulama, session yok            |

---

## ❗ Dikkat Edilecekler

- Token süresi dolabilir → Refresh mekanizması gerekir
- Kullanıcı logout olduğunda token'ı blacklist'e alma (ekstra iş)
- Token'ı çalan biri kimlik taklidi yapabilir → HTTPS şart

---

## Alternatifler

| Yöntem           | Ne İçin Uygun?                          |
| ---------------- | --------------------------------------- |
| Laravel Sanctum  | SPA ve basit mobil uygulamalar          |
| Laravel Passport | OAuth2 tabanlı yetkilendirme sistemleri |
| JWT (tymon)      | Bağımsız mobil veya frontend API yapısı |

---

İstersen JWT ile çalışan küçük bir örnek proje yapısını da sıfırdan kurabiliriz (login, register, profil, token yenileme). Devam etmek ister misin?
