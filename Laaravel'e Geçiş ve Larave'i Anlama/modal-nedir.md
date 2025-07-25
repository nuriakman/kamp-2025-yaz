# Modal Nedir?

Laravel’de (ve genel olarak MVC mimarisinde) **Model**, yazılımın "veri" ile ilgili kısmını temsil eden bir yapıdır. Veritabanı ile doğrudan iletişim kurar, yani veri alma, kaydetme, güncelleme ve silme işlemleri bu katmanda gerçekleşir.

Aşağıda **model nedir**, **neden kullanılır** ve **nasıl çalışır** detaylı bir şekilde açıklanmıştır:

---

## ✅ 1. MODEL NEDİR?

Model, bir veritabanı tablosunu temsil eden bir **PHP sınıfıdır**.

Laravel’de her tablo için bir Model sınıfı oluşturman önerilir. Örneğin:

- `users` tablosu için `User` modeli
- `products` tablosu için `Product` modeli

Bu sınıf:

- İlgili veritabanı tablosuyla eşleşir,
- Veritabanı işlemlerini basit hale getirir,
- Laravel’in güçlü ORM sistemi olan **Eloquent**’i kullanır.

---

## ✅ 2. NEDEN MODEL KULLANILIR?

### 🔸 2.1. SQL YAZMAKTAN KURTARIR

Eski yöntemde:

```php
$sql = "SELECT * FROM users WHERE id = 5";
$result = mysqli_query($conn, $sql);
```

Model ile:

```php
$user = User::find(5);
```

### 🔸 2.2. Veriyi Nesne Olarak Temsil Eder

Model sayesinde veritabanındaki bir satır, bir nesne olur:

```php
$user = User::find(1);
echo $user->name;
```

### 🔸 2.3. Kodun Düzenli ve Anlaşılır Olmasını Sağlar

Her model yalnızca **bir tabloyu** temsil eder. Kodlar dağınık olmaz. Controller, sadece veriyi ne yapacağını bilir; veriyle nasıl çalışılacağını model belirler.

### 🔸 2.4. Güvenlik Sağlar (Mass Assignment, Fillable, Guarded)

Model içinde hangi alanların toplu olarak doldurulabileceğini belirleyebilirsin:

```php
protected $fillable = ['name', 'email'];
```

---

## ✅ 3. BİR MODEL NASIL OLUŞTURULUR?

```bash
php artisan make:model Product
```

Bu komut, `app/Models/Product.php` dosyasını oluşturur.

İçeriği:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];
}
```

---

## ✅ 4. MODEL NE İŞE YAPAR? – ÖRNEKLERLE

### 🔹 4.1. Kayıt Ekleme

```php
Product::create([
    'name' => 'Telefon',
    'price' => 12000,
]);
```

### 🔹 4.2. Veri Çekme

```php
$products = Product::all();
```

### 🔹 4.3. Tek Kayıt Bulma

```php
$product = Product::find(1);
```

### 🔹 4.4. Güncelleme

```php
$product = Product::find(1);
$product->price = 15000;
$product->save();
```

### 🔹 4.5. Silme

```php
$product = Product::find(1);
$product->delete();
```

---

## ✅ 5. MODEL İÇİNDE EKSTRA FONKSİYONLAR

Model, sadece veri çekmez. Aynı zamanda iş mantığı da taşıyabilir.

```php
class Product extends Model
{
    public function isExpensive()
    {
        return $this->price > 10000;
    }
}
```

Kullanımı:

```php
$product = Product::find(1);
if ($product->isExpensive()) {
    echo "Bu ürün pahalı";
}
```

---

## ✅ 6. İLİŞKİLER (Relations)

Model'ler arasında **veritabanı ilişkileri** kurulabilir:

- Bir kullanıcının birçok gönderisi olabilir:

```php
class User extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }
}
```

- Her gönderinin bir kullanıcısı vardır:

```php
class Post extends Model {
    public function user() {
        return $this->belongsTo(User::class);
    }
}
```

---

## ✅ 7. SONUÇ

| Model’in Rolü      | Açıklama                                                                         |
| ------------------ | -------------------------------------------------------------------------------- |
| Veritabanı temsili | Her tabloya karşılık gelir                                                       |
| ORM ile bağlantı   | Eloquent sayesinde SQL yazmadan işlem yapar                                      |
| Kod düzeni         | Veritabanı işlemleri controller’dan ayrılır                                      |
| Güvenlik           | Mass-assignment, accessors, mutators gibi yapılarla veri kontrol altında tutulur |
| İlişkilendirme     | Tablo ilişkileri kurulabilir (hasMany, belongsTo, vb.)                           |

---

## 🧠 LARAVEL'DE MODEL — TAM ANLAMIYLA

### 📌 KISACA TANIM

Laravel'de **Model**, veritabanındaki bir tabloyu temsil eden **PHP sınıfıdır**. Bu sınıf, verileri **çekme**, **ekleme**, **güncelleme**, **silme** işlemlerini Laravel’in ORM sistemi olan **Eloquent** aracılığıyla yapar.

---

### 📁 MODEL DOSYASININ YERİ

Tüm modellerin varsayılan yeri:

```
app/Models/
```

Örnek:

```
app/Models/User.php
```

Bu sınıf, Laravel’in `users` adlı veritabanı tablosuyla bağlantı kurar (ismi otomatik eşleştirir ama elle de ayarlanabilir).

---

### 🏗️ 1. MODEL NASIL OLUŞTURULUR?

```bash
php artisan make:model Product
```

Bu komut:

- `Product` adında bir model dosyası oluşturur (`Product.php`)
- Otomatik olarak `products` tablosunu temsil eder

> 💡 Laravel çoğul/tekil ilişkisini otomatik yapar. Yani:
>
> `Product` modeli, `products` tablosunu temsil eder.

İstersen elle de tanımlayabilirsin:

```php
protected $table = 'urunler';
```

---

### 🔍 2. MODEL NEYİ TUTAR?

#### ✅ 2.1. Hangi alanlar topluca yazılabilir (`$fillable`)

```php
protected $fillable = ['name', 'price'];
```

Bu özellik, dışarıdan gelen isteklerde hangi alanların toplu olarak `create()` veya `update()` ile yazılabileceğini belirler. Güvenlik sağlar.

---

#### ✅ 2.2. Varsayılan değerler, tarih alanları, gizli alanlar

```php
protected $hidden = ['password']; // JSON'da gösterme

