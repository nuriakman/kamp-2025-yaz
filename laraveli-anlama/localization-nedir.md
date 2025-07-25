# 🌍 **LOCALIZATION NEDİR?**

Localization (yerelleştirme), Laravel uygulamanı farklı **dillerde çalışacak şekilde** hazırlamak demektir.

Yani:

- Uygulamanın içerikleri (etiketler, hata mesajları, uyarılar, buton yazıları vs.) birden fazla dile çevrilebilir.
- Kullanıcının dil tercihi veya tarayıcı diline göre içerik gösterilebilir.

---

## 📂 **DİL DOSYALARI NEREDE BULUNUR?**

Laravel, dil dosyalarını şu dizinde tutar:

```
resources/lang/
```

Örneğin:

```
resources/lang/en/messages.php
resources/lang/tr/messages.php
```

> Her klasör bir dili temsil eder (`en`, `tr`, `de`, vs.)

---

## 📄 **BİR DİL DOSYASI ÖRNEĞİ**

**resources/lang/tr/messages.php**

```php
return [
    'welcome' => 'Hoş geldiniz!',
    'login'   => 'Giriş yap',
];
```

**resources/lang/en/messages.php**

```php
return [
    'welcome' => 'Welcome!',
    'login'   => 'Login',
];
```

---

## 💬 **ÇEVİRİYİ GÖSTERMEK İÇİN:**

Blade veya controller içinde:

```blade
{{ __('messages.welcome') }}
```

ya da

```php
__('messages.welcome')
```

Laravel otomatik olarak `config/app.php` içindeki `locale` ayarına göre doğru dili seçer.

---

## ⚙️ **DİL AYARI (varsayılan dil):**

`config/app.php` dosyasında:

```php
'locale' => 'tr', // varsayılan dil
```

Alternatif: Otomatik kullanıcı dilini tanımlamak için middleware oluşturabilirsin.

---

## 🔁 **DİNAMİK DİL DEĞİŞTİRME**

Örneğin kullanıcının seçtiği dile göre:

```php
App::setLocale('en');
```

Bu, o request süresince geçerli olur. (Session’a kaydedilerek kalıcı yapılabilir.)

---

## 📍 **YERELLEŞTİRİLEBİLİR LARAVEL BİLEŞENLERİ**

| Bileşen              | Yerelleştirilebilir mi?         |
| -------------------- | ------------------------------- |
| Hata mesajları       | ✅ Evet (`validation.php`)      |
| Gün/ay isimleri      | ✅ Evet (Carbon destekler)      |
| Blade içerikleri     | ✅ Evet (`__()` fonksiyonu ile) |
| Model hata mesajları | ✅ Evet                         |

---

## 📁 **HAZIR DİL PAKETLERİ**

Laravel için Türkçe dahil birçok hazır dil paketi mevcut.

Kurulum örneği:

```bash
composer require laravel-lang/publisher
php artisan lang:add tr
```

> Bu komutlar `validation.php`, `auth.php` gibi sistem mesajlarını Türkçe'ye çevirir.

---

## 🧠 **BONUS: JSON DİL DOSYALARI**

Eğer `resources/lang/tr.json` gibi bir dosya oluşturursan, şu şekilde kullanabilirsin:

```php
__('Welcome to our site')
```

JSON kullanımı sade ve kolaydır, ama dosya büyüdükçe yönetimi zorlaşabilir.

---

## 🔚 **ÖZET**

| Özellik            | Açıklama                        |
| ------------------ | ------------------------------- |
| `resources/lang/`  | Dil dosyalarının yeri           |
| `__('...')`        | Çeviri çağırma fonksiyonu       |
| `App::setLocale()` | Dinamik dil değiştirme          |
| `lang/add tr`      | Laravel resmi çeviri desteği    |
| `locale`           | `config/app.php` varsayılan dil |

---

Laravel’de localization, çok dilli siteler ve uluslararası projeler için oldukça güçlü bir altyapı sunar.

## 🚀 Laravel Localization — İleri Seviye Teknikler

---

### 1️⃣ **Kullanıcı Bazlı Dil Tercihlerini Kalıcı Yapmak**

#### Sorun:

Kullanıcı dil tercihini oturum (session) veya çerez (cookie) bazlı tutmak çoğu zaman yeterli değil. Çünkü kullanıcı oturumu kapatıp açtığında ya da farklı cihazdan girdiğinde dil tercihi kaybolur.

#### Çözüm:

- Kullanıcı modeline `language` alanı ekle → DB’de sakla
- Girişte ve dil değişiminde bu alanı güncelle
- Middleware ile her istek öncesi DB’den dili ayarla

