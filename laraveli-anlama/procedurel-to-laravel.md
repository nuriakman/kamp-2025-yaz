# Procedural bir yapıdan Laravel’e geçiş rehberi

Laravel’e geçiş yapmak, prosedürelden nesne yönelimli ve MVC yapısına geçiş anlamına gelir. Bu geçişi **adım adım**, her aşamada **neden böyle yapıldığı** ile birlikte açıklayan detaylı bir rehber aşağıdadır.

---

## 🧭 BÖLÜM 1: MANTIKSAL GEÇİŞ – Prosedürel ile Laravel’in Farkı

### 🔸 1.1. Prosedürel Yaklaşım

Kodlar, genellikle tek bir dosyada veya fonksiyonlar halinde:

```php
<?php
$conn = mysqli_connect("localhost", "root", "", "db");

function getUsers() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM users");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
```

### 🔸 1.2. Laravel Yaklaşımı (MVC)

Kodlar 3 ana gruba ayrılır:

| Katman         | Görevi                             |
| -------------- | ---------------------------------- |
| **Model**      | Veritabanı ile çalışır             |
| **Controller** | Kullanıcıdan gelen istekleri işler |
| **Route**      | Adres ile işlemleri eşleştirir     |

---

## 🏁 BÖLÜM 2: KURULUM

### 🔹 2.1. Laravel Projesi Başlat

```bash
composer create-project laravel/laravel projeAdi
```

### 🔹 2.2. .env Dosyasını Yapılandır

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=veritabani_adi
DB_USERNAME=kullanici_adi
DB_PASSWORD=sifre
```

---

## 🏗️ BÖLÜM 3: VERİTABANI İŞLEMLERİ

### 🔸 3.1. Migration Oluştur

```bash
php artisan make:model User -m
```

Bu komut:

- `app/Models/User.php` model dosyasını
- `database/migrations/xxxx_create_users_table.php` migration dosyasını oluşturur.

### 🔸 3.2. Migration'a Kolon Ekle

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamps();
});
```

### 🔸 3.3. Migration’ı Çalıştır

```bash
php artisan migrate
```

---

## 🔃 BÖLÜM 4: ROUTE → CONTROLLER → MODEL AKIŞI

### 🔹 4.1. Route Oluştur (routes/api.php)

```php
Route::get('/users', [UserController::class, 'index']);
```

### 🔹 4.2. Controller Oluştur

```bash
php artisan make:controller UserController
```

**app/Http/Controllers/UserController.php**

```php
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }
}
```

### 🔹 4.3. Model Kullanımı

**app/Models/User.php**

```php
class User extends Model
{
    protected $fillable = ['name', 'email'];
}
```

---

## 🧪 BÖLÜM 5: VERİ DOĞRULAMA VE FORM GÖNDERME

### 🔹 5.1. POST Route

```php
Route::post('/users', [UserController::class, 'store']);
```

### 🔹 5.2. store() Metodu

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users'
    ]);

    $user = User::create($validated);

    return response()->json($user, 201);
}
```

---

## ⚙️ BÖLÜM 6: ESKİ FONKSİYONLARINI TAŞIMA STRATEJİSİ

### 🔸 6.1. Eski Fonksiyonları Gruplandır

Örnek: `getUsers()`, `addUser()` gibi işlemleri `UserController` içinde topla.

### 🔸 6.2. SQL Sorgularını Eloquent ile Yeniden Yaz

Eski:

```php
$sql = "SELECT * FROM users";
```

Yeni:

```php
$users = User::all();
```

---

## 🔍 BÖLÜM 7: TEST – Postman ile API Testi

| Endpoint     | Method | Görev                   |
| ------------ | ------ | ----------------------- |
| `/api/users` | GET    | Tüm kullanıcıları getir |
| `/api/users` | POST   | Yeni kullanıcı ekle     |

---

## 🧱 BÖLÜM 8: PROJELERİNE GÖRE İLERLEME

| Prosedürel Yapı        | Laravel Karşılığı             |
| ---------------------- | ----------------------------- |
| `db.php`               | `.env` + config/database.php  |
| `functions.php`        | Controller'lar                |
| `form_submit.php`      | API endpoint (`POST` Route)   |
| `index.php?islem=ekle` | RESTful route (`Route::post`) |

---

## 🧠 BÖLÜM 9: KAFANI KARIŞTIRAN KONULARI SOR

Laravel’de zorlandığın her fonksiyonun karşılığını birlikte yazabiliriz.

Örneğin:

- Formdan gelen veriyi kaydetme
- Kullanıcı girişi (auth)
- Dosya yükleme
- Eski bir işlem fonksiyonunu Laravel'e aktarma

---

## 🎓 SONUÇ: BU GEÇİŞİ BAŞARMAK İÇİN

- Başta Laravel’i **sadece API** olarak kullan: View’larla uğraşma.
- Birkaç CRUD API yaz: Kullanıcı, ürün, sipariş gibi.
- Her yeni şey öğrendikçe eski procedural fonksiyonlarını dönüştür.
- Gerekirse birlikte küçük bir proje sıfırdan başlatabiliriz.

---
