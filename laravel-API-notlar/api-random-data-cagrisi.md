# API çağrısı için örnek veri toplama (Python ile random data)

Bu kod, belirli bir kaynaktan üretilen verilerin nasıl saklanacağına örnek olması amacıyla yazılmıştır.

Bu örnek, sensörler, üçüncü parti servisler, IoT cihazları veya toplu veri üreticilerinden gelen olay/veri akışlarının REST API üzerinden alınması, şema/doğrulama kurallarına göre kontrol edilmesi ve veritabanına güvenli bir şekilde yazılması sürecini küçük ama gerçekçi bir senaryo ile gösterir. Odak noktaları; istek hacmi yönetimi, validasyon, indeks kullanımı, hata ve yeniden deneme mantıkları ile geliştirme/test ortamlarında gerçekçi veri akışını simüle etmektir.

**Kullanım Senaryoları:**

- Veri toplama testi: Yerel/staging ortamda doğrulama kuralları ve hata yönetimini sınamak
- Simülasyon: Gerçek kaynak hazır değilken backend uçlarını beslemek
- Demo/PoC: Ekip veya müşteri için uçtan uca akışı göstermek
- CI/CD tohum verisi: Pipeline aşamalarında örnek kayıt üretmek
- Geliştirme: Model/migration değişikliklerini gerçekçi verilerle doğrulamak
- Hata senaryoları: Geçersiz payload, rate limit ve ağ hatalarını gözlemlemek
- Performans ölçümü: `lokasyonID` ve `tarihsaat` indekslerinin sorgu performansına etkisini ölçmek

**Kodun Özellikleri:**

Random veri üreten python kod dosyası: [./api-random-data-cagrisi-json.py](api-random-data-cagrisi-json.py)

- 100 POST isteği yapar
- `lokasyonID`: 1-5 arası rastgele
- `deger1`: 10-30 arası rastgele
- `deger2`: 31-70 arası rastgele
- `deger3`: 71-99 arası rastgele
- `tarihsaat`: Son 30 gün içinde rastgele zaman
- İstekler arası 0.1 saniye bekleme
- Hata durumlarını konsola yazar
- Endpoint: <http://localhost/api/veriler>

**Çalıştırmak için:**

```bash
pip install requests
python api-random-data-cagrisi-json.py
```

**Not:** Sunucunun yoğunluğuna göre `time.sleep()` değerini ayarlayabilirsiniz.

## Laravel Tarafı Kodları

**1. Route Tanımı (`routes/api.php`):**

```php
use App\Http\Controllers\VeriController;

// POST ile JOSN verilerini almak için
Route::post('/veriler', [VeriController::class, 'veriKaydet']);

```

**2. Controller Tanımı (`app/Http/Controllers/VeriController.php`):**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Veri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VeriController extends Controller
{
    public function veriKaydet(Request $request)
    {
        // Validasyon
        $validator = Validator::make($request->all(), [
            'lokasyonID' => 'required|integer|between:1,5',
            'deger1' => 'required|integer|between:10,30',
            'deger2' => 'required|integer|between:31,70',
            'deger3' => 'required|integer|between:71,99',
            'tarihsaat' => 'required|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Veriyi kaydet
        $veri = Veri::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla kaydedildi',
            'data' => $veri
        ], 201);
    }
}
```

**3. Model Tanımı (`app/Models/Veri.php`):**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veri extends Model
{
    use HasFactory;

    protected $table = 'veriler';

    protected $fillable = [
        'lokasyonID',
        'deger1',
        'deger2',
        'deger3',
        'tarihsaat'
    ];

    protected $casts = [
        'tarihsaat' => 'datetime:Y-m-d H:i:s'
    ];
}
```

**4. Migration Tanımı (`database/migrations/2024_01_01_000000_create_veriler_table.php`):**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('veriler', function (Blueprint $table) {
            $table->id();
            $table->integer('lokasyonID');
            $table->integer('deger1');
            $table->integer('deger2');
            $table->integer('deger3');
            $table->datetime('tarihsaat');
            $table->timestamps();

            // İsteğe bağlı indexler
            $table->index('lokasyonID');
            $table->index('tarihsaat');
        });
    }

    public function down()
    {
        Schema::dropIfExists('veriler');
    }
};
```

**Kurulum Adımları:**

1. Migration'ı çalıştır:

```bash
php artisan migrate
```

2. Test etmek için:

```bash
curl -X POST http://localhost/api/veriler \
  -H "Content-Type: application/json" \
  -d '{
    "lokasyonID": 1,
    "deger1": 15,
    "deger2": 50,
    "deger3": 80,
    "tarihsaat": "2025-03-01 10:00:00"
  }'
```

**Özellikler:**

- JSON veri alır ve validasyon yapar
- Otomatik created_at/updated_at ekler
- Hata durumunda detaylı response döner
- LokasyonID ve tarihsaat için index kullanır