```php
// Middleware örneği
public function handle($request, Closure $next)
{
    if (auth()->check()) {
        App::setLocale(auth()->user()->language);
    } else {
        App::setLocale(session('locale', config('app.locale')));
    }

    return $next($request);
}
```

---

### 2️⃣ **URL veya Alt Domain ile Dil Yönetimi**

#### Örnekler:

- `https://site.com/en/about`
- `https://site.com/tr/about`

Ya da:

- `https://en.site.com/about`
- `https://tr.site.com/about`

#### Nasıl yapılır?

- Route prefix kullanarak:

```php
Route::group(['prefix' => '{locale}', 'middleware' => 'setlocale'], function() {
    Route::get('/about', 'AboutController@index');
});
```

- Middleware’de gelen `{locale}` parametresini kontrol et:

```php
public function handle($request, Closure $next)
{
    $locale = $request->route('locale');
    if (in_array($locale, ['en', 'tr'])) {
        App::setLocale($locale);
    } else {
        App::setLocale(config('app.locale'));
    }
    return $next($request);
}
```

---

### 3️⃣ **Fallback Locale ve Eksik Çevirilerin Yönetimi**

Bazen çevirisi olmayan metinler olabilir.

- Laravel `config/app.php` içindeki `fallback_locale` ayarını kullanır.

```php
'locale' => 'tr',
'fallback_locale' => 'en',
```

- Eğer `tr` dosyasında metin yoksa `en` dosyasındaki gösterilir.

- İstersen özel bir middleware ile eksik çevirileri loglayabilir veya farklı işlem yapabilirsin.

---

### 4️⃣ **Veritabanı Tabanlı Çeviri Yönetimi**

Dosya tabanlı dil dosyaları büyük projelerde yetersiz kalabilir.

- Çevirileri veritabanında saklamak için paketler (örn: [spatie/laravel-translation-loader](https://github.com/spatie/laravel-translation-loader)) kullanılabilir.
- Admin panelinden çeviri düzenleme yapılabilir.
- Performans için cache ile desteklenmeli.

---

### 5️⃣ **Dinamik İçeriklerin Çevirisi**

Blog, ürün açıklamaları gibi dinamik içeriklerin farklı dillerde tutulması gerekir.

Yöntemler:

- **Tablo içinde dil sütunları:** `title_tr`, `title_en` vs.
- **Ayrı çeviri tablosu:** `product_translations` (product_id, locale, title, description)
- **JSON sütunu:** Tek tabloda `translations` alanı JSON olarak saklanabilir.

---

### 6️⃣ **Date, Time ve Number Formatting**

Localization sadece metin çevirisi değil, tarih/saat ve sayı biçimlendirme de içerir.

- Laravel’in kullandığı [Carbon](https://carbon.nesbot.com/) kütüphanesi lokalizasyon destekler.

```php
\Carbon\Carbon::now()->locale('tr')->isoFormat('LLLL');
```

- Sayı ve para formatlama için PHP’nin `NumberFormatter` sınıfı veya paketler kullanılabilir.

---

### 7️⃣ **Çoklu Dil İçin Validation Mesajları**

`resources/lang/{locale}/validation.php` dosyalarında dil bazlı hata mesajları yer alır.

- Bu dosyalar otomatik olarak seçilen dile göre kullanılır.

- Kendi özel validation mesajlarını da buraya ekleyebilirsin.

---

### 8️⃣ **Middleware ile Otomatik Dil Algılama**

Kullanıcının tarayıcı dilini `Accept-Language` HTTP başlığından alıp, en uygun dili seçmek için middleware yazabilirsin.

```php
public function handle($request, Closure $next)
{
    $preferred = $request->getPreferredLanguage(['en', 'tr']);
    App::setLocale($preferred ?? config('app.locale'));

    return $next($request);
}
```

---

## 🎯 Özet

| İleri seviye konu                     | Açıklama                               |
| ------------------------------------- | -------------------------------------- |
| Kullanıcı dil tercihini DB’de tutma   | Dil tercihi kalıcı olur                |
| URL / Alt domain ile dil seçimi       | SEO ve UX için faydalı                 |
| Fallback locale                       | Eksik çeviriler için yedek dil         |
| Veritabanı tabanlı çeviri yönetimi    | Büyük projelerde kolay yönetim         |
| Dinamik içerik çevirisi               | Ürün, blog vs. veritabanında çoklu dil |
| Tarih, sayı, para yerelleştirme       | Kullanıcıya göre formatlama            |
| Validation mesajlarının çok dilliliği | Formlarda dil uyumu                    |
| Otomatik dil algılama middleware      | Kullanıcı deneyimini artırır           |

---
