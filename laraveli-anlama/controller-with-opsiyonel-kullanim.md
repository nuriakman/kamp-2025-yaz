# Eager Loading’i Opsiyonel Hale Getirmek

Laravel'de `::with()` ile ilişkili verileri çekerken, bu işlemi **Controller içinde opsiyonel hale** getirmek oldukça faydalı bir tekniktir. Bu, aynı controller metodunun hem ilişkili verilerle hem de sade veriyle esnek çalışabilmesini sağlar.

Aşağıda sana **tam kapsamlı** bir açıklama yapacağım:

---

# 🧠 Amaç: with() ile Eager Loading’i **opsiyonel** hale getirmek

Senaryo:
`CategoryController@index` içinde:

* Eğer kullanıcı “ilişkili verileri de getir” derse `with()` çalışacak.
* Aksi durumda sadece sade `Category::all()` dönecek.

---

## ✅ 1. Yol: Request Parametresine Göre Ayarlamak

### Örnek:

```php
public function index(Request $request)
{
    $query = Category::query();

    if ($request->has('with')) {
        // Tek veya çoklu ilişkiler olabilir
        $with = explode(',', $request->input('with'));
        $query->with($with);
    }

    $categories = $query->get();

    return response()->json($categories);
}
```

---

### 🔎 Nasıl Çağırılır?

* Sade çağrı:

  ```
  GET /api/categories
  ```

* İlişkili veriyle çağrı:

  ```
  GET /api/categories?with=products
  ```

* Birden fazla ilişki:

  ```
  GET /api/categories?with=products,children
  ```

---

## 🧪 2. Yol: Route İçinde Parametreyle Kullanmak

```php
Route::get('/categories/{withRelations?}', [CategoryController::class, 'index']);
```

```php
public function index($withRelations = null)
{
    $query = Category::query();

    if ($withRelations) {
        $relations = explode(',', $withRelations);
        $query->with($relations);
    }

    return $query->get();
}
```

> Bu yöntem REST dışı kalabilir ama bazı özel projelerde kullanılabilir.

---

## 🎛️ 3. Yol: `request()->boolean('with_products')` gibi anahtarlarla

Bu yöntemde kontrol daha nettir:

```php
public function index(Request $request)
{
    $query = Category::query();

    if ($request->boolean('with_products')) {
        $query->with('products');
    }

    if ($request->boolean('with_children')) {
        $query->with('children');
    }

    return $query->get();
}
```

### Örnek istek:

```
GET /api/categories?with_products=true&with_children=true
```

---

## 🧰 4. Daha Gelişmiş: Güvenli ve Filtrelenmiş with()

İlişki isimlerinin doğru yazıldığından emin olmak için izinli olanları kontrol edebilirsin:

```php
$allowedRelations = ['products', 'children', 'parent'];

$requested = explode(',', $request->input('with', ''));

$validRelations = array_intersect($requested, $allowedRelations);

$categories = Category::with($validRelations)->get();
```

---

## 📦 Bonus: `Resource` sınıfında kullanılabilir hale getirme

Eğer `CategoryResource` kullanıyorsan, içeride `whenLoaded()` ile bu işlemi güzelce ayarlayabilirsin:

```php
return [
    'id' => $this->id,
    'name' => $this->name,
    'products' => ProductResource::collection($this->whenLoaded('products')),
];
```

Bu sayede sadece `with('products')` ile yüklendiğinde `products` bilgisi görünür olur.

---

## 🧩 Özet

| Yöntem                    | Açıklama                               |
| ------------------------- | -------------------------------------- |
| `?with=products`          | Temel ve sade kontrol                  |
| `?with=products,children` | Çoklu ilişki kontrolü                  |
| `?with_products=true`     | Açık/kapalı kontrol mantığı            |
| `array_intersect`         | Güvenli ve sınırlı ilişki kontrolü     |
| `whenLoaded()`            | Laravel Resource içinde akıllı kontrol |

---


# GERÇEK ÖRNEK ÜZERİNDE UYGULAMA

