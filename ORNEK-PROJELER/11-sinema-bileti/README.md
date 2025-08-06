# 🎦 Sinema Bileti Satış Sistemi

## 📋 Proje Tanımı

Sinema salonlarında film gösterimlerini yönetmek, bilet satışı yapmak ve koltuk rezervasyonu sağlamak için geliştirilmiş sistem. Kullanıcılar film seçebilir, koltuk rezerve edebilir ve online ödeme yapabilir.

## 🎯 Proje Hedefleri

- Film gösterim programı yönetim sistemi
- Interaktif koltuk haritası ve rezervasyon
- Online bilet satış ve ödeme sistemi
- Bilet sorgulama ve iptal işlemleri
- Admin paneli ile salon ve film yönetimi

## 🗺️ Veritabanı Yapısı

### 1. movies (Filmler)

```sql
id (Primary Key)
title (varchar 200) - Film adı
description (text) - Film açıklaması
duration (integer) - Süre (dakika)
genre (varchar 100) - Tür
rating (varchar 10) - Yaş sınırı (G, PG, PG-13, R)
director (varchar 100) - Yönetmen
cast (text) - Oyuncular
poster_image (varchar 255) - Poster görsel yolu
trailer_url (varchar 255) - Fragman URL
release_date (date) - Vizyon tarihi
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. halls (Salonlar)

```sql
id (Primary Key)
name (varchar 100) - Salon adı
capacity (integer) - Toplam kapasite
row_count (integer) - Sıra sayısı
seats_per_row (integer) - Sıra başına koltuk
hall_type (enum) - Salon tipi (standard, vip, imax)
screen_type (varchar 50) - Ekran tipi
sound_system (varchar 50) - Ses sistemi
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. showtimes (Gösterimler)

```sql
id (Primary Key)
movie_id (Foreign Key) - movies.id
hall_id (Foreign Key) - halls.id
show_date (date) - Gösterim tarihi
show_time (time) - Gösterim saati
base_price (decimal 8,2) - Temel bilet fiyatı
vip_price (decimal 8,2) - VIP bilet fiyatı
available_seats (integer) - Müsait koltuk sayısı
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 4. seats (Koltuklar)

```sql
id (Primary Key)
hall_id (Foreign Key) - halls.id
row_number (varchar 5) - Sıra numarası (A, B, C...)
seat_number (integer) - Koltuk numarası
seat_type (enum) - Koltuk tipi (standard, vip, disabled)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 5. bookings (Rezervasyonlar)

```sql
id (Primary Key)
showtime_id (Foreign Key) - showtimes.id
customer_name (varchar 100) - Müşteri adı
customer_email (varchar 255) - Müşteri e-posta
customer_phone (varchar 20) - Müşteri telefon
total_price (decimal 8,2) - Toplam fiyat
booking_status (enum) - Durum (pending, confirmed, cancelled, used)
booking_code (varchar 20) - Rezervasyon kodu
payment_method (varchar 50) - Ödeme yöntemi
payment_status (enum) - Ödeme durumu (pending, completed, failed)
qr_code (varchar 255) - QR kod
created_at (timestamp)
updated_at (timestamp)
```

### 6. booking_seats (Rezervasyon Koltukları)

```sql
id (Primary Key)
booking_id (Foreign Key) - bookings.id
seat_id (Foreign Key) - seats.id
seat_price (decimal 8,2) - Koltuk fiyatı
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/movies - Aktif film listesi
GET /api/movies/{id} - Film detayı
GET /api/showtimes - Gösterim programı
GET /api/showtimes/movie/{id} - Filme ait seanslar
GET /api/showtimes/{id}/seats - Koltuk durumu
POST /api/bookings - Rezervasyon yap
GET /api/bookings/{code} - Rezervasyon sorgula
```

### Customer Endpoints (JWT korumalı)

