# dump-autoload Nedir?

Çok iyi bir soru! `composer dump-autoload` komutu, Laravel (ve genel olarak Composer kullanan PHP projeleri) için **önemli bir yapılandırma komutudur.**
Şimdi sana sade ve net bir şekilde anlatayım 👇

---

## 📦 `composer dump-autoload` Nedir?

Bu komut, Laravel projenin **autoload (otomatik sınıf ve dosya yükleyici)** sistemini **yeniden oluşturur**.

Yani:

> 📁 Projene yeni dosyalar, sınıflar, fonksiyonlar eklediysen
> 🧠 Laravel’in bunları otomatik olarak bulup çalıştırabilmesi için "autoload listesi"ni günceller.

---

## 🧰 Nerelerde Kullanılır?

| Nerede?                                        | Neden Gerekir?                                                 |
| ---------------------------------------------- | -------------------------------------------------------------- |
| ✅ Yeni bir `helper.php` dosyası eklediğinde   | Çünkü `composer.json` içine "autoload > files" olarak tanıttın |
| ✅ Yeni bir sınıf/sınıf dosyası oluşturduğunda | Laravel otomatik yükleme için bu sınıfı tanısın diye           |
| ✅ `app/` klasöründe yeni dizinler açtığında   | PSR-4 eşlemesi için                                            |
| 🧹 Autoload cache bozulduğunda                 | Temiz ve güncel bir autoload dosyası üretmek için              |

---

## 🛠️ Teknik olarak ne yapar?

```bash
composer dump-autoload
```

Bu komut, projenin kökünde (`vendor/`) şu dosyaları günceller:

- `vendor/autoload.php`
- `vendor/composer/autoload_classmap.php`
- `vendor/composer/autoload_files.php`
- `vendor/composer/autoload_psr4.php`
  ... gibi dosyaları **yeniden oluşturur**.

> Yani Laravel, bu dosyaları kullanarak projenin hangi dosyayı nerede bulacağını bilir.

---

## 🔁 Alternatif Komutlar

| Komut                       | Ne yapar?                                                                   |
| --------------------------- | --------------------------------------------------------------------------- |
| `composer dump-autoload -o` | Optimize eder (autoload dosyaları tek dosyada toplanır). Üretimde önerilir. |
| `composer install`          | `composer.lock` dosyasını okur ve paketleri yükler; autoload da üretir.     |
| `composer update`           | Tüm paketleri günceller, autoload’ı da yeniden yazar.                       |

---

## 📌 Özetle

| Soru                                   | Cevap                                                                                                |
| -------------------------------------- | ---------------------------------------------------------------------------------------------------- |
| `composer dump-autoload` ne işe yarar? | Projeye eklenen sınıf, helper, dosyaları Laravel’in tanıyabilmesi için autoload sistemini günceller. |
| Ne zaman kullanılır?                   | Yeni helper dosyası eklediğinde veya Laravel bir dosyayı “bulamıyor” hatası verdiğinde.              |
| Üretimde kullanılır mı?                | Evet, `-o` (optimize) ile birlikte.                                                                  |

---
