# Eğitmen Bilgisayarı Dizin Yapısı ve Açıklamaları

Bu dosya, Ubuntu kullanan eğitmen bilgisayarında eğitim boyunca kullanılacak ana dizin yapısını ve her klasörün amacını açıklar. Docker ortamı ve kaynak kodlarıyla uyumludur.

---

## Ana Dizin: `~/Documents/Kamp-2025-Yaz/`

```
Kamp-2025-Yaz/
├── src/                # Laravel backend kodları (public, routes, app, vs.)
├── frontend/           # Vue.js/Quasar frontend kodları
├── docker-compose.yml  # Docker servis tanımı
├── php.ini             # PHP ayar dosyası
├── .env                # Laravel ortam değişkenleri (ilk kurulumdan sonra oluşur)
├── egitim.plani.md     # Eğitim programı ve günlere göre konu başlıkları
├── ornek-prd.md        # Örnek PRD (gereksinim dokümanı)
├── ornek-ui-mockup.md  # Metin tabanlı UI mockup'ları
├── ornek-akis-diagramlari.md # Mermaid akış diyagramları
├── ornek-tablolar-api.md     # Tablo yapıları ve API endpointleri
├── docker-kurulum-ve-kullanim.md # Docker kurulum ve kullanım notları
└── egitmen-dizin-yapisi-ve-aciklama.md # (Bu dosya)
```

### Klasörler

- **src/** : Laravel backend uygulamasının tüm kaynak kodları ve alt klasörleri burada bulunur. (app, routes, public, database, vs.)
- **frontend/** : Vue.js veya Quasar ile geliştirilen frontend kodlarının tamamı burada tutulur. (src, public, package.json, vs.)

### Dosyalar

- **docker-compose.yml** : Tüm servislerin (app, db, node) tanımlandığı ana Docker yapılandırma dosyası.
- **php.ini** : PHP çalışma ortamı için özel ayarlar.
- **.env** : Laravel ortam değişkenleri (veritabanı, anahtarlar, vs.)
- **egitim.plani.md** : Gün gün eğitim akışını ve ana başlıkları içerir.
- **ornek-prd.md** : Grup projesi için örnek gereksinim dokümanı.
- **ornek-ui-mockup.md** : Temel ekranlar için metin tabanlı UI mockup'ları.
- **ornek-akis-diagramlari.md** : Mermaid ile hazırlanmış akış diyagramları.
- **ornek-tablolar-api.md** : Tablo yapıları ve API endpointlerinin listesi.
- **docker-kurulum-ve-kullanim.md** : Katılımcılar için Docker kurulum ve kullanım rehberi.
- **egitmen-dizin-yapisi-ve-aciklama.md** : Eğitmen bilgisayarındaki dizin yapısı ve açıklamaları.

---

## Notlar

- Tüm kaynak dosyalar bu ana dizinde tutulur ve yedeklenir.
- Docker ile çalışan servislerin kodları doğrudan bu dizinlerde güncellenir.
- Katılımcılarla paylaşılacak tüm dökümanlar ve örnekler burada düzenlenir.
- Geliştirme sırasında yeni dosya ve klasörler ihtiyaca göre eklenebilir.

Bu yapı, hem eğitmen hem de katılımcılar için düzenli ve erişilebilir bir çalışma ortamı sağlar.
