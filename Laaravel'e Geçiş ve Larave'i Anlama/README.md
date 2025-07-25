# Laravel'e Geçiş ve Laravel'i Anlama

Bu klasör, **prosedürel PHP'den modern Laravel framework'üne geçiş** sürecini adım adım açıklayan kapsamlı bir rehber niteliğindedir. Aynı zamanda Laravel'in temel yapı taşlarını ve ileri seviye konseptlerini anlamak isteyenler için zengin bir kaynak sunar.

Bu kaynak, Laravel 10.x sürümüne göre hazırlanmıştır.

## 📚 Klasör İçeriği

### 🚀 Temel Kavramlar

- **[procedurel-to-laravel.md](./procedurel-to-laravel.md)** - Prosedürel PHP'den Laravel'e geçiş süreci
- **[olaganustu-laravel.md](./olaganustu-laravel.md)** - Laravel'in sıradışı özellikleri ve gücü

### 🏗️ Laravel Mimarisi ve Yapı Taşları

- **[controller.nedir.md](./controller.nedir.md)** - Controller yapısı ve kullanımı
- **[middleware-nedir.md](./middleware-nedir.md)** - Middleware katmanı ve HTTP istekleri yönetimi
- **[service-nedir.md](./service-nedir.md)** - Service katmanı ve iş mantığı yönetimi
- **[modal-nedir.md](./modal-nedir.md)** - Model (Eloquent ORM) yapısı ve veritabanı işlemleri

### 🔧 Veritabanı ve Veri Yönetimi

- **[migration-nedir.md](./migration-nedir.md)** - Migration dosyaları ve veritabanı versiyonlaması
- **[seeder-nedir.md](./seeder-nedir.md)** - Seeder kullanımı ve test verisi oluşturma
- **[factory-nedir.md](./factory-nedir.md)** - Factory pattern ve model fabrikaları
- **[seeder-vs-factory.md](./seeder-vs-factory.md)** - Seeder ve Factory arasındaki farklar
- **[tablolar-tablo-iliskileri.md](./tablolar-tablo-iliskileri.md)** - Tablo ilişkileri ve Eloquent ilişkileri

### 🛡️ Güvenlik ve Koruma

- **[api-guvenligi.md](./api-guvenligi.md)** - API güvenliği best practices
- **[csrf-ve-xss-nedir.md](./csrf-ve-xss-nedir.md)** - CSRF ve XSS koruması
- **[jwt-kullanimi.md](./jwt-kullanimi.md)** - JWT authentication kullanımı
- **[rate-limiting-nedir.md](./rate-limiting-nedir.md)** - Rate limiting ve istek sınırlaması

### 🌐 Uluslararasılaştırma ve Destek

- **[localization-nedir.md](./localization-nedir.md)** - Çoklu dil desteği ve localization
- **[helper-nedir.md](./helper-nedir.md)** - Global helper fonksiyonları

### 📊 İzleme ve Performans

- **[loglama-nedir.md](./loglama-nedir.md)** - Loglama sistemleri ve hata takibi
- **[onbellekleme-nedir.md](./onbellekleme-nedir.md)** - Caching mekanizmaları ve performans optimizasyonu

### 🔧 Geliştirici Araçları

- **[dump-autoload-nedir.md](./dump-autoload-nedir.md)** - Composer autoload optimizasyonu
- **[ortak-degisken-kullanimi.md](./ortak-degisken-kullanimi.md)** - Ortam değişkenleri ve konfigürasyon
- **[sadece-api-icin-laravel.md](./sadece-api-icin-laravel.md)** - API-first Laravel uygulamaları

### 📖 Ek Kaynaklar

- **[birkac-konu.md](./birkac-konu.md)** - Ek konular ve püf noktalar

## 🎯 Hedef Kitle

Bu kaynaklar özellikle şu gruplar için hazırlanmıştır:

- **Prosedörel PHP geliştiricileri** modern framework'e geçmek isteyen
- **Yeni başlayan Laravel geliştiricileri** temel kavramları öğrenmek isteyen
- **Orta seviye Laravel geliştiricileri** ileri seviye konseptleri derinlemesine anlamak isteyen

## 🗂️ Kullanım Önerisi

1. **Başlangıç için**: `procedurel-to-laravel.md` ile başlayın
2. **Temel yapı**: `controller.nedir.md`, `modal-nedir.md`, `migration-nedir.md` dosyalarını sırasıyla inceleyin
3. **Güvenlik**: `api-guvenligi.md` ve `csrf-ve-xss-nedir.md` ile devam edin
4. **İleri seviye**: `onbellekleme-nedir.md`, `localization-nedir.md` gibi konuları keşfedin

