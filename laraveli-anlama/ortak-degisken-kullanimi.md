# Ortak Değişkenlerin Kullanımı

Laravel projende **sistem genelinde kullanılacak sabit değişkenler** (örneğin uygulama ayarları, statik metinler, sabitler, enum değerleri gibi) için birkaç farklı ve etkili yöntem var. İşte en yaygın ve pratik çözümler:

---

## 1. .env Dosyası (Çevresel Değişkenler)

- **Küçük, basit ve değişebilir değerler** için ideal (API anahtarları, servis URL’leri, debug flagleri gibi).
- `env('SABIT_DEGISKEN')` ile erişilir.
- **NOT:** `.env` dosyası sadece config dosyaları veya servislerde kullanılır, uygulama içinde sık sık çağırmak performans sorunlarına neden olabilir.

---

## 2. Config Dosyaları (`config/`)

- Laravel config dosyaları, `config/app.php` gibi, **sabit ayarlar için en doğru yer**dir.
- `config/sabitler.php` gibi yeni bir dosya oluşturup, tüm sabitleri buraya koyabilirsin:

```php
// config/sabitler.php
return [
    'statuslar' => [
        'aktif' => 1,
        'pasif' => 0,
    ],
    'roller' => [
        'admin' => 'admin',
        'kullanici' => 'user',
    ],
    'sayilar' => [
        'max_urun' => 100,
    ],
];
```

Kullanımı:

```php
config('sabitler.statuslar.aktif'); // 1
```

**Avantajları:**

- Performans için önbelleğe alınır (`php artisan config:cache`).
- Ortamdan bağımsız sabitler burada saklanır.
- Kolay yönetim ve merkezi erişim.

---

## 3. Sabit Sınıflar / Enum Sınıfları

- PHP 8.1+ ile gelen **native enum** veya klasik `class` sabitleri.

Örnek enum:

```php
enum UserRole: string {
    case Admin = 'admin';
    case User = 'user';
}
```

Kullanımı:

```php
UserRole::Admin->value; // 'admin'
```

Alternatif klasik sınıf:

```php
class Sabitler {
    const STATUS_AKTIF = 1;
    const ROL_ADMIN = 'admin';
}
```

---

## 4. Veritabanı Tabanlı Ayarlar (Settings Table)

- Eğer **admin panelden ayar değiştirme** istersen, `settings` gibi bir tablo açıp dinamik yönetim yapabilirsin.
- Örneğin: `key`, `value` kolonları.
- Ayarları cache’te tutup performans optimize edebilirsin.

---

## 5. Helper Fonksiyonlar ile Kolay Erişim

`app/helpers.php` dosyası oluşturup fonksiyon yazabilirsin:

```php
function sabit($key) {
    $sabitler = config('sabitler');
    return data_get($sabitler, $key);
}
```

Kullanımı:

```php
sabit('statuslar.aktif');
```

---

## Özet Tablo

| Yöntem              | Kullanım Alanı                  | Avantajları                   | Dezavantajları                       |
| ------------------- | ------------------------------- | ----------------------------- | ------------------------------------ |
| `.env`              | Ortam bazlı, gizli değişkenler  | Güvenli, ortam bağımlı        | Performans için ideal değil          |
| `config/` dosyaları | Sabit, global ayarlar           | Performanslı, merkezi yönetim | Dinamik değil                        |
| PHP Sabitler / Enum | Kod tabanlı sabit tanımlar      | Tip güvenli, IDE desteği      | Statik                               |
| Veritabanı          | Admin panel ile dinamik ayarlar | Canlı değişiklik imkanı       | Performans için cache gerekir        |
| Helper Fonksiyonlar | Kolay erişim için               | Kullanımı basit               | Fazla kullanımı karmaşıklaştırabilir |

---

### İhtiyacına göre kombinasyon da kullanabilirsin.

Örneğin: Çoğu sabiti `config/` dosyasında tut, ama kritik ve gizli anahtarları `.env` dosyasına koy.
