# Laravel Proje Kurulumu ve API Geliştirme Ders Notu

## ÖZET

```bash
# Yeni Laravel projesi oluşturun
composer create-project laravel/laravel laravel_api_jwt

# Proje dizinine geçin
cd laravel_api_jwt

# Kategoriler için migration dosyası oluştur
php artisan make:migration create_categories_table --create=categories

# Ürünler için migration dosyası oluştur
php artisan make:migration create_products_table --create=products

# Oluşturulan iki dosya içindeki up() metodlarını düzenle. Ardından,
# migration dosyalarını çalıştırarak yukarıda tanımlanan 2 tablonun veritabanında oluşturulmasını sağla
php artisan migrate

# Model sınıflarını oluştur
php artisan make:model Category
php artisan make:model Product
# Model sınıflarını düzenle

# Controller sınıflarını oluştur
php artisan make:controller Api/CategoryController --api --resource
php artisan make:controller Api/ProductController --api --resource

# Route tanımlanın eklenmesi
# routes/api.php dosyasını oluştur ve içeriğini düzenle

# /api/ adresi için rota yapılandırması
# bootstrap/app.php dosyasının içeriğini düzenle

# Geliştirme sunucusunu başlat
php artisan serve --host=0.0.0.0 --port=8000

# Rotaları kontrol et
php artisan route:list

# Postman ile test et
# URL: http://localhost:8000/api/categories
```

## Giriş

Bu ders notunda sıfırdan bir Laravel projesi kurup, temel API yapısını oluşturacağız. Bu notun sonunda JWT authentication eklemeye hazır bir Laravel API projesi elde edeceksiniz.

## Gereksinimler

- PHP 8.2 veya üzeri
- Composer
- Node.js ve npm (opsiyonel, frontend için)
- Veritabanı (MySQL, PostgreSQL, SQLite)
- Git (opsiyonel ama önerilen)

## Sistem Gereksinimlerini Kontrol Etme

Öncelikle sisteminizde gerekli yazılımların kurulu olduğunu kontrol edin:

```bash
# PHP versiyonunu kontrol edin
php --version

# Composer'ın kurulu olduğunu kontrol edin
composer --version

# MySQL'in çalıştığını kontrol edin (MySQL kullanıyorsanız)
mysql --version
```

## Adım 1: Yeni Laravel Projesi Oluşturma

### Composer ile Laravel Kurulumu

```bash
# Yeni Laravel projesi oluşturun
composer create-project laravel/laravel laravel_api_jwt

# Proje dizinine geçin
cd laravel_api_jwt
```

### Laravel Installer ile Kurulum (Alternatif)

```bash
# Laravel installer'ı global olarak yükleyin (bir kez yapılır)
composer global require laravel/installer

# Yeni proje oluşturun
laravel new laravel_api_jwt

# Proje dizinine geçin
cd laravel_api_jwt
```

## LAMP Ortamında Laravel API Geliştirme

Bu derste, LAMP (Linux, Apache, MySQL, PHP) ortamımızı kullanarak sıfırdan bir Laravel API oluşturacağız. Kategori (parent) ve Ürün (child) olmak üzere iki kaynak arasında bir ilişki kuracak ve bu kaynakları yönetmek için temel CRUD (Create, Read, Update, Delete) işlemlerini sunan endpoint'ler hazırlayacağız.

**Önemli:** Tüm komutlar, projenin ana dizininde (`/var/www/html/laravel_api` gibi) çalıştırılmalıdır.

## Adım 2: Proje Konfigürasyonu

### .env Dosyasını Düzenleme

`.env` dosyasını açın ve aşağıdaki ayarları yapın:

