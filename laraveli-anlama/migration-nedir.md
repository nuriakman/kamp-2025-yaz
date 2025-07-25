# Laravel Migration Nedir?

Laravel Migration (göç dosyaları), veritabanı tablolarını **kod yazarak** oluşturmanı ve yönetmeni sağlayan bir sistemdir.

Kısaca: ➡️ **Veritabanı yapısını (schema)** PHP koduyla tanımlamanı sağlar.

## Ne İşe Yarar?

| Amaç                                     | Açıklama                                                                             |
| ---------------------------------------- | ------------------------------------------------------------------------------------ |
| 🏗️ **Veritabanı tablolarını oluşturmak** | create_users_table gibi migration dosyalarıyla tabloları oluşturursun.               |
| 🛠️ **Kolay güncelleme**                  | Tabloya yeni sütun ekleme/silme gibi işlemler kodla yönetilir.                       |
| 👨‍💻 **Takım çalışmasına uygundur**        | Kod deposuna migration dosyaları eklenir; herkes aynı veritabanı yapısını elde eder. |
| ⏪ **Sürüm kontrolü**                    | Geriye dönmek istersen rollback komutu ile önceki duruma dönebilirsin.               |

---

## Zorunlu mu?

**Hayır, teknik olarak zorunlu değildir.**
Ama aşağıdaki durumlar varsa **şiddetle tavsiye edilir**:

- ✅ Projeyi ekiple geliştiriyorsan
- ✅ Geliştirme ortamını sıfırdan kurman gerekiyorsa
- ✅ CI/CD gibi otomasyonlar kullanıyorsan
- ✅ Yapı üzerinde sık sık değişiklik yapıyorsan

Eğer:

- Tek başına çalışıyor
- phpMyAdmin veya Adminer üzerinden tabloları elle oluşturuyorsan
- Ve projen küçükse

o zaman migration kullanmadan da devam edebilirsin. Ama bu uzun vadede seni kısıtlayabilir.

---

## Küçük Örnek

```bash
php artisan make:migration create_products_table
```

```php
// database/migrations/2025_07_24_000000_create_products_table.php

public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('products');
}
```

Uygulamak için:

```bash
php artisan migrate
```

---

## Özet

| Soru                 | Cevap                                                                            |
| -------------------- | -------------------------------------------------------------------------------- |
| **Migration nedir?** | Laravel'de veritabanı yapısını PHP koduyla tanımlamak için kullanılan sistemdir. |
| **Ne işe yarar?**    | Tabloları oluşturur, günceller, geri alır. Takım çalışmasına uygundur.           |
| **Zorunlu mu?**      | Hayır, ama orta/büyük projelerde kullanmak neredeyse şarttır.                    |

## 🧱 1. Migration’ın Yapısı

Her migration dosyası, Laravel tarafından oluşturulan bir PHP sınıfıdır ve iki temel metoda sahiptir:

```php
public function up() { ... }
public function down() { ... }
```

| Metot    | Görevi                                                                                            |
| -------- | ------------------------------------------------------------------------------------------------- |
| `up()`   | Veritabanında yapılacak **oluşturma veya değişiklik işlemleri** burada tanımlanır.                |
| `down()` | `php artisan migrate:rollback` gibi komutlarla geri alındığında, burada tanımlı işlemler çalışır. |

---

## 📦 2. Migration Dosyası Oluşturma

### Komut:

```bash
php artisan make:migration create_products_table
```

### Laravel, `database/migrations/` klasöründe şunu üretir:

```bash
2025_07_24_000000_create_products_table.php
```

Bu dosyada:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
```

---

## 🛠️ 3. Yaygın Olarak Kullanılan Sütun Tipleri

| Laravel Sütun                    | Açıklama                                                  |
| -------------------------------- | --------------------------------------------------------- |
| `$table->id()`                   | `id` adında bir birincil anahtar (primary key) oluşturur. |
| `$table->string('title')`        | 255 karaktere kadar bir `VARCHAR` alanı                   |
| `$table->text('description')`    | Daha uzun metinler için                                   |
| `$table->integer('stock')`       | Tamsayı değer                                             |
| `$table->boolean('is_active')`   | True/False değeri                                         |
| `$table->decimal('price', 8, 2)` | Ondalık sayı (toplam 8 karakter, 2 ondalık)               |
| `$table->timestamps()`           | `created_at` ve `updated_at` sütunlarını otomatik ekler   |

---

## 🔄 4. Migration İşlemleri

### ✅ Migration’ları Uygulamak:

```bash
php artisan migrate
```

### ⏪ Geri Almak (Rollback):

```bash
php artisan migrate:rollback
```

### 🔄 Tümünü Sıfırla ve Yeniden Yükle:

```bash
php artisan migrate:fresh
```

> Bu komut veritabanındaki tüm tabloları siler ve tüm migration’ları sıfırdan uygular. Geliştirme aşamasında faydalıdır.

---

## 🧬 5. Tablolarda İlişkiler Oluşturmak (Foreign Key)

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

Bu satır:

- `user_id` adında bir alan oluşturur,
- `users` tablosunun `id` sütununa bağlar,
- Kullanıcı silinirse onun siparişleri de silinsin (`cascade`).

---

## 🔍 6. Migration Avantajları

- ✅ Kodla veritabanı kontrolü
- ✅ Takım çalışmasında tutarlılık
- ✅ Geliştirme ve üretim ortamlarında kolay kurulum
- ✅ Geri alma ve sürüm kontrolü
- ✅ Seed ile birlikte kullanıldığında test verisi oluşturma

---

## 🧪 7. Bonus: Migration + Seeder Kullanımı

Eğer migration’dan sonra otomatik örnek veri istiyorsan:

```bash
php artisan make:seeder ProductSeeder
```

Ve `DatabaseSeeder.php` içine eklersin:

```php
$this->call(ProductSeeder::class);
```

Sonra:

```bash
php artisan migrate:fresh --seed
```

---

## 📌 Sonuç

Migration sistemi:

| Durum                        | Tavsiye                                  |
| ---------------------------- | ---------------------------------------- |
| Küçük, tek kişilik projeler  | El ile tablo kurmak kabul edilebilir     |
| Orta / Büyük projeler        | Migration şart gibi                      |
| Ekip çalışması               | Migration kullanmak zorundasın           |
| DevOps / CI / test ortamları | Migration + Seeder birlikte kullanılmalı |

---