```
GET /api/customer/bookings - Rezervasyonlarım
PUT /api/customer/bookings/{id}/cancel - Rezervasyon iptal
GET /api/customer/tickets/{id} - Dijital bilet
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/movies - Film ekle
PUT /api/admin/movies/{id} - Film güncelle
DELETE /api/admin/movies/{id} - Film sil
POST /api/admin/halls - Salon ekle
PUT /api/admin/halls/{id} - Salon güncelle
POST /api/admin/showtimes - Gösterim ekle
GET /api/admin/bookings - Tüm rezervasyonlar
GET /api/admin/reports/daily - Günlük rapor
GET /api/admin/reports/movie/{id} - Film raporu
```

### Auth Endpoints

```
POST /api/auth/login - Giriş yap
POST /api/auth/register - Kayıt ol
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## 🧭 Menü Yapısı

### Ana Menü

- 🏠 Ana Sayfa
- 🎦 Filmler
- 🕰️ Seanslar
- 🎫 Biletlerim
- 👤 Giriş/Kayıt

### Kullanıcı Menü (Giriş sonrası)

- 🏠 Ana Sayfa
- 🎫 Biletlerim
- 📋 Rezervasyonlarım
- 👤 Profil

### Admin Menü

- 📈 Kontrol Paneli
- 🎦 Film Yönetimi
- 🏢 Salon Yönetimi
- 🕰️ Seans Yönetimi
- 🎫 Rezervasyon Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Giriş)    |
+----------------------------------+
|  Hero Section                    |
|  "En Yeni Filmler Burada"       |
|  [Film Ara] [Seans Bul]         |
+----------------------------------+
|  Vizyondaki Filmler (Slider)     |
|  [Poster] [Poster] [Poster]     |
|  Avatar 2 | Aksiyon | 162 dk    |
+----------------------------------+
|  Bugünkü Seanslar               |
|  🕰️ 14:00 Avatar 2 - Salon 1  |
|  🕰️ 16:30 Top Gun - Salon 2   |
|  🕰️ 19:00 Avatar 2 - Salon 1  |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Film Detayı (/movies/:id)

```
+----------------------------------+
|  Avatar 2: Su Yolu               |
+----------------------------------+
|  [Büyük Poster]  | Fragman Video  |
|                  | Tür: Aksiyon    |
|                  | Süre: 162 dk   |
|                  | Yönetmen: Cameron|
|                  | IMDB: 8.2/10   |
+----------------------------------+
|  Konu                            |
|  Jake Sully ve ailesinin...      |
+----------------------------------+
|  Seanslar                        |
|  Bugün (16 Ara)                 |
|  [14:00] [16:30] [19:00] [21:30] |
|  Yarın (17 Ara)                 |
|  [14:00] [16:30] [19:00] [21:30] |
+----------------------------------+
```

### 3. Koltuk Seçimi (/showtimes/:id/seats)

```
+----------------------------------+
|  Avatar 2 - 16 Ara 19:00        |
|  Salon 1 | Fiyat: 25 TL         |
+----------------------------------+
|           PERDE                  |
+----------------------------------+
|  A [1][2][3][4]  [5][6][7][8]   |
|  B [1][2][✓][✓]  [5][6][7][8]   |
|  C [1][2][3][4]  [5][✓][7][8]   |
|  D [1][2][X][X]  [5][6][7][8]   |
+----------------------------------+
|  Koltuk Durumu:                  |
|  [ ] Müsait  [✓] Seçili  [X] Dolu |
+----------------------------------+
|  Seçilen: B3, B4, C6 (3 koltuk) |
|  Toplam: 75 TL                  |
|  [Devam Et]                     |
+----------------------------------+
```

### 4. Ödeme Sayfası (/booking/payment)

```
+----------------------------------+
|  Ödeme Bilgileri                 |
+----------------------------------+
|  Film: Avatar 2                 |
|  Tarih: 16 Ara 2024 - 19:00     |
|  Salon: 1 | Koltuklar: B3,B4,C6 |
|  Toplam: 75 TL                  |
+----------------------------------+
|  Müşteri Bilgileri              |
|  Ad Soyad: [_______________]    |
|  E-posta: [_______________]     |
|  Telefon: [_______________]     |
+----------------------------------+
|  Ödeme Yöntemi                  |
|  ● Kredi Kartı                 |
|  ○ Banka Kartı                 |
|  ○ Nakit (Ödeme Noktasında)    |
+----------------------------------+
|  [Geri] [Ödemeyi Tamamla]       |
+----------------------------------+
```

### 5. Dijital Bilet (/tickets/:id)

```
+----------------------------------+
|  🎫 DİJİTAL BİLET              |
+----------------------------------+
|  AVATAR 2: SU YOLU              |
|  16 Aralık 2024 - 19:00         |
+----------------------------------+
|  Salon: 1                       |
|  Koltuklar: B3, B4, C6          |
|  Fiyat: 75 TL                   |
+----------------------------------+
|  Müşteri: Ahmet Yılmaz          |
|  Rezervasyon: #ABC123456        |
+----------------------------------+
|       [QR KOD]                  |
|    ███  ███  ███          |
|    █  █  █  █  █          |
|    ███  ███  ███          |
+----------------------------------+
|  Girişte QR kodu okutunuz       |
|  [PDF İndir] [E-posta Gönder]   |
+----------------------------------+
```

### 6. Admin Kontrol Paneli (/admin)

```
+----------------------------------+
|  Sinema Yönetim Paneli          |
+----------------------------------+
|  Bugünkü Özet (16 Ara 2024)     |
|  Toplam Bilet: 234              |
|  Toplam Gelir: 5,850 TL         |
|  Doluluk Oranı: %78             |
+----------------------------------+
|  Aktif Filmler: 8               |
|  Bugünkü Seanslar: 24            |
|  Bekleyen Rezervasyon: 12       |
+----------------------------------+
|  En Popüler Film               |
|  Avatar 2 (89 bilet)            |
|  Top Gun (67 bilet)             |
|  Black Panther (45 bilet)       |
+----------------------------------+
|  Hızlı İşlemler                 |
|  [Film Ekle] [Seans Oluştur]    |
|  [Raporlar] [Ayarlar]           |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Seat mapping algorithms
- ✅ Booking conflict resolution
- ✅ Payment integration
- ✅ QR code generation
- ✅ Inventory management
- ✅ Time-based availability
- ✅ Revenue reporting

