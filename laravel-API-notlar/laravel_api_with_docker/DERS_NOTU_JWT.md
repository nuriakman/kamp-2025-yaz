# Ders Notu: Laravel API'ye JWT ile Kimlik Doğrulama Ekleme

## JWT (JSON Web Token) ile Kimlik Doğrulama Sistemi

Bu bölümde, daha önce oluşturduğumuz API'ye JWT (JSON Web Token) tabanlı bir kimlik doğrulama sistemi ekleyeceğiz. Bu sayede belirli endpoint'leri sadece giriş yapmış kullanıcıların erişimine açabileceğiz.

Kullanacağımız kütüphane: `tymon/jwt-auth`

### Bölüm 1: JWT Kütüphanesini Kurma ve Yapılandırma

**1. Kütüphaneyi Composer ile Projeye Ekleme**

İlk adım olarak, `tymon/jwt-auth` paketini projemize dahil edelim.

```bash
docker-compose exec app composer require tymon/jwt-auth
```

**2. Yapılandırma Dosyasını Yayınlama**

Kütüphanenin yapılandırma dosyasını `config` dizinine kopyalayalım. Bu dosya üzerinden token geçerlilik süresi gibi ayarları yönetebiliriz.

```bash
docker-compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

**3. JWT Gizli Anahtarını (Secret Key) Oluşturma**

JWT token'larını imzalamak için kullanılacak olan güvenli bir anahtar oluşturalım. Bu komut, `.env` dosyanıza `JWT_SECRET` adında bir değişken ekleyecektir.

```bash
docker-compose exec app php artisan jwt:secret
```

---

### Bölüm 2: Kullanıcı Modelini ve Yapılandırmayı Güncelleme

**1. `User` Modelini JWT için Hazırlama**

`app/Models/User.php` modelini, `tymon/jwt-auth` kütüphanesinin gerektirdiği arayüzü (interface) implemente edecek şekilde güncellemeliyiz. Bu, modele `getJWTIdentifier` ve `getJWTCustomClaims` metodlarını eklememizi gerektirir.

```bash
# User.php dosyasını açın ve aşağıdaki gibi güncelleyin:
docker-compose exec app nano app/Models/User.php
```

```php
// app/Models/User.php
namespace App\Models;

// ... diğer use ifadeleri
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    // ... mevcut içeriği koruyun

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

**2. Auth Guard'ını Yapılandırma**

Laravel'e varsayılan kimlik doğrulama mekanizması olarak `jwt` kullanmasını söylemeliyiz. `config/auth.php` dosyasını açın ve `guards` bölümünü güncelleyin.

```bash
# config/auth.php dosyasını açın:
docker-compose exec app nano config/auth.php
```

Dosyanın içinde `defaults` ve `guards` kısımlarını bulun ve aşağıdaki gibi değiştirin:

```php
// config/auth.php

'defaults' => [
    'guard' => 'api', // Varsayılan guard'ı api yap
    'passwords' => 'users',
],

// ...

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [ // api guard'ını jwt kullanacak şekilde ayarla
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

### Bölüm 3: Kimlik Doğrulama Controller'ı ve Rotaları

Kullanıcıların kayıt olmasını, giriş yapmasını, profilini görmesini ve çıkış yapmasını sağlayacak bir controller ve rotalar oluşturalım.

**1. `AuthController` Oluşturma**

```bash
docker-compose exec app php artisan make:controller Api/AuthController
```

`app/Http/Controllers/Api/AuthController.php` dosyasını açın ve içeriğini aşağıdaki gibi doldurun. Bu controller, `login`, `register`, `logout` ve `me` (profil) metodlarını içerecektir.

```php
// app/Http/Controllers/Api/AuthController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // login ve register dışındaki tüm metodlar için auth middleware'i uygula
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
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


    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60, // saniye cinsinden
            'user' => auth()->user()
        ]);
    }
}
```

**2. Auth Rotalarını Ekleme**

`routes/api.php` dosyasını açın ve `AuthController` için rotaları ekleyin.

```php
// routes/api.php
// ... mevcut rotaların altına ekleyin

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
});
```

---

### Bölüm 4: Korumalı Rotalar Oluşturma ve Test Etme

Artık `categories` ve `products` rotalarımızı sadece giriş yapmış kullanıcıların erişebilmesi için koruma altına alabiliriz.

**1. Rotaları Koruma Altına Alma**

`routes/api.php` dosyasında `apiResource` rotalarını bir `auth:api` middleware grubu içine alalım.

```php
// routes/api.php

// ... auth rotalarının altına veya üstüne ekleyebilirsiniz

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
});
```

**2. Postman ile Test Aşamaları**

1.  **Kayıt Ol (Register):**

    - **Metod:** `POST`
    - **URL:** `http://localhost:8001/api/auth/register`
    - **Body (JSON):**
      ```json
      {
        "name": "Nuri Test",
        "email": "nuri@test.com",
        "password": "123456",
        "password_confirmation": "123456"
      }
      ```

2.  **Giriş Yap (Login):**

    - **Metod:** `POST`
    - **URL:** `http://localhost:8001/api/auth/login`
    - **Body (JSON):**
      ```json
      {
        "email": "nuri@test.com",
        "password": "123456"
      }
      ```
    - **Yanıt:** Yanıttan gelen `access_token` değerini kopyalayın. Bu sizin JWT'nizdir.

3.  **Korumalı Rotalara Erişme (Örn: Kategorileri Listele):**

    - **Metod:** `GET`
    - **URL:** `http://localhost:8001/api/categories`
    - **Authorization Sekmesi:**
      - **Type:** Bearer Token
      - **Token:** Buraya bir önceki adımda kopyaladığınız `access_token` değerini yapıştırın.

    Eğer token olmadan istek atarsanız `401 Unauthorized` hatası alırsınız. Token ile istek attığınızda ise kategorilerin listesini başarıyla almalısınız.

Tebrikler! API'nizi JWT ile başarıyla güvence altına aldınız.
