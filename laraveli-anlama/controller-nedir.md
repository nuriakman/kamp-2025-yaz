# Controller Nedir?

Laravel’de **Controller**, uygulamandaki **iş mantığını (business logic)** taşıyan katmandır. Yani, kullanıcıdan gelen isteklere (HTTP GET, POST, vs.) ne yapılacağına **karar verir** ve genellikle modeli (veriyi) kullanarak sonuç döndürür.

## 📌 KISACA TANIM

**Controller**, gelen HTTP isteğini alır → işler → veritabanıyla çalışmak için **Model**’i kullanır → sonra yanıtı geri döner.

📬 **Kullanıcı → Route → Controller → Model → Controller → Yanıt**

---

## 🎯 NEDEN CONTROLLER KULLANILIR?

| Amaç                             | Açıklama                                                          |
| -------------------------------- | ----------------------------------------------------------------- |
| 💡 İş mantığını taşır            | Kullanıcıdan gelen veriye ne yapılacağını belirler                |
| 🧼 Kod düzenini sağlar           | Her işlemi ayrı metotlara ayırır                                  |
| 🔁 Tekrar kullanımı artırır      | Aynı mantığı birden çok yerde çağırabilirsin                      |
| 📤 JSON / HTML / Response üretir | API yazarken JSON döner, sayfa yaparken Blade'e yönlendirme yapar |

---

## ⚙️ 1. CONTROLLER NASIL OLUŞTURULUR?

### 🔹 Temel bir controller:

```bash
php artisan make:controller ProductController
```

> 📂 `app/Http/Controllers/ProductController.php` dosyası oluşur.

### 🔹 RESTful (CRUD) controller:

```bash
php artisan make:controller ProductController --resource
```

Bu komut şu 7 metodu otomatik oluşturur:

- index() → Listele
- create() → Yeni form (view için)
- store() → Kaydet
- show(\$id) → Detay göster
- edit(\$id) → Düzenle formu
- update(\$id) → Güncelle
- destroy(\$id) → Sil

---

## 🧩 2. ROUTE → CONTROLLER BAĞLANTISI

### 🔹 2.1. Temel route örneği

```php
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);
```

Bu, `/products` adresine istek gelince `ProductController@index` metodunu çalıştırır.

---

## 📦 3. CONTROLLER İÇERİĞİ (BİR ÖRNEK)

```php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Ürünleri listeler
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Yeni ürün kaydeder
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    // Tek ürünü gösterir
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // Ürünü günceller
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->only('name', 'price'));

        return response()->json($product);
    }

    // Ürünü siler
    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['deleted' => true]);
    }
}
```

---

## 📎 4. HANGİ İSTEĞE HANGİ METOT CEVAP VERİR?

| HTTP Yöntemi | URL           | Controller Metodu |
| ------------ | ------------- | ----------------- |
| GET          | `/products`   | `index()`         |
| POST         | `/products`   | `store()`         |
| GET          | `/products/3` | `show(3)`         |
| PUT/PATCH    | `/products/3` | `update(3)`       |
| DELETE       | `/products/3` | `destroy(3)`      |

---

## 🧠 5. CONTROLLER NE DEĞİLDİR?

| Yanlış Anlama                | Gerçek                               |
| ---------------------------- | ------------------------------------ |
| Veritabanı işlemlerini yapar | ❌ Hayır, bunu **Model** yapar       |
| Doğrudan HTML yazılır        | ❌ Hayır, bu **View** işidir         |
| Tek dosyada her şey olur     | ❌ Hayır, işlemler metotlara ayrılır |

---

## 🎯 6. CONTROLLER’DA NE OLUR?

| Parça                | Açıklama                            |
| -------------------- | ----------------------------------- |
| `Request`            | Kullanıcıdan gelen verileri alır    |
| `validate()`         | Verileri doğrular                   |
| `Model::create()`    | Veritabanına kaydeder               |
| `response()->json()` | JSON döner (API için)               |
| `return view()`      | Sayfa döner (Blade için, istenirse) |

---

## 🔚 SONUÇ

Laravel Controller:

- Kullanıcı isteğini işler,
- Doğrulama yapar,
- Gerekirse modeli çağırarak veritabanıyla etkileşir,
- Uygun bir cevap döner (JSON veya View).

---

## 📘 LARAVEL'DE CONTROLLER — TÜM DETAYLAR

---

### 🔷 1. CONTROLLER NEDİR?

**Controller**, gelen HTTP isteklerini (GET, POST, PUT, DELETE) karşılayan ve bu isteklere göre:

- veriyi işler (Model ile çalışır),
- doğrulama yapar (Request),
- yanıt döner (JSON, View vs),
- yönlendirme yapar (redirect).

MVC mimarisinde Controller, **C** harfidir.

---

### 🔷 2. KOD MANTIĞININ YERİ

