# Küçük İlanlar Sitesi

## Proje Tanımı

Kullanıcıların ücretsiz ilan verebileceği, arama yapabileceği ve iletişim kurabileceği platform. Emlak, araç, elektronik gibi kategorilerde ilan verilebilir.

## Proje Hedefleri

- İlan oluşturma ve yönetim sistemi
- Kategori bazlı sınıflandırma
- Gelişmiş arama ve filtreleme
- İlan sahipleri ile iletişim sistemi
- Favori ilan takip sistemi

## Veritabanı Yapısı

### 1. categories (Kategoriler)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
slug (varchar 100) - URL dostu ad
description (text) - Açıklama
icon (varchar 50) - İkon sınıfı
parent_id (integer) - Üst kategori ID
sort_order (integer) - Sıralama
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. listings (İlanlar)

```sql
id (Primary Key)
user_id (Foreign Key) - users.id
category_id (Foreign Key) - categories.id
title (varchar 200) - İlan başlığı
description (text) - İlan açıklaması
price (decimal 10,2) - Fiyat
currency (varchar 3) - Para birimi
location (varchar 100) - Konum
contact_name (varchar 100) - İletişim adı
contact_phone (varchar 20) - Telefon
contact_email (varchar 255) - E-posta
status (enum) - Durum (active, pending, expired, sold)
expires_at (timestamp) - Son geçerlilik tarihi
view_count (integer) - Görüntülenme sayısı
is_featured (boolean) - Öne çıkarılmış mı
created_at (timestamp)
updated_at (timestamp)
```

### 3. listing_images (İlan Görselleri)

```sql
id (Primary Key)
listing_id (Foreign Key) - listings.id
image_path (varchar 255) - Görsel yolu
original_name (varchar 255) - Orijinal dosya adı
file_size (integer) - Dosya boyutu
is_primary (boolean) - Ana görsel mi
sort_order (integer) - Sıralama
created_at (timestamp)
updated_at (timestamp)
```

### 4. favorites (Favoriler)

```sql
id (Primary Key)
user_id (Foreign Key) - users.id
listing_id (Foreign Key) - listings.id
created_at (timestamp)
updated_at (timestamp)
```

### 5. messages (Mesajlar)

```sql
id (Primary Key)
listing_id (Foreign Key) - listings.id
sender_name (varchar 100) - Gönderen adı
sender_email (varchar 255) - Gönderen e-posta
sender_phone (varchar 20) - Gönderen telefon
message (text) - Mesaj içeriği
is_read (boolean) - Okundu mu
replied_at (timestamp) - Yanıtlanma tarihi
created_at (timestamp)
updated_at (timestamp)
```

## API Endpoint'leri

### Public Endpoints

```
GET /api/listings - Aktif ilan listesi
GET /api/listings/{id} - İlan detayı (view_count artırır)
GET /api/categories - Kategori listesi
GET /api/listings/search - İlan arama
GET /api/listings/featured - Öne çıkan ilanlar
GET /api/categories/{id}/listings - Kategoriye ait ilanlar
```

### User Endpoints (JWT korumalı)

```
POST /api/listings - İlan oluştur
PUT /api/listings/{id} - İlan güncelle
DELETE /api/listings/{id} - İlan sil
POST /api/listings/{id}/images - Görsel yükle
DELETE /api/listings/images/{id} - Görsel sil
POST /api/favorites - Favoriye ekle
DELETE /api/favorites/{id} - Favoriden çıkar
GET /api/user/listings - İlanlarım
GET /api/user/favorites - Favori ilanlarım
GET /api/user/messages - Mesajlarım
```

### Admin Endpoints (JWT korumalı)

```
GET /api/admin/listings - Tüm ilanlar
PUT /api/admin/listings/{id}/approve - İlan onayla
PUT /api/admin/listings/{id}/reject - İlan reddet
POST /api/admin/categories - Kategori ekle
PUT /api/admin/categories/{id} - Kategori güncelle
GET /api/admin/reports/listings - İlan istatistikleri
```

### Auth Endpoints

