# Resource Nedir?

Laravel’de “resource”, bir varlığın CRUD işlemlerini standart bir sözleşme ile tanımlamak anlamına gelir. İki temel bağlamda kullanılır:

- Resource Controller: CRUD işlemleri için 7 standart metodu barındıran controller yapısı.
- API Resource (JsonResource): Model verisini API cevabına dönüştüren sınıf.

---

## 1) Resource Controller

Resource Controller, CRUD için şu 7 metodu içerir:

- index() → Listele
- create() → Form sayfası (genelde web için)
- store() → Kaydet
- show($id) → Tek kaydı göster
- edit($id) → Düzenleme formu (genelde web için)
- update($id) → Güncelle
- destroy($id) → Sil

### Oluşturma

```bash
php artisan make:controller ProductController --resource
```

### Route Tanımlama

```php
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);
```

> Tüm CRUD rotalarını tek satırda üretir.

### Varsayılan Eşleşmeler

| HTTP       | URL                      | Action   | Controller Metodu |
| ---------- | ------------------------ | -------- | ----------------- |
| GET        | `/products`              | index    | `index()`         |
| GET        | `/products/create`       | create   | `create()`        |
| POST       | `/products`              | store    | `store()`         |
| GET        | `/products/{id}`         | show     | `show($id)`       |
| GET        | `/products/{id}/edit`    | edit     | `edit($id)`       |
| PUT/PATCH  | `/products/{id}`         | update   | `update($id)`     |
| DELETE     | `/products/{id}`         | destroy  | `destroy($id)`    |

### Sadece API İçin

```php
Route::apiResource('products', ProductController::class);
```

> `create` ve `edit` rotaları dahil edilmez (form sayfası üretmez).

---

## 2) API Resource (JsonResource)

API Resource (JsonResource), model verisini kontrol ederek istenen formatta JSON üretmeni sağlar. Dönüş verisini soyutlayıp tek bir yerde yönetirsin.

### Oluşturma

```bash
php artisan make:resource ProductResource
```

### Örnek Kullanım

```php
use App\Http\Resources\ProductResource;
use App\Models\Product;

// Tek kayıt
return new ProductResource(Product::findOrFail($id));

// Liste
return ProductResource::collection(Product::paginate());
```

### Basit Bir `toArray` Örneği

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'price' => $this->price,
        'created_at' => $this->created_at?->toISOString(),
    ];
}
```

---

## 3) Resource Controller vs API Resource

- Resource Controller: İstek akışını ve CRUD işlemlerini yönetir (Controller katmanı).
- API Resource: Model verisini API cevabına dönüştürür (Sunum/transform katmanı).

Birlikte kullanımı idealdir: Controller içinden Resource döndürülür.

```php
use App\Http\Resources\ProductResource;
use App\Models\Product;

public function index()
{
    return ProductResource::collection(Product::latest()->paginate(10));
}
```

---

## 4) İpuçları

- Sadece API geliştiriyorsan `Route::apiResource()` tercih et.
- Doğrulamayı Request sınıflarına taşı (örn. `php artisan make:request StoreProductRequest`).
- Resource’larda alanları sürüm bazlı ya da role bazlı yönetebilirsin.
- Büyük listelerde `paginate()` + `Resource::collection()` kullan.
 
 ---
 

# ÖRNEK

**Resource Controller** ve **API Resource'ları** birlikte kullanarak hesaplanmış (calculated) ve formatlanmış (formatted) verileri yönetmek için mükemmel bir çözüm sunar. İşte nasıl yapabileceğiniz:

---

### **1. Resource Oluşturma**
Önce bir Resource sınıfı oluşturun:
```bash
php artisan make:resource ProductResource
```

---

### **2. Hesaplanmış ve Formatlanmış Alanlar Ekleme**
`ProductResource.php` içinde:
```php
public function toArray($request)
{
    return [
        // Temel alanlar
        'id' => $this->id,
        'name' => $this->name,
        
        // Hesaplanmış alan (calculated)
        'discounted_price' => $this->calculateDiscount(),
        
        // Formatlanmış alan (formatted)
        'price_with_currency' => number_format($this->price, 2).' ₺',
        
        // Koşullu alan
        'stock_status' => $this->when($this->stock > 0, 'In Stock', 'Out of Stock'),
        
        // İlişkiler (formatted)
        'category' => new CategoryResource($this->whenLoaded('category'))
    ];
}

