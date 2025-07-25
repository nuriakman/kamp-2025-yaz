# Ders Notu: Laravel ile Sıfırdan API Geliştirme

## LAMP Ortamında Laravel API Geliştirme

Bu derste, LAMP (Linux, Apache, MySQL, PHP) ortamımızı kullanarak sıfırdan bir Laravel API oluşturacağız. Kategori (parent) ve Ürün (child) olmak üzere iki kaynak arasında bir ilişki kuracak ve bu kaynakları yönetmek için temel CRUD (Create, Read, Update, Delete) işlemlerini sunan endpoint'ler hazırlayacağız.

**Önemli:** Tüm komutlar, projenin ana dizininde (`/var/www/html/laravel_api` gibi) çalıştırılmalıdır.

---

### Bölüm 1: Veritabanı Yapısını Oluşturma (Migrations)

Veritabanı tablolarımızı kodla tanımlamak için Laravel'in "migration" özelliğini kullanacağız.

**1. Kategoriler Tablosunu Oluşturma**

Önce kategorilerimizi tutacak tablo için migration dosyasını oluşturalım.

```bash
php artisan make:migration create_categories_table
```

Bu komut, `database/migrations/` dizininde yeni bir dosya oluşturur. Bu dosyayı açın ve `up()` metodunu aşağıdaki gibi düzenleyin:

```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_categories_table.php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}
```

**2. Ürünler Tablosunu Oluşturma**

Şimdi de ürünler tablosunu oluşturalım. Bu tablo, `categories` tablosuna bağlı olacak.

```bash
php artisan make:migration create_products_table
```

Oluşturulan yeni migration dosyasının `up()` metodunu aşağıdaki gibi düzenleyin. Burada `category_id` alanını bir "foreign key" olarak tanımlıyoruz. `onDelete('cascade')` ifadesi, bir kategori silindiğinde o kategoriye ait tüm ürünlerin de otomatik olarak silinmesini sağlar.

```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 8, 2);
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
```

**3. Veritabanını Güncelleme**

Migration dosyalarımız hazır. Şimdi bu dosyaları çalıştırarak tabloları veritabanında oluşturalım.

```bash
php artisan migrate
```

Bu komut sonrası MySQL veritabanınızda `categories` ve `products` tabloları oluşturulmuş olacak.

---

### Bölüm 2: Modelleri ve İlişkileri Tanımlama (Models)

Modeller, veritabanı tablolarımızla etkileşim kurmamızı sağlayan PHP sınıflarıdır.

**1. Category Modeli**

```bash
php artisan make:model Category
```

Oluşturulan `app/Models/Category.php` dosyasını açın ve içeriğini düzenleyin. `$fillable` dizisi, hangi alanların toplu atama (mass assignment) ile doldurulabileceğini belirtir. `products()` metodu ise bir kategorinin birden çok ürüne sahip olabileceğini (`hasMany`) tanımlar.

```php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

**2. Product Modeli**

```bash
php artisan make:model Product
```

`app/Models/Product.php` dosyasını açın ve düzenleyin. `category()` metodu, bir ürünün bir kategoriye ait olduğunu (`belongsTo`) belirtir.

```php
// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

---

### Bölüm 3: API Mantığını Oluşturma (Controllers)

Controller'lar, gelen HTTP isteklerini işleyen, modellerle konuşan ve yanıt döndüren sınıflardır.

**1. CategoryController Oluşturma**

`--api` ve `--resource` flag'leri, bize temel API metodlarını (index, store, show, update, destroy) hazır olarak sunan bir controller oluşturur.

```bash
php artisan make:controller Api/CategoryController --api --resource
```

`app/Http/Controllers/Api/CategoryController.php` dosyasını açın ve `index()` ile `store()` metodlarını doldurun.

```php
// app/Http/Controllers/Api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Response sabitleri için ekliyoruz

class CategoryController extends Controller
{
    /**
     * Tüm kategorileri listeler.
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Yeni bir kategori oluşturur ve veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories|max:255',
        ]);

        $category = Category::create($request->all());

        return response()->json($category, Response::HTTP_CREATED); // 201 Created
    }

    /**
     * Belirtilen bir kategoriyi gösterir.
     */
    public function show(Category $category)
    {
        // Laravel'in Route-Model Binding özelliği sayesinde, URL'deki {category}
        // ID'sine sahip olan Category modeli otomatik olarak bulunur ve enjekte edilir.
        return response()->json($category);
    }

    /**
     * Belirtilen bir kategoriyi günceller.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            // Kategori adının benzersiz olmasını kontrol ederken, mevcut kategorinin kendisini hariç tutarız.
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    /**
     * Belirtilen bir kategoriyi siler.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        // Başarılı silme işleminden sonra içerik döndürmeye gerek yoktur.
        return response()->json(null, Response::HTTP_NO_CONTENT); // 204 No Content
    }
}
```

**2. ProductController Oluşturma**

Aynı şekilde ürünler için de bir controller oluşturalım.

```bash
php artisan make:controller Api/ProductController --api --resource
```

`app/Http/Controllers/Api/ProductController.php` dosyasını açın ve `index()` ile `store()` metodlarını doldurun.