---

## 📄 Dosya Detayları ve Örnek Kodlar

### 🚀 Temel Kavramlar

#### **[procedurel-to-laravel.md](./procedurel-to-laravel.md)**

**Özet:** Prosedürel PHP'den modern Laravel framework'üne adım adım geçiş rehberi. Tek dosyalık PHP script'lerinden MVC yapısına geçiş sürecini detaylıca açıklar.

**Ana Başlıklar:**

- Prosedürel yapı ile Laravel farkları
- Veritabanı işlemlerinin geçişi
- OOP ve MVC kavramları
- Route yapısı ve kontrolör kullanımı

**Örnek Kod:**

```php
// Prosedürel PHP
$conn = mysqli_connect("localhost", "root", "", "db");
$result = mysqli_query($conn, "SELECT * FROM users");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Laravel'e geçiş
// routes/web.php
Route::get('/users', [UserController::class, 'index']);

// app/Http/Controllers/UserController.php
class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }
}
```

#### **[olaganustu-laravel.md](./olaganustu-laravel.md)**

**Özet:** Laravel'in sıradışı özellikleri ve framework'ün sunduğu güçlü araçlar. Facade'ler, Service Container, Event sistemleri gibi ileri seviye konular.

**Örnek Kod:**

```php
// Facade kullanımı
use Illuminate\Support\Facades\Cache;

Cache::put('key', 'value', 600);
$value = Cache::get('key');

// Service Container
app()->singleton('mailer', function () {
    return new Mailer();
});
```

### 🏗️ Laravel Mimarisi ve Yapı Taşları

#### **[controller.nedir.md](./controller.nedir.md)**

**Özet:** Controller'ların rolü, oluşturulması ve kullanımı. HTTP isteklerini karşılayan ve iş mantığını yöneten katman.

**Ana Başlıklar:**

- Controller oluşturma komutları
- Resource controller'lar
- Form validation
- API response'ları

**Örnek Kod:**

```bash
# Controller oluşturma
php artisan make:controller ProductController
php artisan make:controller Api/ProductController --api
```

```php
// app/Http/Controllers/ProductController.php
class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }
}
```

#### **[middleware-nedir.md](./middleware-nedir.md)**

**Özet:** HTTP isteklerini filtreleyen ve işleyen ara katman. Authentication, rate limiting, CORS gibi işlemler için kullanılır.

**Örnek Kod:**

```bash
# Middleware oluşturma
php artisan make:middleware CheckAge
```

```php
// app/Http/Middleware/CheckAge.php
public function handle($request, Closure $next)
{
    if ($request->age < 18) {
        return redirect('home');
    }
    return $next($request);
}
```

#### **[service-nedir.md](./service-nedir.md)**

**Özet:** İş mantığını controller'lardan ayıran ve yeniden kullanılabilir hale getiren service katmanı.

**Örnek Kod:**

```php
// app/Services/OrderService.php
class OrderService
{
    public function calculateTotal($items)
    {
        return collect($items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }
}

// Controller'da kullanım
public function store(OrderService $orderService)
{
    $total = $orderService->calculateTotal(request('items'));
}
```

#### **[modal-nedir.md](./modal-nedir.md)**

**Özet:** Eloquent ORM ile veritabanı işlemleri. Model'lerin oluşturulması, ilişkiler ve query builder kullanımı.

**Örnek Kod:**

```bash
# Model oluşturma
php artisan make:model Post -m
```

```php
// app/Models/Post.php
class Post extends Model
{
    protected $fillable = ['title', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// Kullanım
$posts = Post::with('user')->where('published', true)->get();
```

### 🔧 Veritabanı ve Veri Yönetimi

#### **[migration-nedir.md](./migration-nedir.md)**

**Özet:** Veritabanı şemasını PHP koduyla tanımlama ve versiyonlama sistemi. Takım çalışması için vazgeçilmezdir.

**Ana Başlıklar:**

- Migration oluşturma
- Sütun tipleri
- Foreign key'ler
- Rollback işlemleri

**Örnek Kod:**

```bash
# Migration oluşturma
php artisan make:migration create_posts_table
```

```php
// database/migrations/xxxx_create_posts_table.php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->foreignId('user_id')->constrained();
    $table->boolean('published')->default(false);
    $table->timestamps();
});
```

