# Helper Nedir?

Laravel’de **Helper fonksiyonlar**, uygulama genelinde tekrar tekrar ihtiyaç duyulan küçük ve bağımsız işlevleri barındırmak için kullanılır. Bu fonksiyonlar genellikle **global erişilebilir** olacak şekilde tanımlanır ve her yerden kolayca çağrılabilir.

---

## 🧠 NEDİR?

Helper fonksiyonlar, **servis, controller, model gibi sınıflardan bağımsız** çalışabilen, küçük, tekrar kullanılabilir işlemleri içerir:

| Örnek İşlem              | Açıklama                         |
| ------------------------ | -------------------------------- |
| `format_price(199.9)`    | 199,90 ₺ şeklinde döndürür       |
| `slugify("Deneme Ürün")` | "deneme-urun" üretir             |
| `isWeekend($date)`       | Cumartesi/Pazar mı? kontrol eder |
| `calculateTax($amount)`  | Vergi hesaplaması yapar          |

---

## 🛠 NASIL OLUŞTURULUR?

### 1. Dosya Oluştur

`app/Helpers` klasörü oluştur ve içine `AppHelper.php` adında bir dosya koy:

```bash
mkdir app/Helpers
touch app/Helpers/AppHelper.php
```

### 2. Fonksiyonları Yaz

**app/Helpers/AppHelper.php**

```php
<?php

if (!function_exists('format_price')) {
    function format_price($price, $currency = '₺')
    {
        return number_format($price, 2, ',', '.') . ' ' . $currency;
    }
}

if (!function_exists('isWeekend')) {
    function isWeekend($date)
    {
        return in_array(date('N', strtotime($date)), [6, 7]); // 6: Cumartesi, 7: Pazar
    }
}
```

---

### 3. `composer.json` ile Otomatik Yüklet

**composer.json** içinde `"autoload"` bölümüne ekle:

```json
"autoload": {
    "files": [
        "app/Helpers/AppHelper.php"
    ]
}
```

> ✅ Not: `"psr-4"` değil `"files"` altına eklemen önemli. Bu sayede helper fonksiyonlar **otomatik olarak global yükenir.**

---

### 4. Composer Autoload Güncelle

```bash
composer dump-autoload
```

---

### 5. Kullan

Artık her yerde doğrudan çağırabilirsin:

```php
echo format_price(123.45);  // 123,45 ₺
if (isWeekend('2025-07-27')) {
    echo 'Hafta sonu';
}
```

---

## 🧰 NEREDE KULLANILMALI?

| Kullanım Yeri | Örnek                                             |
| ------------- | ------------------------------------------------- |
| Controller    | `$price = format_price($order->total);`           |
| View (Blade)  | `{{ format_price($product->price) }}`             |
| Service       | `if (isWeekend(now())) { ... }`                   |
| Test          | `assertEquals('1.000,00 ₺', format_price(1000));` |

---

## 🧭 NE ZAMAN HELPER KULLAN, NE ZAMAN SERVICE?

| Durum                                | Kullan            |
| ------------------------------------ | ----------------- |
| Küçük, bağımsız işlev                | ✅ Helper         |
| Birden fazla sınıfın ihtiyacı var    | ✅ Helper         |
| Bağımlılık içeriyor (model, API vs.) | ❌ Service kullan |
| Karmaşık iş mantığı                  | ❌ Service kullan |

---

## 🛡️ İPUÇLARI

- Her helper fonksiyonu için `function_exists` ile çakışmayı önle.
- Çok fazla helper fonksiyonu varsa, **kategoriye göre dosyala** (`StringHelper.php`, `DateHelper.php`, `MathHelper.php`).
- Test yazmayı unutma.

---

## 📦 Laravel’in Kendi Helper Fonksiyonları

Laravel, zaten birçok yerleşik helper sunar:

| Fonksiyon               | İşlev                            |
| ----------------------- | -------------------------------- |
| `route('home')`         | Route URL’i döner                |
| `asset('img/logo.png')` | Public dizininden yol döner      |
| `now()`                 | Şu anki zamanı verir             |
| `bcrypt('123')`         | Şifreyi hashler                  |
| `str_slug()`            | Slug oluşturur (eski sürümlerde) |