```php
// app/Http/Controllers/Api/ProductController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Tüm ürünleri, ilişkili kategorileriyle birlikte listeler.
     */
    public function index()
    {
        // Eager loading ile N+1 problemini önlüyoruz.
        return Product::with('category')->get();
    }

    /**
     * Yeni bir ürün oluşturur ve veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id' // Kategori var olmalı
        ]);

        $product = Product::create($request->all());

        return response()->json($product, Response::HTTP_CREATED); // 201
    }

    /**
     * Belirtilen bir ürünü, kategorisiyle birlikte gösterir.
     */
    public function show(Product $product)
    {
        // Route-Model Binding sayesinde bulunan ürünü, kategorisiyle birlikte yüklüyoruz.
        return response()->json($product->load('category'));
    }

    /**
     * Belirtilen bir ürünü günceller.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|integer|exists:categories,id'
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    /**
     * Belirtilen bir ürünü siler.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT); // 204
    }
}
```

---

### Bölüm 4: API Rotalarını Tanımlama (Routing)

Son olarak, oluşturduğumuz controller metodlarını dış dünyaya açan API endpoint'lerini tanımlayalım. `routes/api.php` dosyasını açın ve içeriğini aşağıdaki gibi güncelleyin.

`Route::apiResource` metodu, bizim için tüm standart CRUD rotalarını otomatik olarak oluşturur.

```php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

// Kategori ve Ürünler için API kaynak rotaları
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
```

**Not:** Rotalarınızın `api/` ön ekiyle (prefix) başladığını fark edeceksiniz (örneğin: `api/categories`). Bu ön ek, Laravel'in yeni versiyonlarında `bootstrap/app.php` dosyasında merkezi olarak yönetilir. İlgili yapılandırma aşağıdaki gibidir ve `apiPrefix` parametresi ile bu ön ek belirlenir:

`bootstrap/app.php` Dosyasını Açın:

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```

**Şu şekilde düzeltin:**

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```

---

### Bölüm 5: Uygulama

Uygulama dizini içinde:

```bash
# Migration başlat
php artisan migrate

# Sunucuyu başlat
php artisan serve --host=0.0.0.0 --port=8000

# Rotaları kontrol et
php artisan route:list


```

### Bölüm 6: API'yi Test Etme ve Doğrulama

Artık API'miz hazır! Postman veya benzeri bir araç kullanarak test edebiliriz. Alternatif olarak `curl` komutunu da kullanabilirsiniz.

**Proje Adresi:**
Kurulum adımlarında Apache için sanal konak (`VirtualHost`) ayarladıysanız, projenize `http://laravel-api.test` gibi bir adresten erişebilirsiniz. Aşağıdaki örneklerde bu adres kullanılacaktır.

**1. Yeni Kategori Oluşturma (POST)**

- **Metod:** `POST`
- **URL:** `http://laravel-api.test/api/categories`
- **Body (JSON):**
  ```json
  {
    "name": "Elektronik"
  }
  ```
- **Curl Komutu:**
  ```bash
  curl -X POST http://laravel-api.test/api/categories -H "Content-Type: application/json" -d '{"name": "Elektronik"}'
  ```
- **Başarılı Yanıt (201 Created):**
  ```json
  {
    "name": "Elektronik",
    "updated_at": "2025-07-24T12:00:00.000000Z",
    "created_at": "2025-07-24T12:00:00.000000Z",
    "id": 1
  }
  ```

**2. Tüm Kategorileri Listeleme (GET)**

- **Metod:** `GET`
- **URL:** `http://laravel-api.test/api/categories`
- **Curl Komutu:**
  ```bash
  curl http://laravel-api.test/api/categories
  ```

**3. Yeni Ürün Oluşturma (POST)**

- **Metod:** `POST`
- **URL:** `http://laravel-api.test/api/products`
- **Body (JSON):** (category_id'nin `1` olduğuna dikkat edin)
  ```json
  {
    "name": "Akıllı Telefon",
    "description": "Son model, güçlü işlemcili",
    "price": 25000.0,
    "category_id": 1
  }
  ```
- **Curl Komutu:**
  ```bash
  curl -X POST http://laravel-api.test/api/products -H "Content-Type: application/json" -d '{"name": "Akıllı Telefon", "description": "Son model, güçlü işlemcili", "price": 25000.00, "category_id": 1}'
  ```

**4. Tüm Ürünleri Listeleme (GET)**

- **Metod:** `GET`
- **URL:** `http://laravel-api.test/api/products`
- **Curl Komutu:**
  ```bash
  curl http://laravel-api.test/api/products
  ```
- **Başarılı Yanıt:**
  ```json
  [
    {
      "id": 1,
      "name": "Akıllı Telefon",
      "description": "Son model, güçlü işlemcili",
      "price": "25000.00",
      "category_id": 1,
      "created_at": "...",
      "updated_at": "...",
      "category": {
        "id": 1,
        "name": "Elektronik",
        "created_at": "...",
        "updated_at": "..."
      }
    }
  ]
  ```

Tebrikler! Artık çalışan, ilişkisel ve test edilebilir bir Laravel API'niz var. Sonraki adımlar bu API'ye JWT ile kimlik doğrulama eklemek olacaktır.
