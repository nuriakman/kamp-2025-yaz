# Laravel’de `Tinker` içinde Eloquent sorguları

## ✅ Doğru Sıra Örneği

```php
\App\Models\Product::where('stock', '>', 0)
    ->orderBy('price')
    ->get(['id', 'name']);
```

Bu sıralama mantıklı çünkü:

1. `where()` → Filtreleme
2. `orderBy()` → Sıralama
3. `get()` → Veriyi alma

---

## ❌ Yanlış Sıra Örneği

```php
\App\Models\Product::get()->where('stock', '>', 0);
```

> Bu durumda önce tüm ürünler veritabanından alınır (`get()`), sonra PHP tarafında `Collection`'a `where()` uygulanır.

Bu, performans açısından **yanlıştır** çünkü:

- `get()` veritabanından **tüm ürünleri çeker**
- `where()` ise veriyi **Laravel içinde** filtreler (yani veritabanı çalışmaz)

---

## 🎯 Önemli Nokta: Sorguyu "çalıştıran" metotlar her şeyi bitirir.

### 🔴 Sorguyu bitiren (sonlandıran) metotlar:

- `get()`
- `first()`
- `count()`
- `pluck()`
- `exists()`

Bunlardan sonra zincire başka şeyler ekleyemezsin, çünkü artık veriler **sorgulanmış ve getirilmiş olur.**

## 🔁 Karşılaştırma Tablosu

| Metot       | Ne yapar?            | Veritabanında mı çalışır? | Zincire eklenebilir mi? |
| ----------- | -------------------- | ------------------------- | ----------------------- |
| `where()`   | Filtreleme           | ✅ Evet                   | ✅ Evet                 |
| `orderBy()` | Sıralama             | ✅ Evet                   | ✅ Evet                 |
| `get()`     | Veriyi getirir       | ✅ Evet                   | ❌ Hayır                |
| `first()`   | İlk kaydı getirir    | ✅ Evet                   | ❌ Hayır                |
| `count()`   | Kayıt sayısını döner | ✅ Evet                   | ❌ Hayır                |
| `pluck()`   | Tek kolon döner      | ✅ Evet                   | ❌ Hayır                |

---

## 🧠 Özet

🔹 `get()`'i her zaman en sona koy.
🔹 `where()` ve `orderBy()` sıraları genelde esnek ama **önce filtrele, sonra sırala** prensibi mantıklıdır.
🔹 `get()`'ten sonra gelen `where()` artık veritabanında çalışmaz, `Collection` içindeki filtrelemeye döner.

## 🧩 Eloquent kullanım detayları

**Eloquent Formülü: "SELECT → WHERE → ORDER → LIMIT → EXECUTE"**

Bu formülün temel yapısı şudur:

```php
Model::select(...)->where(...)->orderBy(...)->limit(...)->get();
```

> 🎯 Her adım isteğe bağlıdır ama sıralama **performans ve okuma kolaylığı** için önerilir.

---

## 🔣 FORMÜLÜN AÇILIMI