protected $casts = [
    'is_active' => 'boolean',
    'created_at' => 'datetime',
];
```

---

### 💡 3. NEDEN MODEL KULLANIRIZ?

| Amaç                    | Açıklama                                                                           |
| ----------------------- | ---------------------------------------------------------------------------------- |
| 🧹 Kodun düzenli olması | Controller, sadece "ne yapılacağını" bilir. Model ise "veriyi nasıl işleyeceğini". |
| 🧠 Tekrar kullanım      | Aynı veri yapısı her yerde tek bir modelle temsil edilir.                          |
| 🔐 Güvenlik             | Model, hangi alanlara izin verileceğini bilir. Mass assignment, cast'ler gibi.     |
| 📖 Okunabilirlik        | `$user->name` yazmak, `mysqli_fetch_array()` yerine çok daha anlamlıdır.           |
| 🔗 İlişkiler            | `User` modeli ile `Post` modeli arasında kolayca ilişki kurulabilir.               |

---

### 🔄 4. VERİTABANI İŞLEMLERİ MODEL ÜZERİNDEN NASIL YAPILIR?

#### 🔹 4.1. Veri eklemek:

```php
Product::create([
    'name' => 'iPhone',
    'price' => 30000
]);
```

> ✔️ Modelde `$fillable` tanımlı olmalı.

---

#### 🔹 4.2. Tüm veriyi çekmek:

```php
$products = Product::all();
```

---

#### 🔹 4.3. Tek kayıt çekmek:

```php
$product = Product::find(1);
```

---

#### 🔹 4.4. Koşullu sorgu:

```php
$cheapProducts = Product::where('price', '<', 1000)->get();
```

---

#### 🔹 4.5. Güncelleme:

```php
$product = Product::find(1);
$product->price = 25000;
$product->save();
```

---

#### 🔹 4.6. Silme:

```php
Product::destroy(1);
// veya
$product = Product::find(1);
$product->delete();
```

---

### 🔗 5. İLİŞKİLER (RELATIONSHIPS)

Model'ler veritabanı ilişkilerini tanımlamak için de kullanılır.

#### 🔸 5.1. Bir kullanıcının birçok yazısı varsa:

```php
class User extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }
}
```

#### 🔸 5.2. Bir yazının bir kullanıcısı varsa:

```php
class Post extends Model {
    public function user() {
        return $this->belongsTo(User::class);
    }
}
```

---

### 🛠️ 6. MODEL'DE YARDIMCI METOTLAR

#### ➕ Accessors (erişim dönüştürücü)

```php
public function getNameUpperAttribute() {
    return strtoupper($this->name);
}

// Kullanımı:
$user->name_upper;
```

#### ✏️ Mutators (veri kaydetmeden önce dönüştürme)

```php
public function setNameAttribute($value) {
    $this->attributes['name'] = ucfirst($value);
}
```

---

### 🧪 7. MODEL’İ KULLANMA AKIŞI (CRUD)

| İşlem                   | Yol                                                         |
| ----------------------- | ----------------------------------------------------------- |
| Tablo oluşturma         | `php artisan make:model Product -m` + `php artisan migrate` |
| Model oluşturma         | `php artisan make:model Product`                            |
| Controller ile kullanma | Controller içinde `Product::all()` vb.                      |
| Form verisi kaydetme    | `$request->validate() + Product::create()`                  |
| API'de gösterme         | `return Product::all()`                                     |

---

### 🎯 8. MODEL = VERİNİN KALBİ

Eğer Laravel'i bir şirket gibi düşünürsek:

| Katman     | Rolü                                                        |
| ---------- | ----------------------------------------------------------- |
| Route      | Sekreter – "Kim ne istiyor?" sorusunu sorar                 |
| Controller | Müdür – "Şunu yap" der                                      |
| Model      | Çalışan – Gerçek işi yapar: veriyi getirir, işler, kaydeder |

---

### 🔚 SONUÇ: Model kullanmak sana şunları sağlar:

✅ SQL sorgusu yazmadan veritabanı işlemleri
✅ Kodun daha sade, düzenli ve sürdürülebilir olması
✅ İlişkilendirme sayesinde veri yapıları arasında bağ kurabilme
✅ Güvenlik (fillable, guarded, hidden gibi özelliklerle)
✅ Controller’ların sade kalması (separation of concerns)

---

Eğer istersen şu anda elindeki bir veritabanı tablosu için **model + migration + örnek controller** oluşturarak sana birebir uyarlanmış örnek de yazabilirim.

Hazırsan, tablonun yapısını (alan adları, veri türleri) yaz — senin için sıfırdan model sistemi kurayım.