Laravel helper listesi: [https://laravel.com/docs/helpers](https://laravel.com/docs/helpers)

---

## 🧠 Helper Nedir? Daha detaylı anlatım

Laravel'de **Helper** konusu, birçok kişinin karıştırdığı ama aslında çok sade bir kavramdır. Sana bunu adım adım ve sade bir şekilde anlatacağım:

**Helper**, Laravel'de projenin her yerinden ulaşılabilen **küçük, genel amaçlı fonksiyonlardır**.

✔️ Kod tekrarını azaltır
✔️ Tek bir yerde tanımlanır, her yerde kullanılır
✔️ Laravel'in kendi helper’ları da vardır (`asset()`, `route()`, `now()` gibi)
✔️ İstersen kendi özel helper fonksiyonlarını da yazabilirsin

---

## 🧰 Laravel'in Hazır Helper Örnekleri

| Fonksiyon                      | Ne işe yarar?                                      |
| ------------------------------ | -------------------------------------------------- |
| `asset('css/app.css')`         | `public/` klasöründen URL oluşturur                |
| `route('home')`                | Route adına göre URL döner                         |
| `now()`                        | Şu anki zamanı döner (`Carbon\Carbon`)             |
| `str_slug('Laravel Yardımcı')` | `laravel-yardimci` gibi bir URL dostu ifade üretir |

---

## 🛠️ Kendi Helper Fonksiyonunu Yazmak

### 1. `helpers.php` Dosyası Oluştur

`app/Helpers/helpers.php` adında bir dosya aç:

```php
<?php

if (!function_exists('para')) {
    function para($deger, $birim = '₺')
    {
        return number_format($deger, 2, ',', '.') . " $birim";
    }
}
```

> Bu fonksiyon, 12345.6 → `12.345,60 ₺` şeklinde gösterir.

---

### 2. Composer ile Otomatik Yüklensin

`composer.json` içine şu satırı ekle:

```json
"autoload": {
    "files": [
        "app/Helpers/helpers.php"
    ]
}
```

Sonra şu komutu çalıştır:

```bash
composer dump-autoload
```

---

### 3. Artık Her Yerden Kullanabilirsin

```php
echo para(1500.75); // ➜ 1.500,75 ₺
```

- Controller’da kullanabilirsin
- View’de kullanabilirsin
- Seeder’da bile çalışır

---

## 💡 Neden Helper Kullanılır?

| Amaç                       | Açıklama                                                                                       |
| -------------------------- | ---------------------------------------------------------------------------------------------- |
| 🔁 Tekrarı önlemek         | Aynı kodu her yerde yazmamak için                                                              |
| 🌍 Genel kullanılabilirlik | Controller, view, middleware fark etmeden çalışır                                              |
| 🧼 Temizlik                | Servis katmanına girmeyecek basit işleri ayırır (örneğin: para formatlama, gün/ay çevirme vs.) |

---

## ✅ Kullanım Örnekleri

| İhtiyaç                    | Helper               |
| -------------------------- | -------------------- |
| Para formatla              | `para(1234.5)`       |
| JSON cevabı standartlaştır | `jsonSuccess($data)` |
| Türkçe tarih               | `tarih_tr(now())`    |
| IP adresini logla          | `log_ip()`           |

---

## 🧠 Service vs Helper Farkı

| Özellik           | Helper               | Service                            |
| ----------------- | -------------------- | ---------------------------------- |
| Yapısı            | Basit fonksiyon      | Sınıf (OOP)                        |
| Amaç              | Küçük yardımcı işler | İş mantığı, kurallar               |
| Bağımlılık        | Yok                  | Varsa constructor injection        |
| Nerede Kullanılır | Heryerde             | Genellikle Controller, Command vb. |

> Helper küçük işler içindir. Büyük yapılar için Service kullanılır.

---

## 🔚 Özet

| Soru                                 | Cevap                                         |
| ------------------------------------ | --------------------------------------------- |
| Helper nedir?                        | Küçük, genel amaçlı fonksiyonlardır.          |
| Ne işe yarar?                        | Kod tekrarını azaltır, kodu sadeleştirir.     |
| Nerede yazılır?                      | `app/Helpers/helpers.php` dosyasında.         |
| Nerede kullanılır?                   | Her yerde: controller, view, command, seeder… |
| Laravel’in kendi helper’ları var mı? | Evet: `now()`, `asset()`, `route()` gibi.     |

---