| Aşama       | Eloquent Metodu                 | Açıklama                                                              |
| ----------- | ------------------------------- | --------------------------------------------------------------------- |
| **SELECT**  | `select('kolon1', 'kolon2')`    | Hangi sütunları çekmek istiyoruz? (`get(['id', 'name'])` de olabilir) |
| **WHERE**   | `where('alan', 'değer')`        | Filtreleme (birden çok `where` kullanılabilir)                        |
| **ORDER**   | `orderBy('kolon', 'asc')`       | Sıralama (ASC varsayılan)                                             |
| **LIMIT**   | `limit(10)` / `take(5)`         | Kaç kayıt çekileceğini belirle                                        |
| **EXECUTE** | `get()` / `first()` / `count()` | Sorguyu çalıştır (bu noktada SQL'e dönüşür ve veri çekilir)           |

---

## 🔍 Uygulamalı Örnekler

### Örnek 1: Basit ürün listesi

```php
\App\Models\Product::select('id', 'name', 'price')
    ->where('stock', '>', 0)
    ->orderBy('price')
    ->limit(5)
    ->get();
```

### Örnek 2: İlk aktif kullanıcıyı bul

```php
\App\Models\User::where('aktif', true)
    ->orderBy('created_at', 'desc')
    ->first();
```

### Örnek 3: Sayfa başına 10 kayıt (sayfalama yoksa)

```php
\App\Models\Post::where('status', 'yayinda')
    ->orderBy('updated_at', 'desc')
    ->take(10)
    ->get();
```

---

## 🧠 BONUS: İlişki varsa → with() her zaman `where`'den önce yazılır

```php
\App\Models\Product::with('category')
    ->where('price', '>', 100)
    ->orderBy('name')
    ->get();
```

---

## 🧾 Kısa Not: `get()` → sonuçları alır, `toSql()` → SQL sorgusunu gösterir

```php
// SQL görmek için:
\App\Models\Product::where('stock', '>', 0)->toSql();

// Gerçek veriyi almak için:
\App\Models\Product::where('stock', '>', 0)->get();
```

---

## 📌 Hatırlatma

🔸 `get()` → çoklu sonuç
🔸 `first()` → tek sonuç
🔸 `count()` → sayı döner
🔸 `pluck('name')` → sadece istenen alan döner (tek kolon)

---

## 🧩 Tek Cümlelik Formül Özeti:

> **“Model'den başla, filtrele (`where`), sırala (`orderBy`), sınırla (`limit`), sonra çalıştır (`get`, `first` veya `count`).”**

---

# Laravel Eloquent için **Cheat Sheet (Kopya Kağıdı)**

---

# 📘 Laravel Eloquent Cheat Sheet

### 🚀 _SELECT → WHERE → ORDER → LIMIT → EXECUTE_

---

## 🧱 Temel Sorgu Şablonu:

```php
Model::select([...])       // SELECT
     ->where(...)          // WHERE
     ->orderBy(...)        // ORDER
     ->limit(...)          // LIMIT (veya take())
     ->get();              // EXECUTE
```

---

## 🧩 Temel Komutlar

| Amaç                      | Kod                                      |
| ------------------------- | ---------------------------------------- |
| Tüm kayıtları çek         | `Model::all();`                          |
| İlk kaydı al              | `Model::first();`                        |
| Koşullu veri çek          | `Model::where('durum', 'aktif')->get();` |
| Belirli alanları çek      | `Model::select('id', 'ad')->get();`      |
| Sıralama                  | `orderBy('created_at', 'desc')`          |
| İlk 10 kayıt              | `take(10)` veya `limit(10)`              |
| Sayı al                   | `count()`                                |
| Belirli bir alanı listele | `pluck('ad')`                            |

---

## 🔁 Zincirleme Kullanım

```php
Product::select('id', 'name', 'price')
       ->where('stock', '>', 0)
       ->orderBy('price', 'asc')
       ->take(5)
       ->get();
```

---

## 🔗 İlişkilerle Veri

| Amaç                   | Kod                               |
| ---------------------- | --------------------------------- |
| İlişkiyle birlikte çek | `Model::with('relation')->get();` |
| Nested ilişki          | `with('relation.nested')`         |
| İlişki sayısı          | `withCount('relation')`           |

**Örnek:**

```php
Product::with('category')
       ->where('price', '>', 100)
       ->get();
```

---

## 🔍 Arama & Filtreleme

```php
User::where('name', 'like', '%ahmet%')->get();
```

---

## 🔄 Sorgu Sonrası (Collection üstünde)

```php
Product::all()->where('stock', '>', 0); // PHP'de çalışır
```

> ⚠️ Bu veritabanı yerine PHP belleğinde filtreleme yapar. Büyük veri için verimsizdir.

---

## 🛠️ Faydalı Ekstra Komutlar

| Komut                          | Açıklama                         |
| ------------------------------ | -------------------------------- |
| `toSql()`                      | SQL sorgusunu gösterir           |
| `dd()` veya `dump()`           | Veriyi incelemek için kullanılır |
| `find(id)`                     | ID’ye göre tek kayıt getirir     |
| `firstWhere('kolon', 'değer')` | Koşullu ilk kayıt                |

---

## 🧠 İpucu: Sorguyu her zaman `get()`, `first()` veya `count()` ile **bitir**.

---

## ✅ Tinker’da Kullanım

```bash
php artisan tinker
```

```php
\App\Models\Product::where('stock', '>', 0)->orderBy('price')->get();
```

---

## 📌 Model’e fillable yazmadan `create()` çalışmaz!

```php
// Model içinde:
protected $fillable = ['name', 'price', 'stock'];
```

```php
Product::create([
  'name' => 'Masa',
  'price' => 199.99,
  'stock' => 5
]);
```

---

## 📄 PDF İster misin?

İstersen bu cheat sheet’i PDF olarak da sana özel hazırlayabilirim. Tek tıkla indirebileceğin formatta sunabilirim. İster misin?
