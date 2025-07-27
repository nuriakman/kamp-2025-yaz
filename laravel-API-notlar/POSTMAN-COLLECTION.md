# Postman Koleksiyonu

Bu dokümanda, Laravel API projesi için hazırlanmış Postman koleksiyon dosyalarının açıklamaları bulunmaktadır.

## Koleksiyonlar Arasındaki Temel Farklar

| Özellik                    | v1    | v2   | v3    | v4    |
| -------------------------- | ----- | ---- | ----- | ----- |
| Temel CRUD İşlemleri       | ✅    | ✅   | ✅    | ✅    |
| Örnek Test Verileri        | ❌    | ✅   | ✅    | ✅    |
| Zengin Örnek Veri Seti     | ❌    | ✅   | ✅    | ✅    |
| Değişken Desteği           | ❌    | ❌   | ✅    | ✅    |
| Farklı Ortam Desteği       | ❌    | ❌   | ✅    | ✅    |
| URL Yapılandırılabilirliği | ❌    | ❌   | ✅    | ✅    |
| JWT Kimlik Doğrulama       | ❌    | ❌   | ❌    | ✅    |
| Korumalı Endpoint'ler      | ❌    | ❌   | ❌    | ✅    |
| Kullanım Kolaylığı         | Temel | Orta | İleri | Uzman |

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
Route::post('api/test-data', [TestDataController::class, 'createTestData']);

```

Kontroller Oluşturalım:

```php
php artisan make:controller Api/TestDataController
```

**`app/Http/Controllers/Api/TestDataController.php` Dosyası içine:**

```php
// app/Http/Controllers/Api/TestDataController.php
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

## V4 İçin JWT Kimlik Doğrulama Talimatları

Dosyası: [postman-collection-v4-jwt.json](postman-collection-v4-jwt.json)

### JWT Özellikleri:

**V4 Koleksiyonu İçeriği:**

- Tüm V3 özellikleri (değişken desteği, esnek URL yapısı)
- JWT kimlik doğrulama sistemi
- Kullanıcı kaydı ve giriş endpoint'leri
- Otomatik token yönetimi
- Korumalı endpoint'ler (kategoriler, ürünler)
- Token yenileme ve çıkış işlemleri

### Kurulum Adımları:

1. **Koleksiyonu İçe Aktar:**

   - [postman-collection-v4-jwt.json](postman-collection-v4-jwt.json) dosyasını Postman'e import edin

2. **Ortam Değişkenlerini Ayarlayın:**

   ```json
   {
     "host": "localhost",
     "port": "8000",
     "base_path": "api",
     "jwt_token": "" // Otomatik doldurulacak
   }
   ```

3. **Test Sırası:**
   - Önce "Auth > Register" ile kullanıcı oluşturun
   - "Auth > Login" ile giriş yapın (token otomatik kaydedilir)
   - Korumalı endpoint'leri test edin (Categories, Products)
   - "Auth > Logout" ile çıkış yapın

### Otomatik Token Yönetimi:

V4 koleksiyonu, JWT token'larını otomatik olarak yönetir:

- Login sonrası token otomatik kaydedilir
- Tüm korumalı isteklerde otomatik kullanılır
- Logout sonrası token temizlenir

### Korumalı Endpoint'ler:

JWT token gerektiren endpoint'ler:

- `GET /api/categories` - Kategorileri listele
- `POST /api/categories` - Yeni kategori oluştur
- `GET /api/categories/{id}` - Kategori detayı
- `PUT /api/categories/{id}` - Kategori güncelle
- `DELETE /api/categories/{id}` - Kategori sil
- `GET /api/products` - Ürünleri listele
- `POST /api/products` - Yeni ürün oluştur
- `GET /api/products/{id}` - Ürün detayı
- `PUT /api/products/{id}` - Ürün güncelle
- `DELETE /api/products/{id}` - Ürün sil
- `GET /api/auth/me` - Kullanıcı profili
- `POST /api/auth/logout` - Çıkış yap

### Hata Yönetimi:

**Token olmadan erişim:**

```json
{
  "message": "Unauthenticated."
}
```

**Geçersiz token:**

```json
{
  "message": "Token is Invalid"
}
```

**Süresi dolmuş token:**

```json
{
  "message": "Token has expired"
}
```

## 📊 Koleksiyon Seçim Rehberi

**Hangi koleksiyonu kullanmalıyım?**

- **V1:** Laravel API'ye yeni başlıyorsanız ve temel işlemleri öğrenmek istiyorsanız
- **V2:** Test verileri ile çalışmak ve daha kapsamlı testler yapmak istiyorsanız
- **V3:** Farklı ortamlarda (development, staging, production) test yapmak istiyorsanız
- **V4:** JWT kimlik doğrulama sistemi ile tam özellikli API testi yapmak istiyorsanız

## ✨ Önerilen Kullanım

1. **Öğrenme Aşaması:** V1 → V2 → V3 → V4 sırasıyla ilerleyin
2. **Geliştirme:** V4 koleksiyonunu kullanın (tam özellikli)
3. **Üretim Testi:** V4 koleksiyonunu production ortam değişkenleriyle kullanın