Elbette! Aşağıda sana gerçekçi bir senaryo üzerinden, Laravel'de `with()` kullanımının **opsiyonel olarak controller içinde nasıl yönetilebileceğini** ve bu yapıdan dönen **JSON çıktısının nasıl görüneceğini** tüm ayrıntılarıyla gösteriyorum.

---

# 🔧 Senaryo: Kategoriler ve Ürünler

**Modeller:**

* `Category` modeli, birçok `Product` içerir.
* `Product` modeli, bir `Category`'ye aittir.

---

## 🧩 Model Tanımları

**Category.php**

```php
public function products()
{
    return $this->hasMany(Product::class);
}
```

**Product.php**

```php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

---

## 🎯 Controller – Opsiyonel `with()` Kullanımı

```php
use Illuminate\Http\Request;
use App\Models\Category;

public function index(Request $request)
{
    $query = Category::query();

    if ($request->has('with')) {
        // ?with=products,subcategories gibi virgüllü parametreyi işleyelim
        $relations = explode(',', $request->input('with'));
        
        // Güvenlik için sadece izin verilen ilişkiler
        $allowed = ['products', 'subcategories'];
        $validRelations = array_intersect($relations, $allowed);

        $query->with($validRelations);
    }

    $categories = $query->get();

    return response()->json($categories);
}
```

---

## 🌐 Kullanım Örnekleri

### 1. Sade listeleme:

```
GET /api/categories
```

### 2. Ürünleri ile birlikte listeleme:

```
GET /api/categories?with=products
```

### 3. Ürün ve alt kategorileri birlikte:

```
GET /api/categories?with=products,subcategories
```

---

## 📦 Örnek JSON Çıktılar

### 1. Sade `Category::all()`

```json
[
  {
    "id": 1,
    "name": "Elektronik",
    "slug": "elektronik"
  },
  {
    "id": 2,
    "name": "Kitap",
    "slug": "kitap"
  }
]
```

---

### 2. `Category::with('products')`

```json
[
  {
    "id": 1,
    "name": "Elektronik",
    "slug": "elektronik",
    "products": [
      {
        "id": 101,
        "name": "Akıllı Telefon",
        "price": 8000,
        "category_id": 1
      },
      {
        "id": 102,
        "name": "Laptop",
        "price": 15000,
        "category_id": 1
      }
    ]
  },
  {
    "id": 2,
    "name": "Kitap",
    "slug": "kitap",
    "products": []
  }
]
```

---

### 3. `Category::with('products', 'subcategories')` çıktısı

Bu örnek için `Category` modeline bir de şu ilişki eklendiğini varsayalım:

```php
public function subcategories()
{
    return $this->hasMany(Category::class, 'parent_id');
}
```

```json
[
  {
    "id": 1,
    "name": "Elektronik",
    "slug": "elektronik",
    "products": [
      { "id": 101, "name": "Tablet", "price": 5000, "category_id": 1 }
    ],
    "subcategories": [
      { "id": 3, "name": "Telefon", "slug": "telefon", "parent_id": 1 },
      { "id": 4, "name": "Bilgisayar", "slug": "bilgisayar", "parent_id": 1 }
    ]
  }
]
```

---

## 💡 İpucu: `whenLoaded()` ile Resource üzerinden filtreleme

Eğer `CategoryResource` kullanıyorsan, JSON'da sadece `with()` ile yüklenen ilişkilerin görünmesini sağlayabilirsin:

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'products' => ProductResource::collection($this->whenLoaded('products')),
        'subcategories' => CategoryResource::collection($this->whenLoaded('subcategories')),
    ];
}
```

---

## 🧠 Özet

| İstek                                         | `with()` Aktif mi? | JSON Ne Döner?                 |
| --------------------------------------------- | ------------------ | ------------------------------ |
| `/api/categories`                             | ❌                  | Sadece kategoriler             |
| `/api/categories?with=products`               | ✅                  | Kategori + ürün bilgisi        |
| `/api/categories?with=products,subcategories` | ✅                  | Kategori + ürün + alt kategori |