#### **[seeder-nedir.md](./seeder-nedir.md)**

**Özet:** Test verisi oluşturma ve veritabanını başlangıç verisiyle doldurma sistemi.

**Örnek Kod:**

```bash
# Seeder oluşturma
php artisan make:seeder UsersTableSeeder
```

```php
// database/seeders/UsersTableSeeder.php
public function run()
{
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password')
    ]);
}

// Tüm seed'leri çalıştırma
php artisan db:seed
```

#### **[factory-nedir.md](./factory-nedir.md)**

**Özet:** Model fabrikaları ile büyük miktarda test verisi oluşturma. Faker kütüphanesi ile gerçekçi veri üretimi.

**Örnek Kod:**

```bash
# Factory oluşturma
php artisan make:factory PostFactory
```

```php
// database/factories/PostFactory.php
public function definition()
{
    return [
        'title' => $this->faker->sentence,
        'content' => $this->faker->paragraphs(3, true),
        'user_id' => User::factory(),
        'published' => $this->faker->boolean(70)
    ];
}

// Kullanım
Post::factory()->count(50)->create();
```

### 🛡️ Güvenlik ve Koruma

#### **[api-guvenligi.md](./api-guvenligi.md)**

**Özet:** API güvenliği için best practices. Authentication, authorization, rate limiting ve input validation konuları.

**Örnek Kod:**

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
});

// Controller'da authorization
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    // ...
}
```

#### **[csrf-ve-xss-nedir.md](./csrf-ve-xss-nedir.md)**

**Özet:** CSRF (Cross-Site Request Forgery) ve XSS (Cross-Site Scripting) saldırılarına karşı koruma yöntemleri.

**Örnek Kod:**

```php
// Blade template'de XSS koruması
{{ $userInput }} // Otomatik escaping
{!! $trustedHtml !!} // Raw HTML (dikkatli kullanılmalı)

// CSRF token'ı form'da
<form method="POST" action="/profile">
    @csrf
    <!-- form alanları -->
</form>
```

### 📊 İzleme ve Performans

#### **[onbellekleme-nedir.md](./onbellekleme-nedir.md)**

**Özet:** Laravel'in güçlü caching sistemi. Redis, Memcached ve dosya tabanlı cache kullanımı.

**Örnek Kod:**

```php
// Cache kullanımı
use Illuminate\Support\Facades\Cache;

// Veri cache'leme
$users = Cache::remember('users.active', 3600, function () {
    return User::where('active', true)->get();
});

// Cache temizleme
Cache::forget('users.active');

// Route caching (performans için)
php artisan route:cache
```

#### **[localization-nedir.md](./localization-nedir.md)**

**Özet:** Çoklu dil desteği ve localization sistemi. Dil dosyaları ve çeviri fonksiyonları.

**Örnek Kod:**

```php
// resources/lang/tr/messages.php
return [
    'welcome' => 'Hoş geldiniz',
    'goodbye' => 'Güle güle'
];

// Kullanım
echo __('messages.welcome');
echo trans('messages.goodbye');
```

### 🔧 Geliştirici Araçları

#### **[dump-autoload-nedir.md](./dump-autoload-nedir.md)**

**Özet:** Composer autoload optimizasyonu ve sınıf haritalarının yeniden oluşturulması.

**Örnek Kod:**

```bash
# Autoload dosyalarını yeniden oluşturma
composer dump-autoload

# Optimizasyonlu autoload
composer dump-autoload --optimize

# Laravel özel komutlar
php artisan optimize
php artisan config:cache
```

---

## 🔗 Faydalı Kaynaklar

- **Laravel Resmi Dokümantasyonu:** [laravel.com/docs](https://laravel.com/docs)
- **Laravel Türkçe Kaynak:** [laravel.gen.tr](https://laravel.gen.tr)
- **Laravel Best Practices:** GitHub'da topluluk tarafından hazırlanan rehberler
- **Laravel News:** Güncel Laravel haberleri ve makaleler

## 📞 Destek ve İletişim

Bu kaynaklarla ilgili sorularınız veya katkılarınız varsa:

- GitHub üzerinden pull request oluşturabilirsiniz
- İçerik hakkında geri bildirimde bulunabilirsiniz
- Kendi deneyimlerinizi ekleyebilirsiniz

---

_Bu belge, Türkçe Laravel öğrenim kaynakları arasında en kapsamlı ve güncel rehber olma hedefiyle hazırlanmıştır._