| Parça          | Görevi                                           |
| -------------- | ------------------------------------------------ |
| **Route**      | “Ne istendi?” → Controller’a yönlendirir         |
| **Controller** | “İstek ne yapmalı?” → Model’den veri alır, işler |
| **Model**      | “Veri nerede?” → DB işlemleri                    |
| **View**       | “Nasıl gösterilecek?” → HTML/JSON/Cevap          |

---

### 🔷 3. CONTROLLER NASIL OLUŞTURULUR?

#### 🧪 Temel Controller:

```bash
php artisan make:controller ProductController
```

#### 🧪 CRUD (resource) Controller:

```bash
php artisan make:controller ProductController --resource
```

> Bu komut otomatik olarak 7 CRUD metodunu oluşturur:

```php
public function index()   // Listeleme (GET /products)
public function create()  // Form sayfası (GET /products/create)
public function store(Request $request) // Kayıt (POST)
public function show($id) // Tek kayıt (GET /products/3)
public function edit($id) // Düzenleme formu (GET /products/3/edit)
public function update(Request $request, $id) // Güncelleme (PUT/PATCH)
public function destroy($id) // Silme (DELETE)
```

> 💡 Eğer sadece API geliştiriyorsan `create()` ve `edit()` metodlarını kullanmazsın çünkü onlar genellikle sayfa gösterir (Blade için).

---

### 🔷 4. ROUTES İLE BAĞLANTI

#### 🛣 Tek tek tanımlamak:

```php
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
```

#### 🛣 Toplu tanımlamak:

```php
Route::resource('products', ProductController::class);
```

> Bu satır tüm CRUD işlemlerini otomatik tanımlar.

---

### 🔷 5. ÖRNEK: BASİT BİR API CONTROLLER

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Tüm ürünleri getir
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Yeni ürün kaydet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    // Tek ürün göster
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // Güncelle
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->only('name', 'price'));
        return response()->json($product);
    }

    // Sil
    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['message' => 'Silindi']);
    }
}
```

---

### 🔷 6. NEDEN CONTROLLER METOTLARI KULLANILIR?

| Metot       | Ne işe yarar         | HTTP        |
| ----------- | -------------------- | ----------- |
| `index()`   | Tüm kayıtları göster | GET         |
| `store()`   | Yeni kayıt ekle      | POST        |
| `show($id)` | Tek kaydı göster     | GET         |
| `update()`  | Kaydı güncelle       | PUT / PATCH |
| `destroy()` | Kaydı sil            | DELETE      |

---

### 🔷 7. GÜVENLİK VE DOĞRULAMA

Controller içinde gelen veriyi daima doğrula:

```php
$request->validate([
    'name' => 'required|max:255',
    'email' => 'required|email'
]);
```

> Eğer doğrulama geçmezse Laravel otomatik olarak 422 kodlu JSON hata döner (API için).

---

### 🔷 8. TEK SORUMLULUK PRENSİBİ

Controller sadece:

- İstekle ilgilenir,
- Model’i kullanır,
- View veya JSON üretir.

Ama verinin iş mantığını **Model’e**, sayfa görünümünü **View’a**, yeniden kullanılabilir servisleri **Service Class**'lara aktarmalısın.

---

### 🔷 9. KÖTÜ BİR CONTROLLER ÖRNEĞİ

```php
public function store(Request $request)
{
    $conn = mysqli_connect(...);
    $sql = "INSERT INTO users ...";
    mysqli_query($conn, $sql);
    // 😱 Bu işler modelde olmalı
}
```

> ❌ **Veritabanı işlemi controller’a değil, Model’e aittir.**

---

### 🔷 10. EN İYİ UYGULAMALAR

- ✅ Her resource için ayrı controller kullan
- ✅ Fonksiyonları kısa tut (maksimum 30-40 satır)
- ✅ Validation’ı ayrı Request class'a taşıyabilirsin (daha profesyonel)
- ✅ `try/catch` ile hata yönetimi yap
- ✅ API yazıyorsan daima `response()->json()` kullan

---

### 🔷 11. BONUS: REQUEST İLE DOĞRULAMA SINIFI KULLANMAK

```bash
php artisan make:request StoreProductRequest
```

**StoreProductRequest.php:**

```php
public function rules()
{
    return [
        'name' => 'required',
        'price' => 'required|numeric'
    ];
}
```

**Controller’da:**

```php
public function store(StoreProductRequest $request)
{
    return Product::create($request->validated());
}
```

> 💎 Kod daha sade ve güvenli olur.

---

### 🔚 ÖZET: CONTROLLER NE İŞE YARAR?

| Soru                | Cevap                                               |
| ------------------- | --------------------------------------------------- |
| Controller nedir?   | HTTP isteğini karşılayan sınıftır                   |
| Ne yapar?           | Model ile çalışır, doğrulama yapar, veri döner      |
| Neden kullanılır?   | Kod düzenini sağlar, test edilebilir yapı oluşturur |
| Route ile farkı ne? | Route yönlendirir, controller işler                 |
| Model ile farkı ne? | Model veri ile ilgilenir, controller isteği işler   |
