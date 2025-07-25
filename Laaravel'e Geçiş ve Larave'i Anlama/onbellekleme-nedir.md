# Önbellekleme Nedir?

Laravel’de **önbellekleme (caching)** konusu, performans artırmak, veritabanı sorgularını azaltmak ve genel olarak uygulamanın hızlı çalışmasını sağlamak için çok önemli. Sana hem kavramsal hem de pratik açıdan, sade bir anlatımla özetleyeyim.

---

## 🧠 Önbellekleme Nedir?

Önbellekleme, uygulamanın daha önce yaptığı bir işlemin sonucunu (örneğin, sorgu sonucu, sayfa, ayar dosyası vb.) geçici olarak **dış bir hafızada (cache)** saklamasıdır.
Böylece aynı veri tekrar gerektiğinde, doğrudan bu hızlı hafızadan alınır, işlem tekrarlanmaz.

---

## 🛠️ Laravel’de Önbellekleme Sistemi

Laravel, önbellekleme için birden çok **driver** destekler:

| Driver      | Ne İçin Kullanılır?                   |
| ----------- | ------------------------------------- |
| `file`      | Dosya tabanlı cache. (default)        |
| `database`  | Veritabanı tablosunda cache tutulur.  |
| `redis`     | Çok hızlı, bellek tabanlı cache.      |
| `memcached` | Dağıtık önbellekleme için kullanılır. |
| `array`     | Geçici, sadece istek boyunca saklar.  |

---

## 📦 Cache Kullanımı – Temel Örnekler

### 1. Değer Kaydetmek

```php
Cache::put('anahtar', 'değer', $saniye);
```

Örnek:

```php
Cache::put('site_ayar', ['tema' => 'koyu'], 3600); // 1 saat sakla
```

### 2. Değeri Almak

```php
$value = Cache::get('anahtar', 'varsayılan');
```

### 3. Değer Yoksa Üretip Saklamak (remember)

```php
$value = Cache::remember('products', 600, function() {
    return Product::all();
});
```

- Eğer `products` cache’de varsa onu döner.
- Yoksa sorguyu yapar, cache’e kaydeder ve döner.

### 4. Cache Silmek

```php
Cache::forget('anahtar');
```

---

## ⏰ Örnek Senaryo: Ürün Listesini Cache’lemek

```php
public function index()
{
    $products = Cache::remember('products.all', 300, function() {
        return Product::all();
    });

    return view('products.index', compact('products'));
}
```

> 300 saniye boyunca ürün listesi veritabanından çekilmez, cache’den gelir.

---

## 🔧 Cache Süresi (Expiration)

- Süre **saniye** cinsindendir.
- Süre dolunca cache otomatik silinir.
- Süresiz cache için `forever()` kullanılır.

```php
Cache::forever('ayarlar', $data);
```

---

## 🚦 Cache’in Avantajları

- Veritabanı yükünü azaltır
- Performansı artırır
- API cevaplarını hızlandırır
- Yoğun istek altında uygulama stabil kalır

---

## ⚠️ Dikkat Edilmesi Gerekenler

- Cache süresi çok uzun olursa, eski veri gösterilir.
- Dinamik verilerde cache invalidation (geçersiz kılma) önemlidir.
- Cache kullanımı arttıkça, yönetimi karmaşıklaşabilir.

---

## 🧰 Laravel Cache Facade Alternatifleri

- `Cache::tags()` — Özel tag ile cache gruplamak (Redis, Memcached destekler)
- `cache()` helper fonksiyonu — `Cache` facade yerine kısaca kullanılır.

Örnek:

```php
cache(['key' => 'value'], 3600);
$value = cache('key');
```

---

## ⚙️ Cache Ayarları Nerede?

`config/cache.php` dosyasında önbellekleme driver’ı ve ayarları bulunur.
Örneğin, `default` driver’ı `file` olarak ayarlanmıştır.

---

## 📌 Özet

| Soru                                | Cevap                                                               |
| ----------------------------------- | ------------------------------------------------------------------- |
| Önbellekleme ne işe yarar?          | Sık kullanılan verileri geçici olarak saklar, hızlı erişim sağlar.  |
| Laravel’de cache driver’ları neler? | file, database, redis, memcached, array                             |
| Cache nasıl kullanılır?             | `Cache::put()`, `Cache::get()`, `Cache::remember()` gibi metodlarla |
| Cache süresi nasıl ayarlanır?       | Saniye cinsinden parametre vererek                                  |
| Cache dezavantajları neler?         | Veri eski kalabilir, invalidation zor olabilir                      |

---

## Redis İle Cache Kullanımı

Laravel’de **Redis ile önbellekleme** konusunu detaylıca anlatayım. Redis, Laravel'de en hızlı ve en çok tercih edilen cache driver'larından biridir.

---

### 🧠 Redis Nedir?

**Redis**, verileri RAM üzerinde tutan, çok hızlı bir **anahtar-değer veritabanı**dır.
Laravel, Redis’i hem **önbellekleme (cache)** hem de **kuvvetli kuyruk işlemleri (queue)** için kullanabilir.

