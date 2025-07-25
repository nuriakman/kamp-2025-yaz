# Service Nedir?

Laravel’de **Service Katmanı (Service Layer)**, özellikle büyük veya orta ölçekli projelerde, **iş mantığını controller’lardan ayırmak** için kullanılan bir mimari yaklaşımdır.

---

## 🔷 1. SERVICE KATMANI NEDİR?

Service Katmanı, **iş mantığını (business logic)** barındıran bir ara katmandır.

| Görev                                   | Nerede Olmalı  |
| --------------------------------------- | -------------- |
| HTTP isteğini alma, doğrulama           | Controller     |
| Veritabanı bağlantısı, veri yapısı      | Model          |
| **Kurallar, hesaplamalar, işlem akışı** | ✅ **Service** |

> Özet: Controller "ne yapılacak?" sorusunu cevaplar. Service ise "nasıl yapılacak?" sorusunu çözer.

---

## 🔷 2. NEDEN GEREKLİ?

Controller içinde fazla mantık olması:

- Tekrar eden kodlara,
- Test edilmesi zor yapılara,
- Karmaşık if-else yığınlarına neden olur.

**Service kullanımı ile:**

✅ Controller’lar sadeleşir
✅ Test edilebilirlik artar
✅ Tekrar eden kodlar ortadan kalkar
✅ SOLID prensiplerine yaklaşılır

---

## 🔷 3. MİMARİ GÖRSEL AÇIKLAMA

```
Client → Route → Controller → Service → Repository (isteğe bağlı) → Model → DB
```

---

## 🔷 4. ÖRNEK: ProductService UYGULAMASI

---

### 📁 KLASÖR YAPISI

```
app/
├── Http/
│   └── Controllers/
│       └── ProductController.php
├── Services/
│   └── ProductService.php
├── Models/
│   └── Product.php
```

---

### 🛠 Service Sınıfını Oluştur

```bash
mkdir app/Services
touch app/Services/ProductService.php
```

**app/Services/ProductService.php**

```php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getAll()
    {
        return Product::all();
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
```

---

### 🧾 Controller'da Kullan

**app/Http/Controllers/ProductController.php**

```php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getAll());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric'
        ]);

        return response()->json($this->service->create($data), 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'price' => 'sometimes|numeric'
        ]);

        return response()->json($this->service->update($product, $data));
    }

    public function destroy(Product $product)
    {
        $this->service->delete($product);
        return response()->json(['message' => 'Silindi']);
    }
}
```

---

## 🔷 5. SERVICE KATMANININ KULLANIM KRİTERLERİ

✅ 3’ten fazla controller’da aynı mantık varsa
✅ Hesaplama, kural, dış API çağrısı gibi karmaşık işlemler varsa
✅ Test yazmak istiyorsan
✅ `try/catch` ile sarılması gereken işlemler varsa

---

## 🔷 6. ADVANCED: TRY-CATCH, TRANSACTION, HELPER FONKSİYONLAR

```php
public function create(array $data)
{
    return DB::transaction(function() use ($data) {
        // Buraya log, notification, diğer işlemler eklenebilir
        return Product::create($data);
    });
}
```

---

## 🔷 7. TEST EDİLEBİLİRLİK

```php
$productService = new ProductService();

$response = $productService->create([
    'name' => 'Deneme Ürün',
    'price' => 100.0
]);

$this->assertEquals('Deneme Ürün', $response->name);
```

> Controller test etmek zordur ama Service sınıfları bağımsız olarak test edilebilir.

---

## 🔚 ÖZET: SERVICE LAYER NE SAĞLAR?

| Faydası                  | Açıklama                                      |
| ------------------------ | --------------------------------------------- |
| Temiz controller         | İş mantığını dışarı taşır                     |
| Tekrar kullanılabilirlik | Farklı controller’lar aynı service’i kullanır |
| Test edilebilirlik       | Unit test kolaylaşır                          |
| Ölçeklenebilirlik        | Proje büyüdükçe sınıflar sade kalır           |
| SOLID uyumu              | Sorumluluklar ayrılır                         |

---
