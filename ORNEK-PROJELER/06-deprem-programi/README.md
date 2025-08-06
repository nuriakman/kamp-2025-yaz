# 🌍 Deprem Programı

## 📋 Proje Tanımı

Deprem verilerini takip eden, risk analizi yapan ve acil durum bilgilendirme sistemi. Gerçek zamanlı deprem verilerini kaydeder, risk haritası oluşturur ve vatandaşları bilgilendirir.

## 🎯 Proje Hedefleri

- Deprem verisi kayıt ve görüntüleme
- Risk haritası oluşturma ve görsellleştirme
- Acil durum bildirimleri ve uyarı sistemi
- İstatistiksel analiz ve raporlama
- Halkı bilgilendirme platformu

## 🗺️ Veritabanı Yapısı

### 1. earthquakes (Depremler)

```sql
id (Primary Key)
magnitude (decimal 3,1) - Büyüklük
latitude (decimal 10,8) - Enlem
longitude (decimal 11,8) - Boylam
depth (decimal 5,2) - Derinlik (km)
location (varchar 255) - Konum açıklaması
occurred_at (timestamp) - Oluşma zamanı
status (enum) - Durum (automatic, reviewed, confirmed)
source (varchar 100) - Veri kaynağı
is_significant (boolean) - Önemli deprem mi
created_at (timestamp)
updated_at (timestamp)
```

### 2. regions (Bölgeler)

```sql
id (Primary Key)
name (varchar 100) - Bölge adı
risk_level (enum) - Risk seviyesi (low, medium, high, very_high)
population (integer) - Nüfus
area_km2 (decimal 8,2) - Alan (km2)
min_latitude (decimal 10,8) - Minimum enlem
max_latitude (decimal 10,8) - Maksimum enlem
min_longitude (decimal 11,8) - Minimum boylam
max_longitude (decimal 11,8) - Maksimum boylam
description (text) - Açıklama
created_at (timestamp)
updated_at (timestamp)
```

### 3. alerts (Uyarılar)

```sql
id (Primary Key)
earthquake_id (Foreign Key) - earthquakes.id
alert_type (enum) - Uyarı tipi (info, warning, emergency)
message (text) - Uyarı mesajı
affected_regions (json) - Etkilenen bölgeler
status (enum) - Durum (active, expired, cancelled)
sent_at (timestamp) - Gönderim zamanı
expires_at (timestamp) - Geçerlilik süresi
created_at (timestamp)
updated_at (timestamp)
```

### 4. emergency_contacts (Acil Durum İletişim)

```sql
id (Primary Key)
region_id (Foreign Key) - regions.id
organization (varchar 100) - Kuruluş adı
contact_type (enum) - İletişim tipi (fire, police, medical, rescue)
phone (varchar 20) - Telefon
email (varchar 255) - E-posta
address (text) - Adres
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 5. statistics (Deprem İstatistikleri)

```sql
id (Primary Key)
date (date) - Tarih
region_id (Foreign Key) - regions.id (opsiyonel)
total_earthquakes (integer) - Toplam deprem sayısı
avg_magnitude (decimal 3,1) - Ortalama büyüklük
max_magnitude (decimal 3,1) - En büyük deprem
min_magnitude (decimal 3,1) - En küçük deprem
significant_count (integer) - Önemli deprem sayısı
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/earthquakes - Son depremler listesi
GET /api/earthquakes/recent - Son 24 saatteki depremler
GET /api/earthquakes/significant - Önemli depremler
GET /api/earthquakes/map - Harita için deprem verileri
GET /api/regions - Bölge listesi
GET /api/regions/{id}/earthquakes - Bölgeye göre depremler
GET /api/alerts/active - Aktif uyarılar
GET /api/emergency-contacts/{region} - Acil durum iletişim
GET /api/statistics/daily - Günlük istatistikler
GET /api/statistics/monthly - Aylık istatistikler
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/earthquakes - Deprem verisi ekle
PUT /api/admin/earthquakes/{id} - Deprem güncelle
DELETE /api/admin/earthquakes/{id} - Deprem sil
POST /api/admin/alerts - Uyarı oluştur
PUT /api/admin/alerts/{id} - Uyarı güncelle
POST /api/admin/regions - Bölge ekle
PUT /api/admin/regions/{id} - Bölge güncelle
POST /api/admin/emergency-contacts - İletişim ekle
PUT /api/admin/emergency-contacts/{id} - İletişim güncelle
```

### Auth Endpoints

```
POST /api/auth/login - Giriş yap
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## 🧭 Menü Yapısı

### Genel Kullanıcı Menü

- 🏠 Ana Sayfa
- 🌍 Son Depremler
- 🗺️ Deprem Haritası
- 📈 İstatistikler
- ⚠️ Aktif Uyarılar
- 📞 Acil Durum İletişim
- 📚 Bilgi Bankası

### Admin Menü

