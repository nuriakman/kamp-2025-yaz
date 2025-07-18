# Gün 4 - Sabah Oturumu: Laravel 101 - REST API'ye Giriş

## 1. Laravel'e Giriş

### 1.1 Laravel Nedir?

- PHP tabanlı modern web uygulama çatısı
- MVC (Model-View-Controller) mimarisi
- Açık kaynak ve geniş topluluk desteği
- Zengin özellik seti (ORM, Kimlik Doğrulama, Routing, vb.)

### 1.2 Neden Laravel?

- Hızlı uygulama geliştirme (RAD)
- Güvenlik önlemleri
- Test edilebilirlik
- Zengin dokümantasyon
- Geniş paket ekosistemi

## 2. Laravel Kurulumu ve Proje Oluşturma

### 2.1 Gereksinimler

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (Frontend için)

### 2.2 Yeni Bir Laravel Projesi Oluşturma

```bash
# Yeni bir Laravel projesi oluşturma
composer create-project laravel/laravel e-ticaret-api

# Proje dizinine geçiş
cd e-ticaret-api

# Geliştirme sunucusunu başlatma
php artisan serve
```

### 2.3 Proje Yapısı

```
e-ticaret-api/
├── app/                 # Uygulama mantığı
│   ├── Http/           # Controller'lar ve Middleware'ler
│   ├── Models/         # Veritabanı modelleri
│   └── Providers/      # Service Provider'lar
├── config/             # Yapılandırma dosyaları
├── database/           # Migrations, seeders, factories
├── public/             # Giriş noktası ve asset'ler
├── resources/          # View'lar ve frontend kaynakları
├── routes/             # Rota tanımlamaları
└── tests/              # Test dosyaları
```

## 3. Veritabanı Yapılandırması

### 3.1 .env Dosyası Düzenleme

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_ticaret
DB_USERNAME=root
DB_PASSWORD=
```

### 3.2 Migration Oluşturma ve Çalıştırma

```bash
# Migration oluşturma
php artisan make:migration create_urunler_table --create=urunler
php artisan make:migration create_kategoriler_table --create=kategoriler
```

Örnek migration dosyası (`database/migrations/[timestamp]_create_urunler_table.php`):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('urunler', function (Blueprint $table) {
            $table->id();
            $table->string('urun_adi');
            $table->text('aciklama')->nullable();
            $table->decimal('birim_fiyat', 10, 2);
            $table->integer('stok_miktari')->default(0);
            $table->foreignId('kategori_id')->constrained('kategoriler');
            $table->decimal('indirimli_fiyat', 10, 2)->nullable();
            $table->boolean('satista_mi')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('urunler');
    }
};
```

Migration'ları çalıştırma:

```bash
php artisan migrate
```

## 4. Modeller ve Eloquent ORM

### 4.1 Model Oluşturma

```bash
php artisan make:model Urun
php artisan make:model Kategori
```

### 4.2 Model İlişkileri

`app/Models/Urun.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Urun extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'urun_adi',
        'aciklama',
        'birim_fiyat',
        'stok_miktari',
        'kategori_id',
        'indirimli_fiyat',
        'satista_mi'
    ];

    protected $casts = [
        'birim_fiyat' => 'decimal:2',
        'indirimli_fiyat' => 'decimal:2',
        'satista_mi' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function siparisDetaylari(): HasMany
    {
        return $this->hasMany(SiparisDetay::class);
    }

    public function yorumlar(): HasMany
    {
        return $this->hasMany(Yorum::class);
    }

    public function getIndirimOraniAttribute(): ?float
    {
        if (is_null($this->indirimli_fiyat) || $this->indirimli_fiyat >= $this->birim_fiyat) {
            return null;
        }

        return round((1 - ($this->indirimli_fiyat / $this->birim_fiyat)) * 100, 2);
    }
}
```

`app/Models/Kategori.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kategori extends Model
{
    protected $fillable = ['kategori_adi', 'aciklama', 'ust_kategori_id'];

    public function urunler(): HasMany
    {
        return $this->hasMany(Urun::class);
    }

    public function altKategoriler(): HasMany
    {
        return $this->hasMany(Kategori::class, 'ust_kategori_id');
    }

    public function ustKategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'ust_kategori_id');
    }
}
```

## 5. Controller ve Route Tanımlamaları

### 5.1 API Controller Oluşturma

```bash
php artisan make:controller API/UrunController --api
php artisan make:controller API/KategoriController --api
```

### 5.2 API Routes Tanımlama

`routes/api.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UrunController;
use App\Http\Controllers\API\KategoriController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    // Public Routes
    Route::get('urunler', [UrunController::class, 'index']);
    Route::get('urunler/{id}', [UrunController::class, 'show']);
    Route::get('kategoriler', [KategoriController::class, 'index']);

    // Protected Routes (Authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('urunler', UrunController::class)->except(['index', 'show']);
        Route::apiResource('kategoriler', KategoriController::class)->except(['index']);
    });
});
```

## 6. Ödev

1. Laravel kurulumunu yapın ve yeni bir proje oluşturun
2. Veritabanı yapılandırmasını yapın
3. Aşağıdaki tablolar için migration ve modelleri oluşturun:
   - urunler
   - kategoriler
   - musteriler
   - siparisler
   - siparis_detaylari
4. Model ilişkilerini tanımlayın
5. API rotalarını oluşturun

## 7. Yararlı Kaynaklar

- [Laravel Resmi Dokümantasyonu](https://laravel.com/docs)
- [Laravel Eloquent: Getting Started](https://laravel.com/docs/eloquent)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)

---

**Not:** Öğleden sonraki oturumda API Controller'larımızı yazacak ve test edeceğiz.
