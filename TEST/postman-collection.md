# Postman Koleksiyonu

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
Route::post('/test-data', [TestDataController::class, 'createTestData']);
```

**`app/Http/Controllers/TestDataController.php` Dosyası içine:**

```php
// app/Http/Controllers/TestDataController.php
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
        'categories_count' => count($data['categories']),
        'products_count' => array_sum(array_map(fn($cat) => count($cat['products']), $data['categories']))
    ], 201);
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

- http://localhost:8000/api/categories → {{host}}:{{port}}/{{base_path}}/categories
- http://laravel-api.test/api/products → {{host}}/{{base_path}}/products

Bu değişkenler sayesinde tek bir yerden tüm URL'leri güncelleyebilirsiniz!