// Model içinde hesaplama metodu (app/Models/Product.php)
public function calculateDiscount()
{
    return $this->price * 0.9; // %10 indirim
}
```

---

### **3. Resource Controller Kullanımı**
Controller'da Resource'u çağırın:
```php
// app/Http/Controllers/ProductController.php
public function show(Product $product)
{
    // İlişkileri yükle (eager loading)
    $product->load('category');
    
    return new ProductResource($product);
}

public function index()
{
    $products = Product::with('category')->paginate(10);
    return ProductResource::collection($products);
}
```

---

### **4. Örnek API Çıktısı**
```json
{
    "id": 1,
    "name": "Laravel Kitabı",
    "discounted_price": 44.99,
    "price_with_currency": "49.99 ₺",
    "stock_status": "In Stock",
    "category": {
        "id": 5,
        "name": "Kitaplar"
    }
}
```

---

### **Neden Bu Yaklaşım?**
| Senaryo | Çözüm | Örnek |
|---------|-------|-------|
| **Hesaplanmış Değerler** | Model metotları + Resource | `discounted_price` |
| **Veri Formatlama** | Resource içinde dönüşüm | `price_with_currency` |
| **Koşullu Alanlar** | `when()` metodu | `stock_status` |
| **İlişki Formatlama** | Nested Resource | `category` |

---

### **Avantajlar**
1. **Temiz Kod**: Controller'da formatlama karmaşası yok
2. **Tekrar Kullanılabilirlik**: Aynı Resource'u tüm endpoint'lerde kullanabilirsiniz
3. **Esneklik**: 
   - Farklı hesaplamalar için model metotları ekleyebilirsiniz
   - Formatları merkezi olarak değiştirebilirsiniz
4. **Performans**: Eager loading ile N+1 probleminden kaçınma

---

**Sonuç:**  
Resource'lar, özellikle hesaplanmış ve formatlanmış veriler için **controller'larınızı temiz tutarken** API yanıtlarınızı standartlaştırmanın en etkili yoludur. 🚀  




 ---
 # 📘 LARAVEL'DE RESOURCE — TÜM DETAYLAR
 
 ---
 
 ### 🔷 1. RESOURCE NEDİR?
 
 Laravel’de “resource” iki ana kavramdır ve API/CRUD geliştirmeyi standartlaştırır:
 
 - Resource Controller: CRUD aksiyonlarını tek sözleşmede toplayan controller yapısı (7 standart metot).
 - API Resource (JsonResource): Model verisini API cevabına dönüştüren sunum/transform katmanı.
 
 Kısaca: Controller süreci yönetir; API Resource verinin nasıl görüneceğini belirler.
 
 ---
 
 ### 🔷 2. NEDEN RESOURCE KULLANILIR?
 
 | Amaç                          | Açıklama                                                  |
 | ----------------------------- | --------------------------------------------------------- |
 | 🧼 Düzenli yapı               | Aksiyonlar ve JSON çıktı şablonları netleşir              |
 | 🔁 Tekrarlanabilirlik         | Birçok endpoint aynı şablonu paylaşır                     |
 | 🧩 Ayrık sorumluluk           | İş mantığı (Controller) ↔ Sunum (Resource) ayrılır        |
 | 🧪 Test edilebilirlik         | Çıktı formatını bağımsız test edebilirsin                 |
 | 🔄 Versiyon/rol bazlı kontrol | Alanları sürüm/role göre kolay yönetirsin                 |
 
 ---
 
 ### 🔷 3. RESOURCE CONTROLLER — DERİNLEME
 
 #### 3.1. Oluşturma
 
 ```bash
 php artisan make:controller ProductController --resource
 ```
 
 7 standart metot oluşur: `index, create, store, show, edit, update, destroy`.
 
 #### 3.2. Route Tanımlama ve Varyasyonlar
 
 ```php
 use App\Http\Controllers\ProductController;
 
 // Tüm CRUD rotaları
 Route::resource('products', ProductController::class);
 
 // Sadece API (create/edit olmadan)
 Route::apiResource('products', ProductController::class);
 
 // Birden fazla resource tek seferde
 Route::resources([
     'products' => ProductController::class,
     // 'categories' => CategoryController::class,
 ]);
 
 // Sadece bazı aksiyonlar
 Route::resource('products', ProductController::class)->only(['index', 'show']);
 Route::resource('products', ProductController::class)->except(['create', 'edit']);
 
 // Parametre isimleri (özelleştirme)
 Route::resource('users', UserController::class)->parameters([
     'users' => 'user',
 ]);
 
 // İsimlendirme (route names)
 Route::resource('products', ProductController::class)->names([
     'index' => 'products.list',
 ]);
 
 // İç içe (nested) resource
 Route::resource('categories.products', CategoryProductController::class);
 
 // Shallow nesting (daha kısa URL'ler)
 Route::resource('categories.products', CategoryProductController::class)->shallow();
 ```
 
 #### 3.3. Varsayılan Eşleşmeler (Özet)
 
 | HTTP       | URL                           | Action   | Controller Metodu |
 | ---------- | ----------------------------- | -------- | ----------------- |
 | GET        | `/products`                   | index    | `index()`         |
 | GET        | `/products/create`            | create   | `create()`        |
 | POST       | `/products`                   | store    | `store()`         |
 | GET        | `/products/{product}`         | show     | `show($product)`  |
 | GET        | `/products/{product}/edit`    | edit     | `edit($product)`  |
 | PUT/PATCH  | `/products/{product}`         | update   | `update($product)`|
 | DELETE     | `/products/{product}`         | destroy  | `destroy($product)`|
 
 > Not: Varsayılan parametre adı, resource isminin tekil hali (`{product}`) olur.
 
 #### 3.4. Basit Bir Resource Controller Örneği
 
 ```php
 namespace App\Http\Controllers;
 
 use App\Models\Product;
 use Illuminate\Http\Request;
 
 class ProductController extends Controller
 {
     public function index()
     {
         return Product::latest()->paginate(10);
     }
 
     public function store(Request $request)
     {
         $data = $request->validate([
             'name' => 'required|string|max:255',
             'price' => 'required|numeric|min:0',
         ]);
 
         $product = Product::create($data);
         return response()->json($product, 201);
     }
 
     public function show(Product $product)
     {
         return $product; // Route model binding
     }
 
     public function update(Request $request, Product $product)
     {
         $product->update($request->only('name', 'price'));
         return $product;
     }
 
     public function destroy(Product $product)
     {
         $product->delete();
         return response()->noContent();
     }
 }
 ```
 
 ---
 
 ### 🔷 4. API RESOURCE (JSONRESOURCE) — DERİNLEME
 
 API Resource, döndürülen veriyi merkezî bir yerde biçimlendirir.
 
 #### 4.1. Oluşturma
 
 ```bash
 php artisan make:resource ProductResource
 php artisan make:resource ProductCollection // Opsiyonel, koleksiyon sınıfı
 ```
 
 #### 4.2. Basit `ProductResource`
 
 ```php
 namespace App\Http\Resources;
 
 use Illuminate\Http\Resources\Json\JsonResource;
 
 class ProductResource extends JsonResource
 {
     public function toArray($request)
     {
         return [
             'id' => $this->id,
             'name' => $this->name,
             'price' => $this->price,
             'created_at' => $this->created_at?->toISOString(),
         ];
     }
 }
 ```
 
 #### 4.3. Controller’da Kullanım
 
 ```php
 use App\Http\Resources\ProductResource;
 use App\Models\Product;
 
 public function index()
 {
     return ProductResource::collection(Product::latest()->paginate(10));
 }
 
 public function show(Product $product)
 {
     return new ProductResource($product);
 }
 ```
 
 #### 4.4. İlişkiler, Koşullu Alanlar, Ek Alanlar
 
 ```php
 public function toArray($request)
 {
     return [
         'id' => $this->id,
         'name' => $this->name,
         // Koşullu alan (sadece admin ise)
         'cost' => $this->when($request->user()?->isAdmin(), $this->cost),
         // İlişkiyi resource ile sarmak
         'category' => new CategoryResource($this->whenLoaded('category')),
     ];
 }
 
 public function with($request)
 {
     return [
         'meta' => [
             'api_version' => '1.0',
         ],
     ];
 }
 
 // Ek veri eklemek (controller içinde)
 return (new ProductResource($product))
     ->additional(['trace_id' => request()->header('X-Trace-Id')]);
 ```
 
 #### 4.5. Koleksiyonlar ve Sayfalama
 
 - `ProductResource::collection(Product::paginate())` kullandığında `links` ve `meta` otomatik eklenir.
 - Kendi `ProductCollection` sınıfınla koleksiyon seviyesinde meta/links ekleyebilirsin.
 
 #### 4.6. Sarma (Wrapping)
 
 Varsayılan olarak tekli resource `{ data: {...} }` şeklinde sarılabilir. İstemiyorsan:
 
 ```php
 use Illuminate\Http\Resources\Json\JsonResource;
 JsonResource::withoutWrapping();
 ```
 
 ---
 
 ### 🔷 5. RESOURCE + CONTROLLER BİRLİKTE ÖRNEK
 
 ```php
 // routes/api.php
 use App\Http\Controllers\Api\ProductController;
 Route::apiResource('products', ProductController::class);
 
 // app/Http/Controllers/Api/ProductController.php
 namespace App\Http\Controllers\Api;
 
 use App\Http\Controllers\Controller;
 use App\Http\Resources\ProductResource;
 use App\Models\Product;
 use Illuminate\Http\Request;
 
 class ProductController extends Controller
 {
     public function index()
     {
         return ProductResource::collection(Product::latest()->paginate(15));
     }
 
     public function store(Request $request)
     {
         $data = $request->validate([
             'name' => 'required|string|max:255',
             'price' => 'required|numeric|min:0',
         ]);
 
         $product = Product::create($data);
         return (new ProductResource($product))
             ->response()
             ->setStatusCode(201);
     }
 
     public function show(Product $product)
     {
         return new ProductResource($product->load('category'));
     }
 }
 ```
 
 ---
 
 ### 🔷 6. EN İYİ UYGULAMALAR
 
 ✅ Controller kısa ve yalın olsun; JSON şekillendirmeyi Resource yapar
 ✅ `when()`/`mergeWhen()` ile koşullu alanlar
 ✅ `->only()`/`->except()` ile route kapsamını daralt
 ✅ `apiResource` ile API odaklı rotalar
 ✅ `Request` sınıflarıyla doğrulama
 ✅ `paginate()` + Resource ile tutarlı meta/links
 
 ---
 
 ### 🔷 7. SIK YAPILAN HATALAR
 
 ❌ Controller içinde karmaşık dizi oluşturup JSON dönmek (Resource varken gerek yok)
 ❌ Tüm alanları körlemesine açmak (güvenlik/aşırı veri aktarma riski)
 ❌ Büyük listeleri `all()` ile döndürmek (sayfalama kullan)
 
 ---
 
 ### 🔷 8. ÖZET
 
 - Resource Controller → CRUD akışının iskeleti
 - API Resource → Çıktının nasıl görüneceği
 - Birlikte kullan: Controller işlemi yönetir, Resource çıktıyı biçimlendirir
 - `Route::apiResource()` + `JsonResource` ile temiz, ölçeklenebilir API’ler

 ---

 ### 🔷 9. RESOURCE, CONTROLLER’A NE ZAMAN VE NASIL BAĞLANIR?

 #### Ne Zaman?

 - Controller bir Model ya da koleksiyon döndürüyorsa.
 - JSON çıktıyı standartlaştırmak, alanları filtrelemek/koşullamak istediğinde.
 - Sayfalama yapıyor ve otomatik `links/meta` istiyorsan.

 Örnek akışlar:
 - `index()` → liste/paginate
 - `show()` → tek kayıt
 - `store()`/`update()` → oluşturulan/güncellenen kaydı döndürme

 #### Nasıl?

 Controller içinde Model(ler)i Resource ile sar ve döndür.

 ```php
 // routes/api.php
 use App\Http\Controllers\Api\ProductController;
 
 Route::apiResource('products', ProductController::class);
 ```

 ```php
 // app/Http/Controllers/Api/ProductController.php
 namespace App\Http\Controllers\Api;
 
 use App\Http\Controllers\Controller;
 use App\Http\Resources\ProductResource;
 use App\Models\Product;
 use Illuminate\Http\Request;
 
 class ProductController extends Controller
 {
     // Liste/paginate → koleksiyon sarmalama
     public function index()
     {
         $products = Product::latest()->paginate(10);
         return ProductResource::collection($products);
     }
 
     // Tek kayıt → tekil resource
     public function show(Product $product)
     {
         $product->load('category'); // ilişkiler opsiyonel
         return new ProductResource($product);
     }
 
     // Oluşturma → 201 ile dön
     public function store(Request $request)
     {
         $data = $request->validate([
             'name' => 'required|string|max:255',
             'price' => 'required|numeric|min:0',
         ]);
 
         $product = Product::create($data);
 
         return (new ProductResource($product))
             ->response()
             ->setStatusCode(201);
     }
 }
 ```

 İpuçları:
 - Tekil vs. koleksiyon: `new ProductResource($product)` / `ProductResource::collection($query->paginate())`.
 - İlişkiler: Controller’da `->with()`/`->load()`; Resource’ta `new CategoryResource($this->whenLoaded('category'))`.
 - Koşullu alanlar: `when()` / `mergeWhen()`.
 - Sarma: `{ data: ... }` istemiyorsan `JsonResource::withoutWrapping();`.
 - Tutarlılık: Harici API cevaplarında daima Resource kullan; ham model/array döndürme.
