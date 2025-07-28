# Laravel JWT Authentication Ders Notu

## ÖZET

```bash
# Proje dizinine geçelim, bir kopyalasını oluşturalım
cd laravel_api
cd ..
cp -r laravel_api laravel_api_jwt
cd laravel_api_jwt

# JWT için kütüphaneyi ekleyelim
composer require php-open-source-saver/jwt-auth

# JWT için yapılandırma dosyasını yayınlama
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"

# JWT için gizli anahtar oluştur
php artisan jwt:secret

# User modelini güncelle
# app/Models/User.php dosyasını güncelle

# Auth guard'ını yapılandırma dosyasına ekle
# config/auth.php dosyasını güncelle

# AuthController oluştur
php artisan make:controller Api/AuthController

# AuthController için rotaları ekle
# routes/api.php dosyasını güncelle

# composer.json dosyasını güncelle
composer dump-autoload
php artisan config:clear
php artisan cache:clear

# Geliştirme sunucusunu başlat
php artisan serve --host=0.0.0.0 --port=8000

# Rotaları kontrol et
php artisan route:list

# Postman ile test et
# URL: http://localhost:8000/api/categories
```

## Giriş

Bu ders notunda Laravel projesine `php-open-source-saver/jwt-auth` paketi kullanarak JWT (JSON Web Token) authentication sistemi nasıl entegre edileceğini adım adım öğreneceksiniz.

## Gereksinimler

- Laravel 12.x projesi
- PHP 8.2+
- Composer
- Veritabanı (MySQL, SQLite, vb.)

## Adım 1: JWT Paketini Yükleme

Öncelikle `php-open-source-saver/jwt-auth` paketini projenize yükleyin:

```bash
composer require php-open-source-saver/jwt-auth
```

Bu komut paketi ve bağımlılıklarını yükleyecektir.

## Adım 2: JWT Konfigürasyonunu Yayınlama

JWT konfigürasyon dosyasını projenize kopyalayın:

```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
```

Bu komut `config/jwt.php` dosyasını oluşturacaktır.

## Adım 3: JWT Secret Key Oluşturma

JWT token'ları imzalamak için secret key oluşturun:

```bash
php artisan jwt:secret
```

Bu komut `.env` dosyanıza `JWT_SECRET` anahtarını ekleyecektir.

## Adım 4: User Modelini Güncelleme

`app/Models/User.php` dosyasını açın ve aşağıdaki değişiklikleri yapın:

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

**Önemli değişiklikler:**

- `JWTSubject` interface'ini import ettik
- `User` sınıfı `JWTSubject` interface'ini implement ediyor
- `getJWTIdentifier()` ve `getJWTCustomClaims()` metodları eklendi

## Adım 5: Auth Konfigürasyonunu Güncelleme

`config/auth.php` dosyasını açın, `guards` ve `defaults` başlıklarında aşağıdaki gibi düzenlemeleri yapın:

**İşlem 1/2:**

```bash
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],
```

**İşlem 2/2:**

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

## Adım 6: JWT Middleware'lerini Ekleme

`bootstrap/app.php` dosyasını açın ve middleware'leri ekleyin:

```php
<?php
// bootstrap/app.php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Web rotalarının dosya yolu.
        web: __DIR__ . '/../routes/web.php',

        // API rotalarının dosya yolu.
        api: __DIR__ . '/../routes/api.php',

        // API rotalarının prefix'ini belirtir.
        apiPrefix: 'api',

        // Komut dosyasının dosya yolu.
        commands: __DIR__ . '/../routes/console.php',

        // Sağlık rotalarının dosya yolu.
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

## Adım 7: AuthController Oluşturma

`app/Http/Controllers/Api/AuthController.php` dosyasını oluşturun:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware route seviyesinde tanımlandı
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = Auth::guard('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => Hash::make($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return $this->createNewToken($newToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token could not be refreshed'], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }
}
```

## Adım 8: API Route'larını Güncelleme

`routes/api.php` dosyasını güncelleyin:

```php
<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TestDataController;
use App\Http\Controllers\Api\AuthController;

// Authentication routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'userProfile'])->middleware('auth:api');
});

// Protected routes
Route::group([
    'middleware' => 'auth:api'
], function () {
    // Kategori ve Ürünler için API kaynak rotaları
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);

    // Test verileri oluşturmak için rotası
    Route::post('/test-data', [TestDataController::class, 'createTestData']);
});
```

## Adım 9: Veritabanı Migration'ını Çalıştırma

Eğer henüz çalıştırmadıysanız, veritabanı migration'larını çalıştırın:

```bash
php artisan migrate
```

## Adım 10: Sistemi Test Etme

### Development Server'ı Başlatma

```bash
php artisan serve
```

### 1. Kullanıcı Kaydı Testi

```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Beklenen Yanıt:**

```json
{
  "message": "User successfully registered",
  "user": {
    "name": "Test User",
    "email": "test@example.com",
    "updated_at": "2025-07-26T13:39:41.000000Z",
    "created_at": "2025-07-26T13:39:41.000000Z",
    "id": 1
  }
}
```

### 2. Kullanıcı Girişi Testi

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Beklenen Yanıt:**

```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "email_verified_at": null,
    "created_at": "2025-07-26T13:39:41.000000Z",
    "updated_at": "2025-07-26T13:39:41.000000Z"
  }
}
```

### 3. Kullanıcı Profili Testi (JWT Token ile)

```bash
curl -X GET http://127.0.0.1:8000/api/auth/me \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

### 4. Korumalı Endpoint Testi

```bash
curl -X GET http://127.0.0.1:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

### 5. Token Olmadan Erişim Testi

```bash
curl -X GET http://127.0.0.1:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Beklenen Yanıt:**

