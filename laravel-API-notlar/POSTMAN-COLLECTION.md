# Postman Koleksiyonu

Bu dokümanda, Laravel API projesi için hazırlanmış Postman koleksiyon dosyalarının açıklamaları bulunmaktadır.

## Koleksiyonlar Arasındaki Temel Farklar

| Özellik                    | v1    | v2   | v3    |
| -------------------------- | ----- | ---- | ----- |
| Temel CRUD İşlemleri       | ✅    | ✅   | ✅    |
| Örnek Test Verileri        | ❌    | ✅   | ✅    |
| Zengin Örnek Veri Seti     | ❌    | ✅   | ✅    |
| Değişken Desteği           | ❌    | ❌   | ✅    |
| Farklı Ortam Desteği       | ❌    | ❌   | ✅    |
| URL Yapılandırılabilirliği | ❌    | ❌   | ✅    |
| Kullanım Kolaylığı         | Temel | Orta | İleri |

Daha fazla bilgi için [collection-aciklamalar.md](./collection-aciklamalar.md) dosyasına bakabilirsiniz.

## V1 İçin Kurulum Talimatları

Dosyası: [postman-collection-v1.json](postman-collection-v1.json)

1. [postman-collection-v1.json](postman-collection-v1.json) dosyasını açın
2. İçeriği kopyalayın
3. Postman'i açın
4. "File | Import" menüsünü açın
5. Kopyaladığınız JSON'ı yapıştırın

Artık "Laravel API Koleksiyonu" adı altında tüm endpoint'leriniz hazır olacaktır. Her bir isteği çalıştırmak için ilgili isme tıklayıp "Send" butonuna basabilirsiniz.

Not: Eğer API'niz farklı bir port veya domain üzerinde çalışıyorsa, URL'leri buna göre güncellemeyi unutmayın.

## V1 İçin Kurulum Talimatları (Test Verisi Eklemeli)

Dosyası: [postman-collection-v2.json](postman-collection-v2.json)

**Şu 2 dosyada değişiklik gerekir:**

**`routes/api.php` Dosyası içine:**

```php
// routes/api.php
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

Kontroller Oluşturalım:

```php
php artisan make:controller Api/TestDataController
```

**`app/Http/Controllers/TestDataController.php` Dosyası içine:**

```php
// app/Http/Controllers/TestDataController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class TestDataController extends Controller
{
  public function createTestData(Request $request)
  {
    $data = $request->all();

    foreach ($data['categories'] as $categoryData) {
      $category = Category::create([
        'name' => $categoryData['name']
      ]);

      foreach ($categoryData['products'] as $productData) {
        $productData['category_id'] = $category->id;
        Product::create($productData);
      }
    }

    return response()->json([
      'message' => 'Test verileri başarıyla oluşturuldu',
      'total_categories_count' => Category::count(),
      'total_products_count' => Product::count()
    ], 201);
  }
}

```

## V3 İçin Değişken Kullanım Talimatları

Dosyası: [postman-collection-v3.json](postman-collection-v3.json)

### Değişken Kullanım Talimatları:

#### Postman'de Değişkenleri Ayarlama:

- Collection Variables (Koleksiyon Değişkenleri):
- Collection'ı seçin
- "Variables" sekmesine gidin
- Şu değerleri ayarlayın:
  - `host`: localhost (veya farklı bir host)
  - `port`: 8000 (veya farklı bir port)
  - `base_path`: api

#### Environment Variables (Ortam Değişkenleri):

- Postman'de sağ üst köşedeki "Environment" bölümünden yeni bir environment oluşturun
- Aynı değişkenleri buraya da ekleyebilirsiniz

#### Örnek Değerler:

- Local Development: host=localhost, port=8000
- Production: host=your-domain.com, port=80 veya 443
- Staging: host=staging.your-domain.com, port=8000

**Örnek URL Değişiklikleri:**

- <http://localhost:8000/api/categories> → {{host}}:{{port}}/{{base_path}}/categories
- <http://localhost:8000/api/products> → {{host}}:{{port}}/{{base_path}}/products

Bu değişkenler sayesinde tek bir yerden tüm URL'leri güncelleyebilirsiniz!
