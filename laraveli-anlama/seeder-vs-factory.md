# Seeder vs Factory

Laravel'de gerçek ve sahte veriler farklı amaçlarla ve farklı yollarla üretilir.

- **Seeder** → Veritabanına **hazır ve belirli** verileri ekler (ör. admin kullanıcı).
- **Factory** → **Rastgele veya kural bazlı** test verisi üretir (faker kullanır).

Aşağıda bunu çok net bir tablo ve örneklerle açıklıyorum:

---

## ✅ Gerçek ve Sahte Verilerin Kullanımı

| Veri Türü      | Nerede Kullanılır?            | Üretim Yöntemi                  | Açıklama                                                                      |
| -------------- | ----------------------------- | ------------------------------- | ----------------------------------------------------------------------------- |
| 🏙️ Gerçek veri | Şehir, il, ilçe, kategori vs. | **Seeder (elle yazılır)**       | Bu veriler sabittir, gerçek hayattaki karşılığı vardır. Fake olması istenmez. |
| 🧪 Sahte veri  | Ürün, kullanıcı, sipariş vs.  | **Factory (fake ile üretilir)** | Geliştirme ve test için uydurma ama gerçekçi verilerdir.                      |

---

## 🧱 Örneklerle Gösterelim

### 1. 🔐 Gerçek Veri — Seeder ile

```php
// database/seeders/CitySeeder.php
$cities = ['Ankara', 'İstanbul', 'İzmir'];
foreach ($cities as $city) {
    DB::table('cities')->insert(['name' => $city]);
}
```

> ✔️ Bu veriler **değişmez**. Her ortamda aynıdır.
> ✔️ Üretimde de kullanılır.

---

### 2. 🎲 Sahte Veri — Factory ile

```php
// database/factories/ProductFactory.php
public function definition()
{
    return [
        'name' => fake()->words(2, true),    // Örn: "Akıllı Saat"
        'price' => fake()->randomFloat(2, 10, 500),
    ];
}
```

> ✔️ Bu veriler **her çalıştırmada farklı** olur.
> ✔️ Üretimde kullanılmaz, sadece geliştirme/test amaçlıdır.

---

## 🤝 Seeder ve Factory Birlikte de Kullanılır

Örneğin:

- Seeder ile **şehirleri** ekleriz.
- Seeder içinde **factory ile sahte kullanıcıları** şehirlerle ilişkilendiririz.

```php
// CitySeeder: Gerçek veri
// UserSeeder: Sahte veriler

User::factory()
    ->count(50)
    ->create([
        'city_id' => City::inRandomOrder()->first()->id,
    ]);
```

---

## 🎯 Sonuç

| Ne?                          | Nasıl?           | Ne zaman?              |
| ---------------------------- | ---------------- | ---------------------- |
| Gerçek veriler (şehir, ilçe) | Seeder (elle)    | Geliştirme + Üretim    |
| Sahte veriler (isim, ürün)   | Factory + Seeder | Sadece geliştirme/test |

- **Seeder**: sabit/gerçek veriler içindir
- **Factory**: sahte/otomatik veriler içindir
- **Seeder içinde factory kullanarak sahte veriler de kaydedebilirsin**
