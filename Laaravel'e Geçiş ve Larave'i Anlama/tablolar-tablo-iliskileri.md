# Tablolar ve Tablo İlişkileri

Laravel'de **tablolar ve ilişkiler**, Eloquent ORM sayesinde oldukça anlaşılır ve güçlü şekilde yönetilir. Aşağıda **sıfırdan ve adım adım** açıklamalı bir rehber bulacaksın:

---

## 🧱 1. Tablolar = Modeller

Veritabanındaki **her tablo**, Laravel’de bir **Model** sınıfıyla temsil edilir.

Örnek: `users` tablosu → `User` modeli

```bash
php artisan make:model User
```

Otomatik olarak `app/Models/User.php` dosyası oluşur.

---

## 🔗 2. Tablolar Arasındaki İlişki Türleri

### 📌 a. 1-1 (One To One)

> Kullanıcı'nın bir profil bilgisi var.

```php
// User.php
public function profile()
{
    return $this->hasOne(Profile::class);
}

// Profile.php
public function user()
{
    return $this->belongsTo(User::class);
}
```

Kullanımı:

```php
$user = User::find(1);
echo $user->profile->phone_number;
```

---

### 📌 b. 1-N (One To Many)

> Kategori’nin birçok ürünü var.

```php
// Category.php
public function products()
{
    return $this->hasMany(Product::class);
}

// Product.php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

Kullanımı:

```php
$category = Category::find(1);
foreach ($category->products as $product) {
    echo $product->name;
}
```

---

### 📌 c. N-N (Many To Many)

> Bir öğrenci birden fazla derse, bir ders de birden fazla öğrenciye kayıtlı olabilir.

Pivot tablo gerekli: `course_student`

```php
// Student.php
public function courses()
{
    return $this->belongsToMany(Course::class);
}