- 📈 Dashboard
- 🌍 Deprem Yönetimi
- 🗺️ Bölge Yönetimi
- ⚠️ Uyarı Yönetimi
- 📞 İletişim Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Deprem İzleme Merkezi          |
+----------------------------------+
|  Son Depremler                   |
+----------------------------------+
|  🔴 5.2 | İstanbul | 14:30     |
|  🟡 3.8 | Ankara | 12:15       |
|  🟢 2.1 | İzmir | 10:45        |
+----------------------------------+
|  Aktif Uyarılar (2)              |
|  ⚠️ Marmara Bölgesi - Dikkat    |
|  📵 Ege Bölgesi - Bilgi        |
+----------------------------------+
|  [Haritayı Gör] [İstatistikler]  |
+----------------------------------+
```

### 2. Deprem Haritası (/map)

```
+----------------------------------+
|  Deprem Haritası                |
+----------------------------------+
|  Filtreler                       |
|  Büyüklük: [2.0] - [9.0]        |
|  Tarih: [Son 7 gün ▼]          |
|  [Filtrele] [Temizle]            |
+----------------------------------+
|                                  |
|     🗺️ HARITA ALANI            |
|     (Leaflet/Google Maps)        |
|     • Deprem noktaları           |
|     • Bölge sınırları            |
|                                  |
+----------------------------------+
|  Lejant                          |
|  🔴 >5.0  🟡 3.0-5.0  🟢 <3.0   |
+----------------------------------+
```

### 3. Son Depremler (/earthquakes)

```
+----------------------------------+
|  Son Depremler                   |
+----------------------------------+
|  [Yenile] [Filtrele] [Dışa Aktar]|
+----------------------------------+
|  Tarih    | Büy. | Konum | Detay  |
|  14:30    | 5.2  | İst. | [Gör] |
|  12:15    | 3.8  | Ank.  | [Gör] |
|  10:45    | 2.1  | İzm.  | [Gör] |
+----------------------------------+
|  Sayfalama: [1] 2 3 ... 10      |
+----------------------------------+
```

### 4. Deprem Detayı (/earthquakes/:id)

```
+----------------------------------+
|  Deprem Detayları                |
+----------------------------------+
|  Büyüklük: 5.2 Richter           |
|  Konum: İstanbul Avrupa Yakası   |
|  Derinlik: 12.5 km               |
|  Zaman: 15 Aralık 2024, 14:30   |
+----------------------------------+
|  Koordinatlar                    |
|  Enlem: 41.0082                  |
|  Boylam: 28.9784                 |
+----------------------------------+
|  🗺️ Haritada Göster             |
|  📧 Rapor Al                    |
|  🔗 Paylaş                      |
+----------------------------------+
```

### 5. İstatistikler (/statistics)

```
+----------------------------------+
|  Deprem İstatistikleri           |
+----------------------------------+
|  Zaman Aralığı Seçimi             |
|  [Son 7 gün] [Son ay] [Özel]    |
+----------------------------------+
|  📈 Grafik Alanı                |
|  - Günlük deprem sayısı          |
|  - Büyüklük dağılımı             |
|  - Bölgesel karşılaştırma         |
+----------------------------------+
|  Özet Bilgiler                   |
|  Toplam: 156 | Ortalama: 2.8    |
|  En Büyük: 5.2 | Önemli: 3      |
+----------------------------------+
```

### 6. Aktif Uyarılar (/alerts)

```
+----------------------------------+
|  Aktif Uyarılar                  |
+----------------------------------+
|  🔴 ACİL DURUM                 |
|  Marmara Bölgesi                 |
|  5.2 büyüklüğünde deprem      |
|  Zaman: 14:30                    |
|  [Detaylar] [Paylaş]            |
+----------------------------------+
|  🟡 UYARI                      |
|  Ege Bölgesi                     |
|  Artçı deprem olasılığı          |
|  Zaman: 12:00                    |
|  [Detaylar] [Paylaş]            |
+----------------------------------+
```

### 7. Admin - Deprem Yönetimi (/admin/earthquakes)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Deprem] [Toplu İçe Aktar]|
+----------------------------------+
|  Deprem Tablosu                  |
|  Tarih | Büy. | Konum | Durum     |
|     | [Düzenle] [Sil] [Onayla]  |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Geographic data handling
- ✅ Real-time data processing
- ✅ Alert system implementation
- ✅ Statistical calculations
- ✅ External API integration
- ✅ JSON data storage
- ✅ Scheduled tasks (Cron)

### Vue.js + Quasar

- ✅ Interactive maps (Leaflet)
- ✅ Real-time data updates
- ✅ Chart.js integration
- ✅ Geolocation API
- ✅ Push notifications
- ✅ Data visualization
- ✅ Responsive design

### Genel Beceriler

- ✅ Emergency system design
- ✅ Geographic information systems
- ✅ Data analysis and statistics
- ✅ Alert and notification systems
- ✅ Public information systems
- ✅ Crisis management interfaces

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Geographic data validation
4. Alert system logic
5. Statistics calculation
6. External API integration

### 2. Frontend (Quasar)

1. Map component (Leaflet)
2. Real-time earthquake list
3. Statistics dashboard
4. Alert notification system
5. Admin management panels
6. Mobile responsive design

### 3. Test ve Optimizasyon

1. Geographic data accuracy
2. Real-time performance
3. Alert system reliability
4. Mobile usability

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Gerçek deprem verisi için AFAD API'si kullanılabilir
- Harita için Leaflet ücretsiz alternatifi
- Push notification için Firebase kullanılacak
- Responsive design mobil kullanım için kritik
- Alert sistemi gerçek zamanlı çalışmalı

## 🗺️ Veritabanı

- earthquakes (depremler): magnitude, location, depth, date
- risk_zones (risk bölgeleri): name, risk_level, coordinates
- alerts (uyarılar): message, severity, affected_areas

## 🎓 Kazanımlar

- Harita entegrasyonu, Real-time data handling, Alert sistemi
