# Factory Nedir?

# Factory

**Seeder** ve **Factory**, Laravel'de çok iç içe çalışır ama görevleri farklıdır.
Kafandaki karışıklığı gidermek için bu konuyu **basit, karşılaştırmalı ve örnekli** anlatacağım.

---

## 🧠 Kısa Tanımlar

| Kavram      | Kısa Açıklama                                              |
| ----------- | ---------------------------------------------------------- |
| **Factory** | ✅ Sahte (fake) veri üretmek için **veri şablonu** sağlar. |
| **Seeder**  | ✅ Veritabanına **veri kaydetmek için komut** çalıştırır.  |

---

## 🎭 Benzetmeli Anlatım (En Basit Haliyle)

### 🏭 Factory = Fabrika

- "Nasıl bir ürün oluşturulacak?" onu tarif eder.
- Kalem mi üreteceksin? Nasıl bir kalem?

  - `name`: rastgele bir isim
  - `price`: 1–100 TL arasında rastgele bir değer

- Henüz üretilmedi, sadece kalıbı belli.

### 🚚 Seeder = Üretim ve Veritabanına Gönderme

- Kalemleri üretmek için fabrikaya emir verir: “20 kalem üret ve veritabanına kaydet.”
- Factory’yi kullanarak 20 sahte ürün üretir.
- Bu ürünleri veritabanına yazar.

---

## 🔧 Gerçek Laravel Örneği ile Gösterim

### 1. Product Model’imiz Olsun:

```php
// app/Models/Product.php
class Product extends Model
{
    protected $fillable = ['name', 'price'];
}
```

---

### 2. Factory: Ürün Şablonu Oluştur

```bash
php artisan make:factory ProductFactory --model=Product
```

```php
// database/factories/ProductFactory.php
public function definition()
{
    return [
        'name' => fake()->word(),
        'price' => fake()->randomFloat(2, 10, 100),
    ];
}
```

> Bu sadece şablon! Henüz veritabanına hiçbir şey gitmedi.

---

### 3. Seeder: Ürünleri Oluştur ve Kaydet

```bash
php artisan make:seeder ProductSeeder
```

```php
// database/seeders/ProductSeeder.php
use App\Models\Product;

public function run()
{
    Product::factory()->count(20)->create();
}
```

> Burada diyoruz ki: “Fabrikadan 20 ürün üret ve veritabanına yaz.”

---

## 🪄 4. Artisan Komutu ile Her Şey Hazır

Seeder’ı çalıştır:

```bash
php artisan db:seed --class=ProductSeeder
```

Ya da hepsini sıfırla ve yükle:

```bash
php artisan migrate:fresh --seed
```

---

## 🧾 Not: Factory Olmadan da Seeder Yazabilirsin

Factory olmadan "sabit" veri de ekleyebilirsin:

```php
DB::table('products')->insert([
    'name' => 'Gerçek Kalem',
    'price' => 19.99,
]);
```

Ama 100 tane rastgele ürün gerekiyorsa: Factory kullanmak **zorundasın**.

---

## ✅ Sonuç – En Net Özet

| Özellik                | Factory                                     | Seeder                              |
| ---------------------- | ------------------------------------------- | ----------------------------------- |
| Görevi                 | Sahte veri şablonu oluşturur                | Veritabanına veri kaydeder          |
| Ne yapar?              | Model nesnesi üretir (`Product::factory()`) | Üretilen nesneleri DB’ye yazar      |
| Tek başına çalışır mı? | Hayır, çağrılmalı                           | Evet                                |
| Kullanımı              | `$product = Product::factory()->make();`    | `ProductSeeder` içinde çalıştırılır |

---

İstersen sıradaki adımda birlikte:

- Gerçek uygulama üzerinden **factory ile user, product, order üretelim**
- Her biri için ilişkili veriler üretelim (örneğin: her kullanıcının 5 ürünü olsun)

Hazırsan başlayabiliriz.
