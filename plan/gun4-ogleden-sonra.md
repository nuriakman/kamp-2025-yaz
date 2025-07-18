# Gün 4 - Öğleden Sonra Oturumu: Laravel ile REST API Geliştirme

## 1. API Controller İşlemleri

### 1.1 API Resource Oluşturma

```bash
php artisan make:resource UrunResource
php artisan make:resource KategoriResource
php artisan make:resource UrunCollection
php artisan make:resource KategoriCollection
```

`app/Http/Resources/UrunResource.php`:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'urun_adi' => $this->urun_adi,
            'aciklama' => $this->aciklama,
            'birim_fiyat' => (float) $this->birim_fiyat,
            'indirimli_fiyat' => $this->when(!is_null($this->indirimli_fiyat), (float) $this->indirimli_fiyat),
            'indirim_orani' => $this->when(!is_null($this->indirim_orani), (float) $this->indirim_orani),
            'stok_miktari' => $this->stok_miktari,
            'satista_mi' => (bool) $this->satista_mi,
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

### 1.2 Controller İmplementasyonu

`app/Http/Controllers/API/UrunController.php`:

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UrunResource;
use App\Http\Resources\UrunCollection;
use App\Models\Urun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UrunController extends Controller
{
    /**
     * Tüm ürünleri listele
     */
    public function index(Request $request)
    {
        $query = Urun::with(['kategori']);

        // Filtreleme
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->has('arama')) {
            $query->where('urun_adi', 'like', '%' . $request->arama . '%')
                  ->orWhere('aciklama', 'like', '%' . $request->arama . '%');
        }

        // Sıralama
        $siralama = $request->siralama ?? 'id';
        $siralamaYonu = $request->siralama_yonu ?? 'asc';
        $query->orderBy($siralama, $siralamaYonu);

        // Sayfalama
        $perPage = $request->get('per_page', 15);
        $urunler = $query->paginate($perPage);

        return new UrunCollection($urunler);
    }

    /**
     * Yeni ürün oluştur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'urun_adi' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'birim_fiyat' => 'required|numeric|min:0',
            'indirimli_fiyat' => 'nullable|numeric|lt:birim_fiyat',
            'stok_miktari' => 'required|integer|min:0',
            'kategori_id' => 'required|exists:kategoriler,id',
            'resim' => 'nullable|image|max:2048',
            'satista_mi' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Doğrulama hatası',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        // Resim yükleme
        if ($request->hasFile('resim')) {
            $path = $request->file('resim')->store('urunler', 'public');
            $data['resim_yolu'] = $path;
        }

        $urun = Urun::create($data);

        return response()->json([
            'success' => true,
            'data' => new UrunResource($urun->load('kategori')),
            'message' => 'Ürün başarıyla oluşturuldu',
        ], Response::HTTP_CREATED);
    }

    /**
     * Belirli bir ürünü getir
     */
    public function show(Urun $urun)
    {
        return new UrunResource($urun->load('kategori', 'yorumlar'));
    }

    /**
     * Ürün güncelle
     */
    public function update(Request $request, Urun $urun)
    {
        $validator = Validator::make($request->all(), [
            'urun_adi' => 'sometimes|required|string|max:255',
            'aciklama' => 'nullable|string',
            'birim_fiyat' => 'sometimes|required|numeric|min:0',
            'indirimli_fiyat' => 'nullable|numeric|lt:birim_fiyat',
            'stok_miktari' => 'sometimes|required|integer|min:0',
            'kategori_id' => 'sometimes|required|exists:kategoriler,id',
            'resim' => 'nullable|image|max:2048',
            'satista_mi' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Doğrulama hatası',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        // Yeni resim yükleme
        if ($request->hasFile('resim')) {
            // Eski resmi sil
            if ($urun->resim_yolu) {
                Storage::disk('public')->delete($urun->resim_yolu);
            }

            $path = $request->file('resim')->store('urunler', 'public');
            $data['resim_yolu'] = $path;
        }

        $urun->update($data);

        return response()->json([
            'success' => true,
            'data' => new UrunResource($urun->load('kategori')),
            'message' => 'Ürün başarıyla güncellendi',
        ]);
    }

    /**
     * Ürünü sil (soft delete)
     */
    public function destroy(Urun $urun)
    {
        try {
            $urun->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla silindi',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün silinirken bir hata oluştu',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * İndirimli ürünleri listele
     */
    public function indirimliler()
    {
        $urunler = Urun::whereNotNull('indirimli_fiyat')
            ->where('indirimli_fiyat', '>', 0)
            ->where('satista_mi', true)
            ->orderByRaw('(birim_fiyat - indirimli_fiyat) / birim_fiyat DESC')
            ->paginate(10);

        return new UrunCollection($urunler);
    }
}
```

## 2. API Kimlik Doğrulama (Sanctum)

### 2.1 Sanctum Kurulumu

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2.2 Auth Controller Oluşturma

```bash
php artisan make:controller API/AuthController
```

`app/Http/Controllers/API/AuthController.php`:

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Kullanıcı kaydı
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Doğrulama hatası',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'message' => 'Kullanıcı başarıyla oluşturuldu',
        ], Response::HTTP_CREATED);
    }

    /**
     * Kullanıcı girişi
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz kimlik bilgileri',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'message' => 'Giriş başarılı',
        ]);
    }

    /**
     * Kullanıcı çıkışı
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış başarılı',
        ]);
    }

    /**
     * Mevcut kullanıcı bilgilerini getir
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }
}
```

### 2.3 Auth Rotalarını Ekleme

`routes/api.php`:

```php
// Auth Routes
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});
```

## 3. API Dokümantasyonu (Scribe)

### 3.1 Scribe Kurulumu

```bash
composer require --dev knuckleswtf/scribe
php artisan vendor:publish --provider="Knuckles\Scribe\ScribeServiceProvider" --tag=scribe-config
php artisan vendor:publish --provider="Knuckles\Scribe\ScribeServiceProvider" --tag=scribe-views
```

### 3.2 API Dokümantasyonu Oluşturma

`config/scribe.php` dosyasını düzenleyin ve ardından:

```bash
php artisan scribe:generate
```

## 4. API Testleri

### 4.1 Test Oluşturma

```bash
php artisan make:test UrunApiTest
```

`tests/Feature/UrunApiTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Urun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrunApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Test kullanıcısı oluştur
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Test verileri oluştur
        $this->urun = Urun::factory()->create();
    }

    /** @test */
    public function kullanici_urunleri_listeleyebilir()
    {
        $response = $this->getJson('/api/v1/urunler');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'urun_adi',
                        'birim_fiyat',
                        'stok_miktari',
                        'created_at',
                    ]
                ],
                'links',
                'meta',
            ]);
    }

    /** @test */
    public function kullanici_urun_ekleyebilir()
    {
        $data = [
            'urun_adi' => 'Yeni Ürün',
            'birim_fiyat' => 99.99,
            'stok_miktari' => 50,
            'kategori_id' => 1,
            'aciklama' => 'Bu yeni bir üründür',
            'satista_mi' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/urunler', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'urun_adi',
                    'birim_fiyat',
                    'stok_miktari',
                ],
                'message',
            ]);
    }

    // Diğer test metodları...
}
```

## 5. Ödev

1. Eksik controller'ları (KategoriController, SiparisController) oluşturun
2. Tüm API endpoint'leri için test yazın
3. API dokümantasyonunu oluşturun
4. Postman koleksiyonu oluşturup test edin
5. Rate limiting ve cache mekanizmalarını ekleyin

## 6. Yararlı Kaynaklar

- [Laravel Sanctum Dokümantasyonu](https://laravel.com/docs/sanctum)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)
- [Scribe Dokümantasyonu](https://scribe.knuckles.wtf/)
- [Postman Koleksiyon Oluşturma](https://learning.postman.com/docs/collections/creating-collections/)

---

**Not:** Bir sonraki derste Vue.js ile frontend geliştirmeye başlayacağız ve bu REST API'mizi kullanarak bir e-ticet uygulaması geliştireceğiz.