> Avantajı: Yüksek hız + kompleks veri yapılarını desteklemesi (list, hash, set, vs.)

---

### 🔧 Laravel’de Redis Kurulumu

#### 1. Redis Sunucusunu Kur (Geliştirme ortamı için)

##### Mac (Homebrew):

```bash
brew install redis
brew services start redis
```

##### Ubuntu:

```bash
sudo apt update
sudo apt install redis
sudo systemctl enable redis
sudo systemctl start redis
```

---

#### 2. Laravel Projesine Gerekli Paket

Laravel >= 8 ve PHP >= 8 için tavsiye edilen istemci:

```bash
composer require predis/predis
```

Alternatif olarak Laravel, `phpredis` uzantısını da destekler. Ama `predis` daha kolaydır.

---

#### 3. `.env` Dosyasını Ayarla

```dotenv
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=predis
```

> Cache, Queue ve Session’ları Redis’e bağlayabiliriz.

---

### 📦 Redis Cache Kullanımı Laravel'de

```php
Cache::put('kullanici_adi', 'nuri', 600); // 10 dk sakla

$kullanici = Cache::get('kullanici_adi'); // redis’ten oku

Cache::forget('kullanici_adi'); // redis’ten sil
```

Arka planda tüm bu işlemler Redis üzerinden yapılır.

---

### 🏷️ Redis Cache Tag (Etiketli Önbellekleme)

**Sadece Redis ve Memcached** destekler!

```php
Cache::tags(['kategoriler'])->put('elektronik', $veri, 600);

Cache::tags(['kategoriler'])->get('elektronik');
```

Tüm ‘kategoriler’ etiketiyle işaretlenmiş verileri aynı anda silebilirsin:

```php
Cache::tags(['kategoriler'])->flush();
```

Bu, örneğin bir ürün silindiğinde tüm kategori cache’ini temizlemek için çok kullanışlıdır.

---

### 📁 Redis Yapılandırması (config/database.php)

```php
'redis' => [

    'client' => env('REDIS_CLIENT', 'predis'),

    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
],
```

---

### 🧪 Redis Bağlantısını Test Etmek

Tinker ile deneyebilirsin:

```bash
php artisan tinker
>>> Cache::put('test', 'redis ile cache', 60)
>>> Cache::get('test')
```

---

### 🧼 Cache’i Temizleme

Tüm önbelleği silmek:

```bash
php artisan cache:clear
```

Redis önbelleğini silmek (daha kapsamlı):

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Redis shell üzerinden:

```bash
redis-cli
> FLUSHALL
```

---

### 📊 Redis’i Ne Zaman Kullanmalı?

| Senaryo                     | Redis Avantajı                               |
| --------------------------- | -------------------------------------------- |
| API isteklerini hızlandırma | Saniyede binlerce istek karşılanabilir       |
| Dashboard verisi            | Sık sorgulanan özet veriler cache’lenebilir  |
| Etiketli veri silme         | `Cache::tags()` ile gruplama yapılabilir     |
| Kuyruk (Queue)              | Laravel queue sisteminin en hızlı alt yapısı |
| Oturum yönetimi (Session)   | Büyük ölçekli kullanıcı oturumlarında etkili |

---

### 🚀 Bonus: Laravel Horizon (Redis + Queue Yönetimi)

Laravel, Redis tabanlı kuyruk sistemini yönetmek için **Horizon** adlı bir arayüz sunar.
Büyük projelerde Redis + Horizon kombinasyonu çok kullanışlıdır.

---

### 📌 Özet

| Konu                 | Açıklama                                       |
| -------------------- | ---------------------------------------------- |
| Redis Nedir?         | RAM üzerinde çalışan çok hızlı bir veri deposu |
| Laravel’de Kullanımı | `CACHE_DRIVER=redis` ve `predis` paketi ile    |
| Avantajı             | Yüksek performans, tag desteği, kuyruk uyumu   |
| Etiketleme           | Sadece Redis ve Memcached destekler            |
| Temizleme            | `cache:clear` veya `redis-cli FLUSHALL`        |

---

## Cache Nerede Kullanılır? Model? Controller?

Güzel bir soru! Laravel’de **cache (önbellek)**, model içinde de kullanılabilir ama bu **zorunlu değildir**. Nerede kullanacağını seçmek, uygulamanın yapısına ve önbellekleme stratejine göre değişir.

Aşağıda bu konuyu **netleştirecek şekilde adım adım** açıklıyorum:

---

## ✅ 1. Cache Nerede Kullanılır?

| Kullanım Yeri                  | Açıklama                                                                                   |
| ------------------------------ | ------------------------------------------------------------------------------------------ |
| **Controller**                 | Genellikle veri getirme işlemlerini önbelleklemek için kullanılır. En yaygın yerdir.       |
| **Model**                      | Bazı model metodlarının sonucu cache'e alınabilir (örneğin filtreli sorgular).             |
| **Repository/Service Katmanı** | Daha büyük sistemlerde cache bu katmanda merkezi olarak kullanılır.                        |
| **View (Blade)**               | `@cache` gibi doğrudan Blade içinde cache kullanan paketlerle olabilir (nadiren önerilir). |