```json
{
  "message": "Unauthenticated."
}
```

### 6. Çıkış Testi

```bash
curl -X POST http://127.0.0.1:8000/api/auth/logout \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

### 7. Token Yenileme Testi

```bash
curl -X POST http://127.0.0.1:8000/api/auth/refresh \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

## Endpoint'ler Özeti

### Authentication Endpoint'leri (Korumasız)

- `POST /api/auth/register` - Kullanıcı kaydı
- `POST /api/auth/login` - Kullanıcı girişi

### Authentication Endpoint'leri (JWT Korumalı)

- `GET /api/auth/me` - Kullanıcı profili
- `POST /api/auth/logout` - Çıkış
- `POST /api/auth/refresh` - Token yenileme

### Korumalı API Endpoint'leri

- `GET /api/categories` - Kategoriler listesi
- `POST /api/categories` - Yeni kategori oluşturma
- `GET /api/categories/{id}` - Kategori detayı
- `PUT /api/categories/{id}` - Kategori güncelleme
- `DELETE /api/categories/{id}` - Kategori silme
- `GET /api/products` - Ürünler listesi
- `POST /api/products` - Yeni ürün oluşturma
- `GET /api/products/{id}` - Ürün detayı
- `PUT /api/products/{id}` - Ürün güncelleme
- `DELETE /api/products/{id}` - Ürün silme

## SORUN ÇIKARSA

```bash
# composer.json dosyasını güncelle
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

## Postman ile JWT Test Aşaması

Postman uygulamasını açın ve aşağıdaki adımları izleyin:

1. **Kayıt Ol (Register):**

   - **Metod:** `POST`
   - **URL:** `http://localhost:8000/api/auth/register`
   - **Body (JSON):**
     ```json
     {
       "name": "Test User",
       "email": "test@example.com",
       "password": "password123",
       "password_confirmation": "password123"
     }
     ```

2. **Giriş Yap (Login):**

   - **Metod:** `POST`
   - **URL:** `http://localhost:8000/api/auth/login`
   - **Body (JSON):**
     ```json
     {
       "email": "test@example.com",
       "password": "password123"
     }
     ```
   - **Yanıt:** Yanıttan gelen `access_token` değerini kopyalayın. Bu sizin JWT'nizdir.

3. **Korumalı Rotalara Erişme (Örn: Kategorileri Listele):**

   - **Metod:** `GET`
   - **URL:** `http://localhost:8000/api/categories`
   - **Authorization Sekmesi:**
     - **Type:** Bearer Token
     - **Token:** Buraya bir önceki adımda kopyaladığınız `access_token` değerini yapıştırın.

Eğer token olmadan istek atarsanız `401 Unauthorized` hatası alırsınız. Token ile istek attığınızda ise kategorilerin listesini başarıyla almalısınız.

Tebrikler! API'nizi JWT ile başarıyla güvence altına aldınız.

## cURL ile JWT Testi

Terminal üzerinden de JWT işlemlerinizi test edebilirsiniz. İşte örnek komutlar:

1. **Kayıt Olma (Register):**

   ```bash
   curl -X POST http://localhost:8000/api/auth/register \
   -H "Content-Type: application/json" \
   -d '{
       "name": "Test User",
       "email": "test@example.com",
       "password": "password123",
       "password_confirmation": "password123"
   }'
   ```

2. **Giriş Yapma (Login):**

   ```bash
   curl -X POST http://localhost:8000/api/auth/login \
   -H "Content-Type: application/json" \
   -d '{
       "email": "test@example.com",
       "password": "password123"
   }'
   ```

   - Bu komut size bir `access_token` döndürecektir. Bu token'ı kopyalayın.

3. **Korumalı Rotalara Erişim (Örn: Kategorileri Listeleme):**
   ```bash
   curl -X GET http://localhost:8000/api/categories \
   -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
   -H "Accept: application/json"
   ```
   - `YOUR_ACCESS_TOKEN` kısmına giriş yaparken aldığınız token'ı yazın.

Eğer token geçersiz veya süresi dolmuşsa `401 Unauthorized` hatası alırsınız. Token geçerliyse kategorilerin JSON formatındaki listesini görürsünüz.

## Önemli Notlar

1. **Header'lar**: Tüm API isteklerinde `Accept: application/json` header'ı gereklidir.

2. **Token Süresi**: JWT token'lar varsayılan olarak 1 saat (3600 saniye) geçerlidir.

3. **Authorization Header**: Korumalı endpoint'lere erişim için `Authorization: Bearer {token}` header'ı kullanılmalıdır.

4. **Hata Yönetimi**: Token olmadan korumalı endpoint'lere erişim "Unauthenticated" hatası döndürür.

5. **Token Yenileme**: Token süresi dolmadan önce `/api/auth/refresh` endpoint'i ile yenilenebilir.

## Güvenlik Önerileri

1. **HTTPS Kullanımı**: Production ortamında mutlaka HTTPS kullanın.

2. **Token Saklama**: Client tarafında token'ları güvenli bir şekilde saklayın (localStorage yerine httpOnly cookie tercih edilebilir).

3. **Token Süresi**: Production ortamında token süresini ihtiyaca göre ayarlayın.

4. **Rate Limiting**: API endpoint'lerine rate limiting uygulayın.

5. **Validation**: Tüm input'ları validate edin.

## Sonuç

Bu ders notunu takip ederek Laravel projenize başarıyla JWT authentication sistemi entegre edebilirsiniz. Sistem tam fonksiyonel olup, kullanıcı kaydı, girişi, çıkışı, token yenileme ve korumalı endpoint'lere erişim özelliklerini desteklemektedir.
