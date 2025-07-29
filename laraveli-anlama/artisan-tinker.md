# 🔧 PHP Artisan Tinker - Ders Notu

## 📋 İçindekiler

1. [Tinker Nedir?](#tinker-nedir)
2. [Ne Zaman Kullanılır?](#ne-zaman-kullanılır)
3. [Tinker'ı Başlatma](#tinkeri-başlatma)
4. [Temel Komutlar](#temel-komutlar)
5. [Database İşlemleri](#database-işlemleri)
6. [Model İşlemleri](#model-işlemleri)
7. [Pratik Örnekler](#pratik-örnekler)
8. [İpuçları ve Püf Noktalar](#ipuçları-ve-püf-noktalar)

---

## 🤔 Tinker Nedir?

**PHP Artisan Tinker**, Laravel'in sunduğu interaktif bir **REPL** (Read-Eval-Print Loop) aracıdır.

### 🎯 Temel Özellikler:

- Laravel uygulamanızı **canlı olarak** test edebilirsiniz
- Kod yazmadan önce **hızlıca deneme** yapabilirsiniz
- Database işlemlerini **güvenli şekilde** test edebilirsiniz
- Model'lerinizi ve ilişkilerini **anında** kontrol edebilirsiniz
- **Debugging** için mükemmel bir araçtır

> 💡 **Analoji**: Tinker, Laravel uygulamanız için bir "canlı laboratuvar" gibidir. Kodunuzu yazmadan önce deney yapabileceğiniz güvenli bir ortam!

---

## ⏰ Ne Zaman Kullanılır?

### 🔍 **Geliştirme Aşamasında:**

- Yeni bir özellik geliştirmeden önce **test etmek**
- Model ilişkilerini **kontrol etmek**
- Database sorgularını **denemek**
- API response'larını **test etmek**

### 🐛 **Debug Aşamasında:**

- Hataların **kaynağını bulmak**
- Veri akışını **takip etmek**
- Model'lerin doğru çalışıp çalışmadığını **kontrol etmek**

### 📊 **Veri İşlemlerinde:**

- Toplu veri **ekleme/güncelleme**
- Database'deki verileri **hızlıca kontrol etme**
- Test verisi **oluşturma**

---

## 🚀 Tinker'ı Başlatma

```bash
# Terminal'de Laravel proje klasöründe
php artisan tinker
```

### ✅ Başarılı Başlatma:

```
Psy Shell v0.11.22 (PHP 8.2.0 — cli) by Justin Hileman
>>>
```

### 🚪 Tinker'dan Çıkış:

```php
>>> exit
# veya Ctrl+C
```

---

## 📝 Temel Komutlar

### 🔧 **Yardım Komutları:**

```php
>>> help
>>> help ls        // Belirli komut hakkında yardım
>>> ls             // Mevcut değişkenleri listele
>>> clear          // Ekranı temizle
```

### 📋 **Değişken Kullanımı:**

```php
>>> $name = "Laravel"
>>> echo $name
Laravel

>>> $numbers = [1, 2, 3, 4, 5]
>>> print_r($numbers)
```

### 🔄 **Fonksiyon Çağırma:**

```php
>>> strlen("Hello World")
11

>>> date('Y-m-d H:i:s')
"2024-01-15 14:30:45"
```

---

## 🗄️ Database İşlemleri

### 📊 **Database Bağlantısını Test Etme:**

```php
>>> DB::connection()->getPdo()
// PDO bağlantı objesi döner

>>> DB::select('SELECT DATABASE()')
// Aktif database adını gösterir
```

### 🔍 **Raw SQL Sorguları:**

```php
>>> DB::select('SELECT * FROM categories')
>>> DB::select('SELECT * FROM products WHERE price > ?', [100])
>>> DB::select('SELECT COUNT(*) as total FROM products')
```

### ➕ **Veri Ekleme:**

```php
>>> DB::insert('INSERT INTO categories (name, description) VALUES (?, ?)', ['Elektronik', 'Elektronik ürünler'])

>>> DB::table('categories')->insert([
...     'name' => 'Giyim',
...     'description' => 'Giyim ürünleri',
...     'created_at' => now(),
...     'updated_at' => now()
... ])
```

### 🔄 **Veri Güncelleme:**

```php
>>> DB::update('UPDATE categories SET description = ? WHERE id = ?', ['Güncellenmiş açıklama', 1])

>>> DB::table('categories')->where('id', 1)->update(['name' => 'Yeni İsim'])
```

### ❌ **Veri Silme:**

```php
>>> DB::delete('DELETE FROM categories WHERE id = ?', [1])
>>> DB::table('categories')->where('id', 1)->delete()
```

---

## 🏗️ Model İşlemleri

### 📋 **Model'leri Import Etme:**

```php
>>> use App\Models\Category
>>> use App\Models\Product
```

### 🔍 **Veri Sorgulama:**

```php
// Tüm kategorileri getir
>>> Category::all()

// İlk kategoriyi getir
>>> Category::first()

// ID ile kategori bul
>>> Category::find(1)

// Belirli koşulla ara
>>> Category::where('name', 'Elektronik')->first()

// Sayfa sayfa getir
>>> Category::paginate(5)
```

### ➕ **Yeni Veri Ekleme:**

```php
// Yöntem 1: Yeni instance oluştur
>>> $category = new Category()
>>> $category->name = "Kitap"
>>> $category->description = "Kitap ve dergi ürünleri"
>>> $category->save()

// Yöntem 2: Create kullan
>>> Category::create([
...     'name' => 'Spor',
...     'description' => 'Spor malzemeleri'
... ])

// Yöntem 3: firstOrCreate kullan
>>> Category::firstOrCreate(
...     ['name' => 'Oyuncak'],
...     ['description' => 'Çocuk oyuncakları']
... )
```

### 🔄 **Veri Güncelleme:**

```php
>>> $category = Category::find(1)
>>> $category->name = "Güncellenmiş İsim"
>>> $category->save()

// Veya toplu güncelleme
>>> Category::where('id', 1)->update(['name' => 'Yeni İsim'])
```

### ❌ **Veri Silme:**

```php
>>> $category = Category::find(1)
>>> $category->delete()

// Veya direkt silme
>>> Category::destroy(1)
>>> Category::destroy([1, 2, 3])  // Birden fazla
```

---

## 💡 Pratik Örnekler

### 🏪 **Kategori ve Ürün İşlemleri:**

```php
// 1. Kategoriler oluştur
>>> Category::create(['name' => 'Elektronik', 'description' => 'Elektronik ürünler'])
>>> Category::create(['name' => 'Giyim', 'description' => 'Giyim ürünleri'])
>>> Category::create(['name' => 'Ev & Yaşam', 'description' => 'Ev eşyaları'])

// 2. Kategorileri listele
>>> Category::all()->pluck('name', 'id')

// 3. Ürün ekle
>>> Product::create([
...     'name' => 'iPhone 15',
...     'description' => 'Apple iPhone 15 128GB',
...     'price' => 45000.00,
...     'stock' => 10,
...     'category_id' => 1
... ])

>>> Product::create([
...     'name' => 'Samsung Galaxy S24',
...     'description' => 'Samsung Galaxy S24 256GB',
...     'price' => 42000.00,
...     'stock' => 15,
...     'category_id' => 1
... ])

// 4. Ürünleri kategoriye göre listele
>>> Product::where('category_id', 1)->get()

// 5. Fiyatı 40000'den büyük ürünler
>>> Product::where('price', '>', 40000)->get()

// 6. Stok durumunu kontrol et
>>> Product::where('stock', '<', 5)->get()
```

### 🔗 **İlişkiler ile Çalışma:**

```php
// Kategori ve ürünlerini birlikte getir
>>> $category = Category::with('products')->find(1)
>>> $category->products

// Bir ürünün kategorisini getir
>>> $product = Product::with('category')->find(1)
>>> $product->category->name

// Kategoriye ait ürün sayısı
>>> Category::withCount('products')->get()
```

### 📊 **İstatistiksel Sorgular:**

```php
// Toplam ürün sayısı
>>> Product::count()

// En pahalı ürün
>>> Product::max('price')

// En ucuz ürün
>>> Product::min('price')

// Ortalama fiyat
>>> Product::avg('price')

// Toplam stok
>>> Product::sum('stock')

// Kategoriye göre ürün sayıları
>>> Product::selectRaw('category_id, COUNT(*) as product_count')
...     ->groupBy('category_id')
...     ->get()
```

### 🧪 **Test Verisi Oluşturma:**

```php
// Toplu kategori ekleme
>>> $categories = [
...     ['name' => 'Bilgisayar', 'description' => 'Bilgisayar ve aksesuarları'],
...     ['name' => 'Telefon', 'description' => 'Cep telefonu ve aksesuarları'],
...     ['name' => 'Tablet', 'description' => 'Tablet bilgisayarlar']
... ];
>>>
>>> foreach($categories as $category) {
...     Category::create($category);
... }

// Random ürün oluşturma
>>> for($i = 1; $i <= 10; $i++) {
...     Product::create([
...         'name' => 'Ürün ' . $i,
...         'description' => 'Test ürünü açıklaması ' . $i,
...         'price' => rand(100, 5000),
...         'stock' => rand(0, 100),
...         'category_id' => rand(1, 3)
...     ]);
... }
```

---

## 🎯 İpuçları ve Püf Noktalar

### ✅ **Yapılması Gerekenler:**

1. **Her zaman backup alın** - Özellikle production veritabanında çalışırken
2. **Transaction kullanın** - Büyük işlemler için:

   ```php
   >>> DB::beginTransaction()
   >>> // İşlemleriniz...
   >>> DB::commit()  // veya DB::rollback()
   ```

3. **Sonuçları kontrol edin:**
   ```php
   >>> $result = Category::create(['name' => 'Test'])
   >>> $result->id  // Oluşturulan ID'yi kontrol et
   ```

### ⚠️ **Dikkat Edilmesi Gerekenler:**

1. **Production'da dikkatli olun** - Tinker gerçek veritabanında çalışır!
2. **Büyük sorgulardan kaçının** - Memory limit'e takılabilirsiniz
3. **Syntax hatalarına dikkat edin** - Tinker'da hata ayıklama zor olabilir

### 🔧 **Yararlı Kısayollar:**

```php
// Son komutu tekrar çalıştır
>>> history
>>> !1  // 1. komutu tekrar çalıştır

// Değişken içeriğini güzel görüntüle
>>> dump($variable)
>>> dd($variable)  // Dump and die

// Model'in SQL sorgusunu göster
>>> Category::where('name', 'Elektronik')->toSql()
```

### 🐛 **Hata Ayıklama:**

```php
// Log'ları kontrol et
>>> \Log::info('Test mesajı')

// Cache'i temizle
>>> \Cache::flush()

// Config değerlerini kontrol et
>>> config('app.name')
>>> config('database.default')
```

---

## 🎯 Controller Metodu Tinker İçinden Nasıl Kullanılır?

### Örnek senaryo:

```php
// App\Http\Controllers\ProductController
class ProductController extends Controller
{
    public function list()
    {
        return \App\Models\Product::with('category')->get();
    }
}
```

### Tinker’da çağırmak:

```bash
php artisan tinker
```

```php
app(\App\Http\Controllers\ProductController::class)->list();
```

✅ Bu komut, `ProductController::list()` metodunu çağırır ve sonucu döner.

---

## 🎉 Özet

**PHP Artisan Tinker** Laravel geliştirici için vazgeçilmez bir araçtır:

- ✅ **Hızlı test** imkanı sağlar
- ✅ **Güvenli deneme** ortamı sunar
- ✅ **Database işlemlerini** kolaylaştırır
- ✅ **Model ilişkilerini** test etmeyi sağlar
- ✅ **Debugging** sürecini hızlandırır

> 💡 **Tavsiye**: Tinker'ı düzenli olarak kullanarak Laravel'in gücünü keşfedin. Her yeni özellik öğrendiğinizde Tinker'da deneyin!

---

## 📚 Ek Kaynaklar

- [Laravel Tinker Dokümantasyonu](https://laravel.com/docs/artisan#tinker)
- [Eloquent ORM Rehberi](https://laravel.com/docs/eloquent)
- [Database Query Builder](https://laravel.com/docs/queries)

---

**🎯 Sonraki Adım**: Tinker'ı açın ve yukarıdaki örnekleri tek tek deneyin! Pratik yaparak öğrenme en etkili yöntemdir.
