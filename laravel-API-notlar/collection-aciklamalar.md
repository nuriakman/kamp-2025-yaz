# Postman Koleksiyon Açıklamaları

Bu dokümanda, Laravel API projesi için hazırlanmış Postman koleksiyon dosyalarının detaylı açıklamaları bulunmaktadır.

## Koleksiyon Dosyalarının Karşılaştırmalı Analizi

### 1. `postman-collection-v1.json`

**Temel Özellikler:**

- **Versiyon:** v1 (Temel)
- **Açıklama:** Laravel API'nin temel CRUD işlemlerini içeren ilk sürüm koleksiyonu.
- **İçerik:**
  - Kategoriler için tam CRUD işlemleri (Listeleme, Ekleme, Görüntüleme, Güncelleme, Silme)
  - Ürünler için tam CRUD işlemleri (Listeleme, Ekleme, Görüntüleme, Güncelleme, Silme)
  - Örnek istek gövdeleri ve beklenen yanıt formatları
- **Örnek Veri Yapısı:**
  - Kategori: `{ id, name, created_at, updated_at }`
  - Ürün: `{ id, name, description, price, category_id, created_at, updated_at }`
- **Kullanım:** API'nin temel işlevselliğini test etmek için kullanılır.

### 2. `postman-collection-v2.json`

**Temel Özellikler:**

- **Versiyon:** v2 (Gelişmiş)
- **Açıklama:** V1 üzerine geliştirilmiş, test veri setleri ve kapsamlı test senaryoları içeren sürüm.
- **Yenilikler (V1'e göre):**
  - **Test Veri Oluşturma:** Örnek kategoriler ve ürünler içeren test veri seti
  - **Zengin Örnek Veriler:**
    - 3 farklı kategori (Elektronik, Ev & Yaşam, Spor & Outdoor)
    - Her kategori için 5'er adet örnek ürün
    - Gerçekçi fiyat ve açıklamalar
  - **Test Senaryoları:**
    - Geçerli/geçersiz veri girişleri
    - Eksik alan kontrolleri
    - Fiyat doğrulamaları
- **Kullanım:** API'nin farklı veri setleriyle çalışma yeteneğini test etmek için kullanılır.

### 3. `postman-collection-v3.json`

**Temel Özellikler:**

- **Versiyon:** v3 (Değişken Destekli)
- **Açıklama:** Ortam değişkenleri kullanan, daha esnek ve yapılandırılabilir koleksiyon.
- **Yenilikler (V2'ye göre):**
  - **Değişken Tabanlı Yapı:**
    - `{{host}}`: Sunucu adresi (varsayılan: localhost)
    - `{{port}}`: Port numarası (varsayılan: 8000)
    - `{{base_path}}`: API ön eki (varsayılan: api)
  - **Dinamik URL Yapısı:** `http://{{host}}:{{port}}/{{base_path}}/endpoint`
  - **Kolay Yapılandırma:** Farklı ortamlar için tek yerden ayar değişikliği
  - **Taşınabilirlik:** Farklı sunucu/port yapılandırmaları için uyumluluk
- **Kullanım:** Farklı geliştirme/üretim ortamlarında esnek test yapabilmek için kullanılır.

## Koleksiyonlar Arasındaki Temel Farklar

| Özellik                    | v1    | v2   | v3    |
| -------------------------- | ----- | ---- | ----- |
| Temel CRUD İşlemleri       | ✅    | ✅   | ✅    |
| Örnek Test Verileri        | ❌    | ✅   | ✅    |
| Zengin Örnek Veri Seti     | ❌    | ✅   | ✅    |
| Değişken Desteği           | ❌    | ❌   | ✅    |
| Farklı Ortam Desteği       | ❌    | ❌   | ✅    |
| URL Yapılandırılabilirliği | ❌    | ❌   | ✅    |
| Kullanım Kolaylığı         | Temel | Orta | İleri |

## Koleksiyonları İçe Aktarma

1. Postman uygulamasını açın
2. "Import" butonuna tıklayın
3. İlgili JSON dosyasını seçin veya sürükleyip bırakın
4. Koleksiyonun içe aktarılmasını bekleyin

## Önerilen Kullanım

1. **Yeni Başlayanlar İçin:** `v1` ile temel işlevleri öğrenin
2. **Test Verisi Gerektirenler İçin:** `v2` ile zengin örnek verilerle çalışın
3. **Farklı Ortamlarda Test İçin:** `v3` ile değişken yapısını kullanın

## Örnek Ortam Değişkenleri (v3 İçin)

```json
{
  "id": "laravel-api-env",
  "name": "Laravel API Ortamı",
  "values": [
    {
      "key": "host",
      "value": "localhost",
      "enabled": true
    },
    {
      "key": "port",
      "value": "8000",
      "enabled": true
    },
    {
      "key": "base_path",
      "value": "api",
      "enabled": true
    }
  ]
}
```

## Önemli Notlar

1. Tüm koleksiyonlar birbiriyle uyumludur, aynı API yapısını test ederler
2. `v3` koleksiyonunu kullanırken ortam değişkenlerini doğru şekilde yapılandırdığınızdan emin olun
3. Test verilerini oluşturmak için `v2` veya `v3` koleksiyonundaki "Test Verilerini Oluştur" bölümünü kullanabilirsiniz
4. Koleksiyonlar, [DERS-NOTU.md](./DERS-NOTU.md) dokümanında anlatılan API yapısına uygun olarak hazırlanmıştır