```env
APP_NAME="Laravel API JWT"
APP_ENV=local
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# Veritabanı ayarları (SQLite için)
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel_api_jwt
# DB_USERNAME=root
# DB_PASSWORD=

# MySQL kullanıyorsanız:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel_api_jwt
# DB_USERNAME=root
# DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### SQLite Veritabanı Dosyası Oluşturma

SQLite kullanıyorsanız:

```bash
# SQLite veritabanı dosyası oluşturun
touch database/database.sqlite
```

**Not:** `database.sqlite` dosya adı Laravel'in varsayılan konfigürasyonundan gelir. `config/database.php` dosyasında SQLite bağlantısı şu şekilde tanımlıdır:

```php
'sqlite' => [
    'driver' => 'sqlite',
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    // ...
],
```

`database_path('database.sqlite')` fonksiyonu `database/database.sqlite` yolunu döndürür. Eğer farklı bir dosya adı kullanmak isterseniz, `.env` dosyasında `DB_DATABASE` değerini değiştirebilirsiniz:

```env
# Özel SQLite dosya adı için
DB_DATABASE=/tam/yol/benim_veritabanim.sqlite
```

### MySQL Veritabanı Oluşturma

MySQL kullanıyorsanız:

```sql
-- MySQL'e bağlanın ve veritabanı oluşturun
CREATE DATABASE laravel_api_jwt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Adım 3: Temel Laravel Kurulumunu Test Etme

### Application Key Oluşturma

```bash
# Uygulama anahtarını oluşturun
php artisan key:generate
```

### Migration'ları Çalıştırma

```bash
# Varsayılan migration'ları çalıştırın
php artisan migrate
```

### Development Server'ı Başlatma

```bash
# Laravel development server'ı başlatın
php artisan serve
```

Tarayıcınızda `http://127.0.0.1:8000` adresine giderek Laravel'in çalıştığını kontrol edin.

## Adım 4: API Yapısı için Temel Model ve Controller'ları Oluşturma

### Category Model ve Migration Oluşturma

```bash
# Category model, migration ve controller oluşturun
php artisan make:model Category -mcr --api
```

<details>
  <summary>**Komutun detaylı açıklaması:**</summary>
  <blockquote>
    Bu komut, Laravel'de bir model, migration ve controller dosyasını aynı anda oluşturmak için kullanılır.

    ```bash
    php artisan make:model Category -mcr --api
    ```

    1. `php artisan`: Laravel'in komut satırı arayüzünü çalıştırır.

    2. `make:model Category`: "Category" adında bir model oluşturur.

    3. `-mcr`: Bu flag'ler şunları oluşturur:
       - `-m`: Migration dosyası
       - `-c`: Controller dosyası
       - `-r`: Resource controller (CRUD metodları ile)

    4. `--api`: API resource controller oluşturur (create ve edit metodları olmadan)

    Bu komutu çalıştırdığınızda Laravel şu dosyaları oluşturur:
    - `app/Models/Category.php`
    - `database/migrations/xxxx_create_categories_table.php`
    - `app/Http/Controllers/CategoryController.php`

  </blockquote>
</details>

Bu komut şunları oluşturacak:

- `app/Models/Category.php` - Model dosyası
- `database/migrations/xxxx_create_categories_table.php` - Migration dosyası
- `app/Http/Controllers/CategoryController.php` - Controller dosyası

### Product Model ve Migration Oluşturma

```bash
# Product model, migration ve controller oluşturun
php artisan make:model Product -mcr --api
```

### Category Migration'ını Düzenleme

`database/migrations/xxxx_create_categories_table.php` dosyasını açın:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

### Product Migration'ını Düzenleme

`database/migrations/xxxx_create_products_table.php` dosyasını açın:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Migration'ları Çalıştırma

```bash
# Yeni migration'ları çalıştırın
php artisan migrate
```

## Adım 5: Model İlişkilerini Tanımlama

### Category Model'ini Düzenleme

`app/Models/Category.php` dosyasını açın:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

### Product Model'ini Düzenleme

`app/Models/Product.php` dosyasını açın:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

## Adım 6: API Controller'larını Oluşturma

### API Controller'ları için Dizin Oluşturma

```bash
# Api dizinini oluşturun
mkdir -p app/Http/Controllers/Api
```

### CategoryController'ı Api Dizinine Taşıma

