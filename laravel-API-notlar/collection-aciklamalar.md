# Postman Koleksiyon Açıklamaları

Bu dokümanda, Laravel API projesi için hazırlanmış Postman koleksiyon dosyalarının detaylı açıklamaları bulunmaktadır.

## Koleksiyon Dosyalarının Karşılaştırmalı Analizi

### 1. [postman-collection-v1.json](./postman-collection-v1.json)

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

### 2. [postman-collection-v2.json](./postman-collection-v2.json)

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

### 3. [postman-collection-v3.json](./postman-collection-v3.json)

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

| Özellik                    | v1    | v2   | v3    | v4    |
| -------------------------- | ----- | ---- | ----- | ----- |
| Temel CRUD İşlemleri       | ✅    | ✅   | ✅    | ✅    |
| Örnek Test Verileri        | ❌    | ✅   | ✅    | ✅    |
| Zengin Örnek Veri Seti     | ❌    | ✅   | ✅    | ✅    |
| Değişken Desteği           | ❌    | ❌   | ✅    | ✅    |
| Farklı Ortam Desteği       | ❌    | ❌   | ✅    | ✅    |
| URL Yapılandırılabilirliği | ❌    | ❌   | ✅    | ✅    |
| JWT Kimlik Doğrulama       | ❌    | ❌   | ❌    | ✅    |
| Korumalı Endpoint'ler      | ❌    | ❌   | ❌    | ✅    |
| Kullanım Kolaylığı         | Temel | Orta | İleri | Uzman |

## Koleksiyonları İçe Aktarma

1. Postman uygulamasını açın
2. "Import" butonuna tıklayın
3. İlgili JSON dosyasını seçin veya sürükleyip bırakın
4. Koleksiyonun içe aktarılmasını bekleyin

## Önerilen Kullanım

1. **Yeni Başlayanlar İçin:** [v1](./postman-collection-v1.json) ile temel işlevleri öğrenin
2. **Test Verisi Gerektirenler İçin:** [v2](./postman-collection-v2.json) ile zengin örnek verilerle çalışın
3. **Farklı Ortamlarda Test İçin:** [v3](./postman-collection-v3.json) ile değişken yapısını kullanın

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

### 4. [postman-collection-v4-jwt.json](./postman-collection-v4-jwt.json)

**Temel Özellikler:**

- **Versiyon:** v4 (JWT Destekli)
- **Açıklama:** JWT kimlik doğrulama sistemi ile tam özellikli API test koleksiyonu.
- **Yenilikler (V3'é göre):**
  - **JWT Kimlik Doğrulama Sistemi:**
    - Kullanıcı kaydı (Register)
    - Kullanıcı girişi (Login)
    - Kullanıcı profili görüntüleme (Me)
    - Çıkış yapma (Logout)
    - Token yenileme (Refresh)
  - **Otomatik Token Yönetimi:**
    - Login sonrası JWT token otomatik kaydedilir
    - Tüm korumalı isteklerde otomatik kullanılır
    - Logout sonrası token temizlenir
  - **Korumalı Endpoint'ler:**
    - Tüm kategori işlemleri JWT gerektirir
    - Tüm ürün işlemleri JWT gerektirir
    - Test veri oluşturma JWT gerektirir
  - **Gelişmiş Hata Yönetimi:**
    - Token olmadan erişim kontrolü
    - Geçersiz token kontrolü
    - Süresi dolmuş token kontrolü
- **Kullanım:** Üretim seviyesinde tam güvenli API testi için kullanılır.

**JWT Test Sırası:**

1. **Register:** Yeni kullanıcı oluştur
2. **Login:** Giriş yap ve JWT token al
3. **Protected Endpoints:** Korumalı endpoint'leri test et
4. **Me:** Kullanıcı profili kontrol et
5. **Logout:** Güvenli çıkış yap

**Desteklenen Kimlik Doğrulama İşlemleri:**

- Kullanıcı kaydı (email, şifre doğrulama ile)
- Güvenli giriş (email/şifre)
- JWT token tabanlı oturum yönetimi
- Otomatik token süresi kontrolü
- Güvenli çıkış (token iptal etme)

**Güvenlik Özellikleri:**

- Tüm hassas endpoint'ler JWT ile korunur
- Token süresi: 1 saat (3600 saniye)
- Geçersiz token'lar reddedilir
- Logout sonrası token geçersiz hale gelir

## Koleksiyon Evrim Süreci

```
V1 (Temel) → V2 (Test Verileri) → V3 (Değişkenler) → V4 (JWT Güvenlik)
```

**Her versiyon bir öncekinin tüm özelliklerini içerir ve yeni özellikler ekler:**

- **V1 → V2:** Test veri setleri eklendi
- **V2 → V3:** Değişken desteği ve ortam yönetimi eklendi
- **V3 → V4:** JWT kimlik doğrulama ve güvenlik katmanı eklendi

## Hangi Koleksiyonu Kullanmalıyım?

### Yeni Başlayanlar İçin:

- **V1** ile başlayın: Temel CRUD işlemlerini öğrenin
- **V2**'ye geçin: Gerçekçi test verileriyle çalışın

### Orta Seviye Geliştiriciler İçin:

- **V3** kullanın: Farklı ortamlar arası geçiş yapın
- Değişken yönetimini öğrenin

### İleri Seviye / Üretim İçin:

- **V4** kullanın: Tam güvenli API testi
- JWT kimlik doğrulama sistemini test edin
- Üretim ortamına hazır test senaryoları