### Vue.js + Quasar

- ✅ Interactive seat selection
- ✅ Real-time seat updates
- ✅ Payment form integration
- ✅ QR code display
- ✅ Movie poster galleries
- ✅ Responsive cinema layout
- ✅ Mobile ticket display

### Genel Beceriler

- ✅ Sinema yönetim sistemleri
- ✅ Rezervasyon ve bilet satış mantığı
- ✅ Ödeme işlemleri entegrasyonu
- ✅ Envanter takibi
- ✅ Kullanıcı deneyimi tasarımı
- ✅ Gelir optimizasyonu

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Koltuk haritalama sistemi
4. Rezervasyon çakışma kontrolü
5. Ödeme entegrasyonu
6. QR kod üretimi

### 2. Frontend (Quasar)

1. Film listeleme arayüzü
2. Etkileşimli koltuk seçimi
3. Rezervasyon akışı
4. Ödeme entegrasyonu
5. Dijital bilet görüntüleme
6. Admin yönetim paneli

### 3. Test ve Optimizasyon

1. Koltuk seçimi testleri
2. Ödeme akışı testleri
3. Çakışma çözümleme testleri
4. Performans optimizasyonu

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- QR kod için SimpleSoftwareIO/simple-qrcode
- Ödeme entegrasyonu için Stripe/PayPal
- Real-time koltuk güncellemeleri için WebSocket
- Poster görselleri için TMDB API
- Responsive tasarim mobil uyumlume seat availability
- Booking workflow management
- QR code generation
- Date/time handling
- Payment simulation

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Koltuk haritasi basit grid yapısı ile
- Ödeme entegrasyonu simule edilecek
- QR kod için basit kütüphane kullanılacak