`app/Http/Controllers/Api/CategoryController.php` dosyasını oluşturun:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('products')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json($category, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('products');
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
```

### ProductController'ı Api Dizinine Taşıma

`app/Http/Controllers/Api/ProductController.php` dosyasını oluşturun:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($validated);
        $product->load('category');

        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validated);
        $product->load('category');

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
```

## Adım 7: API Route'larını Tanımlama

### routes/api.php Dosyasını Düzenleme

`routes/api.php` dosyasını açın ve aşağıdaki içeriği ekleyin:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

// Kategori ve Ürünler için API kaynak rotaları
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
```

## Adım 8: Test Verileri Oluşturma

### TestDataController Oluşturma

```bash
# Test verileri için controller oluşturun
php artisan make:controller Api/TestDataController
```

`app/Http/Controllers/Api/TestDataController.php` dosyasını düzenleyin:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class TestDataController extends Controller
{
    /**
     * Create test data for categories and products.
     */
    public function createTestData()
    {
        // Test kategorileri oluştur
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Elektronik ürünler'],
            ['name' => 'Ev & Yaşam', 'description' => 'Ev ve yaşam ürünleri'],
            ['name' => 'Spor & Outdoor', 'description' => 'Spor ve outdoor ürünleri'],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = Category::create($categoryData);
        }

        // Test ürünleri oluştur
        $products = [
            [
                'name' => 'iPhone 15',
                'description' => 'Apple iPhone 15 128GB',
                'price' => 999.99,
                'stock' => 50,
                'category_id' => $createdCategories[0]->id,
            ],
            [
                'name' => 'Samsung TV',
                'description' => '55 inç 4K Smart TV',
                'price' => 799.99,
                'stock' => 25,
                'category_id' => $createdCategories[0]->id,
            ],
            [
                'name' => 'Kahve Makinesi',
                'description' => 'Otomatik kahve makinesi',
                'price' => 299.99,
                'stock' => 30,
                'category_id' => $createdCategories[1]->id,
            ],
            [
                'name' => 'Koşu Ayakkabısı',
                'description' => 'Nike koşu ayakkabısı',
                'price' => 129.99,
                'stock' => 100,
                'category_id' => $createdCategories[2]->id,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $productData) {
            $createdProducts[] = Product::create($productData);
        }

        return response()->json([
            'message' => 'Test verileri başarıyla oluşturuldu',
            'categories' => $createdCategories,
            'products' => $createdProducts,
        ], 201);
    }
}
```

### Test Data Route'unu Ekleme

`routes/api.php` dosyasına test data route'unu ekleyin:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TestDataController;

// Kategori ve Ürünler için API kaynak rotaları
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

// Test verileri oluşturmak için rotası
Route::post('/test-data', [TestDataController::class, 'createTestData']);
```

**Not:** Rotalarınızın `api/` ön ekiyle (prefix) başladığını fark edeceksiniz (örneğin: `api/categories`). Bu ön ek, Laravel'in yeni versiyonlarında `bootstrap/app.php` dosyasında merkezi olarak yönetilir. İlgili yapılandırma aşağıdaki gibidir ve `apiPrefix` parametresi ile bu ön ek belirlenir:

`bootstrap/app.php` Dosyasını açın ve **şu şekilde düzeltin:**

```php
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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

## Adım 9: API'yi Test Etme

### Development Server'ı Başlatma

```bash
# Laravel development server'ı başlatın
php artisan serve
```

### Route'ları Kontrol Etme

```bash
# Tüm route'ları listeleyin
php artisan route:list
```

### Test Verilerini Oluşturma

```bash
# Test verilerini oluşturun
curl -X POST http://127.0.0.1:8000/api/test-data \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

### Kategorileri Listeleme

```bash
# Kategorileri listeleyin
curl -X GET http://127.0.0.1:8000/api/categories \
  -H "Accept: application/json"
```

### Ürünleri Listeleme

```bash
# Ürünleri listeleyin
curl -X GET http://127.0.0.1:8000/api/products \
  -H "Accept: application/json"
```

### Yeni Kategori Oluşturma

```bash
# Yeni kategori oluşturun
curl -X POST http://127.0.0.1:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Kitap",
    "description": "Kitap ve dergi kategorisi"
  }'