---

## 🧠 Model İçinde Cache Kullanımı Örneği

```php
class Sehir extends Model
{
    public static function getCachedSehirler()
    {
        return Cache::rememberForever('sehirler', function () {
            return self::orderBy('ad')->get();
        });
    }
}
```

Kullanım:

```php
$sehirler = Sehir::getCachedSehirler();
```

> Artık her seferinde veritabanına gitmek yerine `sehirler` Redis veya disk cache’ten çekilir.

---

## ❗ Ne Zaman Model İçinde Kullanmalısın?

### Kullan:

- Model’e ait **statik veri** varsa (örneğin şehirler, kategoriler, sabit listeler)
- Kod tekrarını önlemek istiyorsan
- Reusable (yeniden kullanılabilir) hale getirmek istiyorsan

### Kullanma:

- Her model metoduna ayrı ayrı cache eklersen **cache yönetimi dağınıklaşır**
- Proje büyüdükçe kod karışabilir, bu durumda cache mantığını **Service** veya **Repository** katmanına almak daha iyidir

---

## 📍 Controller İçinde Cache Kullanımı (Alternatif)

```php
public function index()
{
    $sehirler = Cache::remember('sehirler', now()->addHours(12), function () {
        return Sehir::all();
    });

    return response()->json($sehirler);
}
```

> Bu yöntemle cache sadece bu route için geçerli olur.

---

## 🎯 Özetle:

| Soru                                  | Cevap                                                                 |
| ------------------------------------- | --------------------------------------------------------------------- |
| Cache model içinde kullanılabilir mi? | Evet, kullanılabilir. Özellikle sabit ve sık kullanılan veriler için. |
| Kullanmak zorunlu mu?                 | Hayır. Controller veya Service katmanında da kullanabilirsin.         |
| Ne zaman model içinde kullanılır?     | Sabit verilerde, tekrar eden sorgularda ve temiz kod yazmak için.     |

---

## Önbellekleme İçin Stratejiler

Harika bir soru!
Eğer bir tablo **düşük sıklıkta veri ekleniyor** ama **yüksek sıklıkta okunuyorsa**, bu tabloyu **önbelleğe almak (cache)**, performans açısından çok faydalıdır.

Aşağıda bu senaryo için Laravel'de **önerilen kullanım şeklini** açıklıyorum 👇

---

### 📌 Örnek Durum:

> Diyelim ki `iller` tablon var.
>
> - 81 il var, yıllarca sabit kalabilir.
> - API’de sürekli bu listeye ihtiyaç duyuluyor (select kutuları vs.).
> - Ama yılda belki 1 defa yeni bir kayıt ekleniyor (örneğin özel statü bir bölge vs.)

Bu durumda:

---

### ✅ Uygulanabilir Cache Stratejisi:

#### 1. **Cache'e alma (remember)**

Model içinde static bir metodla tüm listeyi cache'e al:

```php
// app/Models/Il.php
use Illuminate\Support\Facades\Cache;

class Il extends Model
{
    public static function getCached()
    {
        return Cache::rememberForever('iller', function () {
            return self::orderBy('ad')->get();
        });
    }
}
```

#### 2. **Controller'da kullan**

```php
public function iller()
{
    return response()->json(Il::getCached());
}
```

#### 3. **Yeni kayıt eklenince cache'i temizle (invalidate)**

```php
public function store(Request $request)
{
    Il::create($request->only('ad'));

    Cache::forget('iller'); // Cache’i temizle

    return response()->json(['message' => 'Yeni il eklendi ve cache sıfırlandı']);
}
```

> Böylece:
> ✅ Okuma işlemleri **cache’ten** yapılır (çok hızlı)
> ✅ Yeni veri eklendiğinde cache **manuel olarak temizlenir**
> ✅ Bir sonraki istekte cache yeniden oluşturulur

---

### 🧠 Alternatif: Event Tabanlı Temizleme

Dilersen `Il` modeline bir observer tanımlayarak, veri eklendiğinde otomatik olarak cache'i temizletebilirsin:

```php
// App\Observers\IlObserver.php
class IlObserver
{
    public function created(Il $il)
    {
        Cache::forget('iller');
    }
}
```

Ve `AppServiceProvider` içinde:

```php
use App\Models\Il;
use App\Observers\IlObserver;

public function boot()
{
    Il::observe(IlObserver::class);
}
```

---

### 🎯 Özetle:

| Durum                                 | Öneri                                                     |
| ------------------------------------- | --------------------------------------------------------- |
| Tablo az değişiyor ama sık okunuyorsa | `rememberForever()` ile cache’e al                        |
| Veri eklendikçe cache bozulmasın      | Ekleme sonrası `Cache::forget()` ile sıfırla              |
| Bu işi otomatikleştirmek istersen     | `Observer` ile model olaylarına bağlı cache sıfırlama yap |

---