// Course.php
public function students()
{
    return $this->belongsToMany(Student::class);
}
```

Kullanımı:

```php
$student->courses()->attach($courseId);    // Ekle
$student->courses()->detach($courseId);    // Sil
$student->courses()->sync([1, 2, 3]);       // Güncelle
```

---

### 📌 d. Has Many Through (İlişkili veriye dolaylı erişim)

> Ülkenin kullanıcıları var, kullanıcıların siparişleri var → Ülkenin siparişleri nedir?

```php
// Country.php
public function orders()
{
    return $this->hasManyThrough(Order::class, User::class);
}
```

---

## 🧰 3. Migration ile İlişkiyi Kurmak

Örnek: `products` tablosunda `category_id` varsa:

```php
// migration dosyası içinde:
$table->foreignId('category_id')->constrained()->onDelete('cascade');
```

Bu satır:

- `category_id` sütununu oluşturur.
- `categories` tablosuna `foreign key` bağlar.
- Ana kategori silinirse ürünleri de siler.

---

## 🧪 4. Laravel Tinker ile Test Et

Terminalde `php artisan tinker` yazarak test edebilirsin:

```bash
>>> $c = Category::find(1);
>>> $c->products;  // ilişkili ürünleri getirir
```

---

## 🔎 5. İlişkiyle Veri Getirme: Eager Loading

```php
// Tek seferde category ve ürünlerini getir:
$categories = Category::with('products')->get();
```

Yoksa her ürün için ayrı sorgu çalışır: ❌ N+1 problemi oluşur.

---

## 💡 6. İlişki İçinde Filtreleme

```php
$users = User::whereHas('orders', function ($query) {
    $query->where('total', '>', 1000);
})->get();
```

---

## 🧪 7. Pivot Tablosuna Ek Kolon

Eğer **many-to-many** ilişki pivot tablosuna ek bilgi gerekiyorsa (`course_student` içinde `grade`, `date` vs.):

```php
$student->courses()->attach($courseId, ['grade' => 85]);
```

Model tanımında:

```php
public function courses()
{
    return $this->belongsToMany(Course::class)->withPivot('grade');
}
```

---

## ✅ 8. Özet Tablo

| İlişki Türü    | Laravel Fonksiyonu     | Pivot Gerekir mi |
| -------------- | ---------------------- | ---------------- |
| 1-1            | `hasOne`, `belongsTo`  | ❌               |
| 1-N            | `hasMany`, `belongsTo` | ❌               |
| N-N            | `belongsToMany`        | ✅               |
| HasManyThrough | `hasManyThrough`       | ❌               |

---

## ✅ Laravel'deki Tablolar Arası İlişki Türleri — Özet ve Açıklama

| İlişki Türü                    | Ne Anlama Gelir?                                                                                                             | Laravel'deki Fonksiyonlar                                       | Ekstra Tablo Gerekir mi?                 |
| ------------------------------ | ---------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------- | ---------------------------------------- |
| **1'e 1 (One to One)**         | Bir kullanıcının **sadece bir profili** olur gibi                                                                            | `hasOne` (sahip olan taraf) <br>`belongsTo` (bağlı olan taraf)  | ❌ Hayır                                 |
| **1'e Çok (One to Many)**      | Bir kategoride **birden fazla ürün** olabilir gibi                                                                           | `hasMany` (sahip olan taraf) <br>`belongsTo` (bağlı olan taraf) | ❌ Hayır                                 |
| **Çoktan Çoğa (Many to Many)** | Bir öğrenci **birden fazla derse**, bir ders de **birden fazla öğrenciye** ait olabilir gibi                                 | `belongsToMany` (her iki tarafta da kullanılır)                 | ✅ Evet, araya bir **ara tablo** gerekir |
| **Dolaylı (Has Many Through)** | Bir ülkenin **doğrudan kullanıcıları**, kullanıcıların da siparişleri varsa, ülkenin tüm siparişleri dolaylı olarak ulaşılır | `hasManyThrough`                                                | ❌ Hayır                                 |

---

### 🔎 Hızlı Örneklerle Açıklayalım:

| Durum                                                             | Laravel'de Ne Kullanılır?                 |
| ----------------------------------------------------------------- | ----------------------------------------- |
| Kullanıcının bir profili var                                      | `User::hasOne(Profile)`                   |
| Profil kime ait?                                                  | `Profile::belongsTo(User)`                |
| Kategori birçok ürüne sahip                                       | `Category::hasMany(Product)`              |
| Ürün hangi kategoriye ait?                                        | `Product::belongsTo(Category)`            |
| Öğrenci birçok dersi alabilir, ders birçok öğrenciye ait olabilir | `Student::belongsToMany(Course)` ve tersi |
| Ülkenin tüm siparişlerini kullanıcılar üzerinden görmek           | `Country::hasManyThrough(Order, User)`    |

---

### ❓“Ekstra tablo gerekir mi?” ne demek?

Laravel’de bazı ilişkiler için **iki tablo arasında bir “köprü tablo”** gerekir. Bu sadece **many-to-many** ilişkilerde olur.

**Örneğin:**

- `students` tablosu
- `courses` tablosu
- Bu ikisi arasına `course_student` diye bir tablo kurarsın.

---

Dilersen her ilişki türü için birebir migration ve model örneği de hazırlayabilirim.
İstersen baştan senin tablolarına özel anlatayım. Yardımcı olayım mı?

## 🔹 `belongsTo()` Ne Zaman Kullanılır?

Bir model başka bir modele **aitse**, yani bir **“bağlılık” varsa**, `belongsTo()` kullanılır.

> 👉 A modeli B modeline aitse, A modelinde `belongsTo(B::class)` yazılır.

---

### 📌 Günlük Hayattan Örnek

- Bir **ürün**, bir **kategoriye** aittir.
- Bir **çalışan**, bir **firmada** çalışır.
- Bir **öğrenci**, bir **okula** aittir.

> ✅ **Küçük olan büyük olana `belongsTo()` ile bağlanır.**
> Yani **ürün**, **kategoriye** `belongsTo()` ile bağlanır.

---

## ✅ Laravel Örneği

### Senaryo: Ürün ve Kategori

**Tablolar:**

- `products` (ürünler)
- `categories` (kategoriler)

Her ürün **bir kategoriye ait** olacak şekilde tasarlanmış.

---

### 🟦 Migration - `products` tablosu:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('category_id')->constrained(); // Bu satır çok önemli!
    $table->timestamps();
});
```

> Bu satır demek: “Her ürün, bir `category_id` ile bir kategoriye bağlı olacak.”

---

### 🟨 Model - `Product.php`:

```php
class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### 🟩 Model - `Category.php`:

```php
class Category extends Model
{
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

---

### 🧠 Hafızaya Kazımak İçin:

| Model        | İlişki              | Laravel fonksiyonu           |
| ------------ | ------------------- | ---------------------------- |
| **Ürün**     | Kategoriye aittir   | `belongsTo(Category::class)` |
| **Kategori** | Birçok ürünü vardır | `hasMany(Product::class)`    |

---

## 🔍 Laravel’de Nasıl Kullanılır?

```php
$product = Product::find(1);
echo $product->category->name;  // Ürünün ait olduğu kategorinin adı
```

```php
$category = Category::find(1);
foreach ($category->products as $product) {
    echo $product->name;
}
```

---

## 📌 Özet

- `belongsTo()` → Bu model, başka bir modele **aittir**.
- Genelde bu modelde bir **foreign key** vardır (`category_id`, `user_id`, vs.).
- **Ters ilişki** olarak düşün: `hasMany` veya `hasOne`'ın tersidir.

---

## 🔹 1. `hasOne()` — Birebir (One to One)

> "Bir kullanıcının bir profili olur."

### 📦 Gerçek Hayat Örneği

- Her kullanıcıya özel bir profil vardır.
- Ama her profil **yalnızca bir kullanıcıya** bağlıdır.

### 🛠 Migration Örneği:

**`profiles` tablosu:**

```php
Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->string('bio');
    $table->foreignId('user_id')->constrained();
});
```

### 🧱 Modeller:

**User.php**

```php
public function profile()
{
    return $this->hasOne(Profile::class);
}
```

**Profile.php**

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

### 🔍 Kullanım:

```php
$user = User::find(1);
echo $user->profile->bio; // Kullanıcının biyografisi
```

---

## 🔹 2. `hasMany()` — Bire Çok (One to Many)

> "Bir kategoride birçok ürün olabilir."

### 📦 Gerçek Hayat Örneği

- Bir kategoriye ait birçok ürün vardır.
- Ama her ürün **tek bir kategoriye** bağlıdır.

### 🛠 Migration:

**`products` tablosu:**

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('category_id')->constrained();
});
```

### 🧱 Modeller:

**Category.php**

```php
public function products()
{
    return $this->hasMany(Product::class);
}
```

**Product.php**

```php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

### 🔍 Kullanım:

```php
$category = Category::find(1);
foreach ($category->products as $product) {
    echo $product->name;
}
```

---

## 🔹 3. `belongsToMany()` — Çoktan Çoğa (Many to Many)

> "Bir öğrenci birçok derse, bir ders birçok öğrenciye bağlıdır."

### 📦 Gerçek Hayat Örneği

- Bir kullanıcı birçok rol alabilir.
- Bir rol birçok kullanıcıya atanabilir.

### 🛠 Ara Tablo (Pivot Tablo):

**`role_user` migration:**

```php
Schema::create('role_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained();
    $table->foreignId('role_id')->constrained();
});
```

### 🧱 Modeller:

**User.php**

```php
public function roles()
{
    return $this->belongsToMany(Role::class);
}
```

**Role.php**

```php
public function users()
{
    return $this->belongsToMany(User::class);
}
```

### 🔍 Kullanım:

```php
$user = User::find(1);
foreach ($user->roles as $role) {
    echo $role->name;
}
```

> Laravel pivot tablodaki ek verileri de kullanabilir (`withPivot()` ile).

---

## 🔹 4. `hasManyThrough()` — Dolaylı Bire Çok

> "Bir ülkenin kullanıcıları var, kullanıcıların siparişleri var. Ülkenin siparişleri dolaylı yoldan görülebilir."

### 📦 Gerçek Hayat Örneği

- Bir ülke birçok kullanıcıya sahiptir.
- O kullanıcılar sipariş verir.
- Ülke → Kullanıcı → Sipariş şeklinde dolaylı erişim vardır.

### 🧱 Modeller:

**Country.php**

```php
public function orders()
{
    return $this->hasManyThrough(Order::class, User::class);
}
```

> `Order` modeli üzerinden `User` modeli aracılığıyla siparişlere ulaşırız.

### 🔍 Kullanım:

```php
$country = Country::find(1);
foreach ($country->orders as $order) {
    echo $order->total;
}
```

---

## 🧠 Hafızaya Kazımak İçin Kısa Notlar:

| Laravel Fonksiyonu | Ne zaman kullanılır?                          | Anahtar cümle                           |
| ------------------ | --------------------------------------------- | --------------------------------------- |
| `belongsTo`        | Yabancı anahtarı (foreign key) içeren modelde | "Bu model bir başkasına ait"            |
| `hasOne`           | Bire bir ilişki (tek kayıtla ilişkili)        | "Bir şeyi var"                          |
| `hasMany`          | Çok kayıtla ilişkili                          | "Birden fazla şeyi var"                 |
| `belongsToMany`    | İki taraf da çok kayıtla ilişkili             | "Karşılıklı çokluk ilişkisi"            |
| `hasManyThrough`   | Dolaylı çok kayıt                             | "Aradaki model üzerinden diğerine ulaş" |

---
