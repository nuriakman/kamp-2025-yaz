# Seeder Nedir?

- **Seeder** → Veritabanına **hazır ve belirli** verileri ekler (ör. admin kullanıcı).
- **Factory** → **Rastgele veya kural bazlı** test verisi üretir (faker kullanır).

### 🎯 Laravel'de Seeder Nedir?

**Seeder**, veritabanına **örnek veya başlangıç verileri** eklemek için kullanılır.
Örneğin:

- Kategoriler (elektronik, giyim, kitap)
- Test ürünleri
- Admin kullanıcı hesabı
- Yapay kullanıcılar (fake data)

Seeder’lar özellikle **geliştirme ve test aşamasında** çok kullanışlıdır.

---

## 🔧 Seeder Ne İşe Yarar?

| Kullanım Durumu                                                | Açıklama                                                         |
| -------------------------------------------------------------- | ---------------------------------------------------------------- |
| 🧪 Test için veri oluşturma                                    | Gerçek veritabanını bozmadan örnek veri ile geliştirme yaparsın. |
| 🧱 Sabit verileri yükleme                                      | Örneğin: şehir listesi, kategoriler gibi sabitler                |
| 👥 Kullanıcı, ürün, sipariş gibi ilişkili örnek veriler üretme | Özellikle `Factory` ile birlikte güçlü hale gelir.               |
| 🔁 Geliştirme ortamında veritabanını hızlıca doldurmak         | Tek komutla örnek veri yüklenebilir.                             |

---

## 🧰 Seeder Nasıl Oluşturulur?

### Komut:

```bash
php artisan make:seeder ProductSeeder
```

Laravel, şu dosyayı oluşturur:

```
database/seeders/ProductSeeder.php
```

---

## ✍️ Örnek Seeder Dosyası

```php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            'name' => 'Kalem',
            'price' => 9.99,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
```

---

## 📥 Seeder'ı Çalıştırmak

Seeder’ı tek başına çalıştırmak için:

```bash
php artisan db:seed --class=ProductSeeder
```

Tüm seeder’ları çalıştırmak için:

```bash
php artisan db:seed
```

> Not: `DatabaseSeeder.php` dosyasında çağrılmayan Seeder’lar bu komutla çalışmaz.

---

### 🔁 Seeder'ı Migration ile Birlikte Çalıştırmak

```bash
php artisan migrate:fresh --seed
```

Bu komut:

1. Tüm tabloları siler
2. Migration dosyalarını yeniden uygular
3. Tüm seeder’ları çalıştırır

---

## 🤝 Factory ile Birlikte Kullanmak

Seeder genellikle **factory** ile birlikte kullanılır:

### 1. Factory Oluştur:

```bash
php artisan make:factory ProductFactory --model=Product
```

```php
// database/factories/ProductFactory.php
public function definition()
{
    return [
        'name' => fake()->word(),
        'price' => fake()->randomFloat(2, 1, 100),
    ];
}
```

### 2. Seeder’da Kullan:

```php
use App\Models\Product;

public function run()
{
    Product::factory()->count(20)->create();
}
```

---

## 📌 Kısa Özet

| Soru                           | Cevap                                                                  |
| ------------------------------ | ---------------------------------------------------------------------- |
| **Seeder nedir?**              | Veritabanına örnek veya sabit veri eklemek için kullanılır.            |
| **Zorunlu mu?**                | Hayır, ama test ve geliştirme için çok faydalıdır.                     |
| **Factory ile kullanılır mı?** | Evet, bu sayede hızlı ve gerçekçi veriler oluşturabilirsin.            |
| **Ne zaman kullanılır?**       | Migration sonrası test verisi yüklemek veya sabitleri tanımlamak için. |

---

## 📌 Örnek Uygulama

Laravel'de **şehirleri ön tanım olarak veritabanına eklemek** için en sağlıklı ve tekrar kullanılabilir yöntem:

> ✅ **Seeder kullanarak sabit verileri eklemektir.**

Aşağıda sana adım adım anlatacağım 👇

---

### 📌 1. `cities` Adında Bir Tablo Oluştur

Migration dosyasını oluştur:

```bash
php artisan make:migration create_cities_table
```

#### `database/migrations/..._create_cities_table.php`

```php
Schema::create('cities', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

Uygula:

```bash
php artisan migrate
```

---

### 📌 2. Şehirleri Eklemek İçin Seeder Oluştur

```bash
php artisan make:seeder CitySeeder
```

#### `database/seeders/CitySeeder.php`

```php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run()
    {
        $cities = [
            'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Amasya',
            'Ankara', 'Antalya', 'Artvin', 'Aydın', 'Balıkesir',
            'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur',
            'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli',
            'Diyarbakır', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum',
            'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkâri',
            'Hatay', 'Isparta', 'Mersin', 'İstanbul', 'İzmir',
            'Kars', 'Kastamonu', 'Kayseri', 'Kırklareli', 'Kırşehir',
            'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa',
            'Kahramanmaraş', 'Mardin', 'Muğla', 'Muş', 'Nevşehir',
            'Niğde', 'Ordu', 'Rize', 'Sakarya', 'Samsun',
            'Siirt', 'Sinop', 'Sivas', 'Tekirdağ', 'Tokat',
            'Trabzon', 'Tunceli', 'Şanlıurfa', 'Uşak', 'Van',
            'Yozgat', 'Zonguldak', 'Aksaray', 'Bayburt', 'Karaman',
            'Kırıkkale', 'Batman', 'Şırnak', 'Bartın', 'Ardahan',
            'Iğdır', 'Yalova', 'Karabük', 'Kilis', 'Osmaniye',
            'Düzce'
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name' => $city,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
```

---

### 📌 3. Seeder'ı `DatabaseSeeder.php`'e Ekle

```php
// database/seeders/DatabaseSeeder.php

public function run()
{
    $this->call([
        CitySeeder::class,
    ]);
}
```

---

### 📌 4. Tümünü Uygula

```bash
php artisan migrate:fresh --seed
```

Bu komut:

1. Tabloları sıfırlar
2. `cities` tablosunu oluşturur
3. Türkiye’deki tüm şehirleri veritabanına ekler ✅

---

### 🎁 Bonus: Eğer Şehirler JSON Dosyasında Varsa

Eğer elinde şöyle bir dosya varsa (`cities.json`):

```json
["Adana", "Adıyaman", "Afyon", "Ankara"]
```

Seeder içinde şu şekilde okursun:

```php
$cities = json_decode(file_get_contents(database_path('data/cities.json')), true);
foreach ($cities as $city) {
    DB::table('cities')->insert([
        'name' => $city,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
```

---

### ✅ Özet

| Aşama | Ne Yaptık?                                          |
| ----- | --------------------------------------------------- |
| 1     | `cities` tablosunu migration ile oluşturduk         |
| 2     | Seeder ile şehirleri tanımladık                     |
| 3     | Veritabanına seed ile ekledik                       |
| 4     | Tek komutla hepsini kurduk (`migrate:fresh --seed`) |