```
POST /api/auth/login - Giriş yap
POST /api/auth/register - Kayıt ol
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## Menü Yapısı

### Ana Menü

- Ana Sayfa
- İlanlar
- Arama
- İlan Ver
- Giriş/Kayıt

### Kullanıcı Menü (Giriş sonrası)

- Ana Sayfa
- İlanlarım
- Favorilerim
- Mesajlarım
- Profil

### Admin Menü

- Kontrol Paneli
- İlan Yönetimi
- Kategori Yönetimi
- Kullanıcı Yönetimi
- Raporlar
- Profil

## UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Arama)    |
+----------------------------------+
|  Hero Section                    |
|  "Aradığınızı Bulun"           |
|  [Arama Kutusu] [Ara]      |
+----------------------------------+
|  Popüler Kategoriler (Grid)      |
|  [Emlak] [Araç] [Elektronik]|
|  [Giyim] [Eşya] [Hayvan]   |
+----------------------------------+
|  Öne Çıkan İlanlar             |
|  [Görsel] 3+1 Daire - 450.000 TL |
|  [Görsel] 2018 Civic - 320.000 TL|
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. İlan Listesi (/listings)

```
+----------------------------------+
|  İlanlar - Emlak (245 ilan)      |
+----------------------------------+
|  Filtreler                       |
|  Fiyat: [Min] - [Max]           |
|  Şehir: [Ankara]             |
|  Sıralama: [Tarih]           |
+----------------------------------+
|  [Görsel] 3+1 Daire - Kızılay     |
|  450.000 TL | 125 görüntülenme   |
|  Durum: Aktif | 25 gün kaldı     |
|  [Düzenle] [Sil] [Mesajlar (3)] |
+----------------------------------+
|  [Görsel] 2+1 Daire - Çankaya     |
|  320.000 TL | 89 görüntülenme    |
|  Durum: Süresi Dolmuş            |
|  [Yenile] [Düzenle] [Sil]       |
+----------------------------------+
|  [1] [2] [3] ... [12] [>>]      |
+----------------------------------+
```

### 3. İlan Detayı (/listings/:id)

```
+----------------------------------+
|  3+1 Daire - Kızılay            |
+----------------------------------+
|  [Ana Görsel - Büyük]           |
|  [Küçük] [Görsel] [Galeri]     |
+----------------------------------+
|  450.000 TL | Görüntülenme: 125  |
|  Konum: Kızılay, Ankara         |
|  Tarih: 2 gün önce              |
+----------------------------------+
|  Açıklama                       |
|  Merkezi konumda, asansörlü...   |
+----------------------------------+
|  İletişim                        |
|  Ahmet Yılmaz                   |
|  0532 123 45 67                 |
|  [Mesaj Gönder] [Favoriye Ekle]|
+----------------------------------+
```

### 4. İlan Ver (/listings/create)

```
+----------------------------------+
|  Yeni İlan Ver                   |
+----------------------------------+
|  Kategori Seçimi                 |
|  [Emlak] > [Satılık]       |
+----------------------------------+
|  İlan Bilgileri                  |
|  Başlık: [_______________]      |
|  Açıklama: [_______________]    |
|  Fiyat: [___] TL                |
|  Konum: [_______________]       |
+----------------------------------+
|  İletişim Bilgileri              |
|  Ad: [_______________]          |
|  Telefon: [_______________]     |
|  E-posta: [_______________]     |
+----------------------------------+
|  Görseller                       |
|  [Görsel Yükle] (Maks 5 adet)   |
|  [Görsel] [Görsel] [Görsel] [ + ] [ + ]  |
+----------------------------------+
|  [İlanı Yayınla] [İptal]        |
+----------------------------------+
```

### 5. Kullanıcı Paneli (/user/listings)

```
+----------------------------------+
|  İlanlarım (8 aktif, 2 süresi dolmuş)|
+----------------------------------+
|  [Görsel] 3+1 Daire - Kızılay     |
|  450.000 TL | 125 görüntülenme   |
|  Durum: Aktif | 25 gün kaldı     |
|  [Düzenle] [Sil] [Mesajlar (3)] |
+----------------------------------+
|  [Görsel] 2018 Civic - Temiz      |
|  320.000 TL | 89 görüntülenme    |
|  Durum: Süresi Dolmuş            |
|  [Yenile] [Düzenle] [Sil]       |
+----------------------------------+
|  [Yeni İlan Ver]                 |
+----------------------------------+
```

## Öğrenim Kazanımları

### Laravel API

- Dosya yükleme ve yönetimi
- Görsel işleme ve boyutlandırma
- Gelişmiş arama algoritması
- Konum entegrasyonu
- İletişim formu yönetimi
- Süre sonu yönetimi

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- İlan süresi 30 gün
- Maksimum 5 görsel yüklenebilir
- Basit moderasyon sistemi