```

### Yeni Ürün Oluşturma

```bash
# Yeni ürün oluşturun (category_id'yi mevcut bir kategori ID'si ile değiştirin)
curl -X POST http://127.0.0.1:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Laptop",
    "description": "Gaming laptop",
    "price": 1299.99,
    "stock": 15,
    "category_id": 1
  }'
```

## Adım 10: Proje Yapısını Kontrol Etme

Bu noktada proje yapınız şu şekilde olmalı:

```
laravel_api_jwt/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── CategoryController.php
│   │           ├── ProductController.php
│   │           └── TestDataController.php
│   └── Models/
│       ├── Category.php
│       ├── Product.php
│       └── User.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── xxxx_create_categories_table.php
│   │   └── xxxx_create_products_table.php
│   └── database.sqlite (SQLite kullanıyorsanız)
├── routes/
│   └── api.php
├── .env
└── composer.json
```

## Adım 11: Git ile Versiyon Kontrolü (Opsiyonel)

```bash
# Git repository'sini başlatın
git init

# .gitignore dosyası zaten mevcut, tüm dosyaları ekleyin
git add .

# İlk commit'i yapın
git commit -m "Initial Laravel API project setup with Categories and Products"

# GitHub'a push etmek isterseniz (repository oluşturduktan sonra)
# git remote add origin https://github.com/username/laravel_api_jwt.git
# git branch -M main
# git push -u origin main
```

## Sonuç ve Sonraki Adımlar

✅ **Tebrikler!** Başarıyla bir Laravel API projesi oluşturdunuz. Projenizde şunlar bulunuyor:

### Mevcut Özellikler:

- ✅ Laravel 12.x kurulumu
- ✅ SQLite/MySQL veritabanı konfigürasyonu
- ✅ Category ve Product modelleri
- ✅ Model ilişkileri (One-to-Many)
- ✅ RESTful API controller'ları
- ✅ API route'ları
- ✅ Test verileri oluşturma sistemi
- ✅ Validation kuralları
- ✅ JSON response'ları

### API Endpoint'leri:

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
- `POST /api/test-data` - Test verileri oluşturma

### API'yi Test Etme ve Doğrulama

Artık API'miz hazır! Postman veya benzeri bir araç kullanarak test edebiliriz. Alternatif olarak `curl` komutunu da kullanabilirsiniz.

API test etmek için şunları kullanabilirsiniz:

- cURL ile [CURL.md](./CURL.md) API test etme talimatları
- Postman ile [POSTMAN.md](./POSTMAN.md) API test etme talimatları
- Postman koleksiyonu ile [POSTMAN-COLLECTION.md](./POSTMAN-COLLECTION.md) API test etme talimatları

Tebrikler! Artık çalışan, ilişkisel ve test edilebilir bir Laravel API'niz var.

### Sonraki Adım:

Sonraki adımlar bu API'ye JWT ile kimlik doğrulama eklemek olacaktır.

[DERS-NOTU-JWT.md](./DERS-NOTU-JWT.md) dosyasından devam edin.

## Sorun Giderme

### Yaygın Hatalar ve Çözümleri:

1. **"Class 'App\Http\Controllers\Api\CategoryController' not found"**

   - Controller'ın doğru namespace'de olduğundan emin olun
   - `composer dump-autoload` komutunu çalıştırın

2. **"SQLSTATE[HY000] [14] unable to open database file"**

   - SQLite dosyasının oluşturulduğundan emin olun: `touch database/database.sqlite`
   - Dosya izinlerini kontrol edin

3. **"Route [categories.index] not defined"**

   - `routes/api.php` dosyasında route'ların doğru tanımlandığından emin olun
   - `php artisan route:clear` komutunu çalıştırın

4. **Migration hataları**
   - `php artisan migrate:fresh` ile migration'ları sıfırlayın
   - Veritabanı bağlantı ayarlarını `.env` dosyasında kontrol edin

Bu ders notunu takip ederek sıfırdan bir Laravel API projesi oluşturabilir ve JWT authentication eklemeye hazır hale getirebilirsiniz!
