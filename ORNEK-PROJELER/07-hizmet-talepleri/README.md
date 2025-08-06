# 🛠️ Hizmet Talepleri Sistemi

## 📋 Proje Tanımı

Müşteri hizmet taleplerini yönetme, takip etme ve çözüm süreci sistemi. Müşteriler talep oluşturabilir, teknisyenler atanabilir ve süreç takip edilebilir.

## 🎯 Proje Hedefleri

- Hizmet talebi oluşturma ve yönetimi
- Talep durumu takibi ve bildirim sistemi
- Teknisyen atama ve iş yükü yönetimi
- Müşteri memnuniyet anketi ve değerlendirme
- Raporlama ve analiz sistemi

## 🗺️ Veritabanı Yapısı

### 1. customers (Müşteriler)

```sql
id (Primary Key)
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
address (text) - Adres
city (varchar 100) - Şehir
district (varchar 100) - İlçe
postal_code (varchar 10) - Posta kodu
customer_type (enum) - Müşteri tipi (individual, corporate)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. service_categories (Hizmet Kategorileri)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
description (text) - Açıklama
icon (varchar 100) - İkon
estimated_duration (integer) - Tahmini süre (dakika)
priority_level (enum) - Öncelik seviyesi (low, medium, high, urgent)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. technicians (Teknisyenler)

```sql
id (Primary Key)
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
specialty (varchar 255) - Uzmanlık alanı
experience_years (integer) - Deneyim yılı
rating (decimal 3,2) - Değerlendirme puanı
is_available (boolean) - Müsaitlik durumu
max_daily_requests (integer) - Günlük maksimum talep
created_at (timestamp)
updated_at (timestamp)
```

### 4. service_requests (Hizmet Talepleri)

```sql
id (Primary Key)
customer_id (Foreign Key) - customers.id
category_id (Foreign Key) - service_categories.id
technician_id (Foreign Key) - technicians.id (nullable)
request_number (varchar 20) - Talep numarası
title (varchar 255) - Talep başlığı
description (text) - Talep açıklaması
priority (enum) - Öncelik (low, medium, high, urgent)
status (enum) - Durum (pending, assigned, in_progress, completed, cancelled)
scheduled_date (date) - Planlanan tarih
scheduled_time (time) - Planlanan saat
completed_at (timestamp) - Tamamlanma zamanı
estimated_cost (decimal 10,2) - Tahmini maliyet
actual_cost (decimal 10,2) - Gerçek maliyet
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

### 5. request_updates (Talep Güncellemeleri)

```sql
id (Primary Key)
request_id (Foreign Key) - service_requests.id
user_type (enum) - Kullanıcı tipi (customer, technician, admin)
user_id (integer) - Kullanıcı ID
status_from (enum) - Önceki durum
status_to (enum) - Yeni durum
message (text) - Güncelleme mesajı
attachments (json) - Ek dosyalar
created_at (timestamp)
```

## 🔌 API Endpoint'leri

### Customer Endpoints

```
POST /api/customers/register - Müşteri kayıt
POST /api/customers/login - Müşteri giriş
GET /api/customers/profile - Profil bilgileri
PUT /api/customers/profile - Profil güncelle
GET /api/customers/requests - Müşteri talepleri
POST /api/requests - Yeni talep oluştur
GET /api/requests/{id} - Talep detayı
POST /api/requests/{id}/feedback - Geri bildirim
```

### Technician Endpoints (JWT korumalı)

```
GET /api/technician/requests - Atanan talepler
GET /api/technician/requests/available - Müsait talepler
PUT /api/technician/requests/{id}/accept - Talebi kabul et
PUT /api/technician/requests/{id}/start - İşe başla
PUT /api/technician/requests/{id}/complete - Tamamla
POST /api/technician/requests/{id}/update - Durum güncelle
GET /api/technician/schedule - Çalışma programı
```

### Admin Endpoints (JWT korumalı)

```
GET /api/admin/requests - Tüm talepler
PUT /api/admin/requests/{id}/assign - Teknisyen ata
POST /api/admin/categories - Kategori ekle
PUT /api/admin/categories/{id} - Kategori güncelle
POST /api/admin/technicians - Teknisyen ekle
PUT /api/admin/technicians/{id} - Teknisyen güncelle
GET /api/admin/reports - Raporlar
GET /api/admin/statistics - İstatistikler
```

### Public Endpoints

```
GET /api/categories - Hizmet kategorileri
GET /api/service-areas - Hizmet bölgeleri
POST /api/quick-request - Hızlı talep (kayıtsız)
```

## 🧭 Menü Yapısı

### Müşteri Menü

- 🏠 Ana Sayfa
- 📝 Yeni Talep
- 📋 Taleplerim
- 🔍 Talep Takip
- 📞 İletişim
- 👤 Profil

### Teknisyen Menü

- 📈 Dashboard
- 📋 Atanan Talepler
- 🔍 Müsait Talepler
- 📅 Programım
- 📄 Raporlarım
- 👤 Profil

### Admin Menü

- 📈 Dashboard
- 📋 Talep Yönetimi
- 👨‍🔧 Teknisyen Yönetimi
- 📂 Kategori Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Müşteri Ana Sayfa (/customer)

```
+----------------------------------+
|  Hoşgeldiniz, Ahmet Bey         |
+----------------------------------+
|  Hızlı İşlemler                 |
+----------------------------------+
|  [📝 Yeni Talep]              |
|  [🔍 Talep Takip]             |
|  [📞 Destek Hattı]            |
+----------------------------------+
|  Son Taleplerim                  |
|  🟡 #2024-001 | Klima Arıza   |
|     Durum: İşleme alındı         |
|  🟢 #2024-002 | Elektrik      |
|     Durum: Tamamlandı            |
+----------------------------------+
```

### 2. Yeni Talep Oluşturma (/customer/new-request)

```
+----------------------------------+
|  Yeni Hizmet Talebi              |
+----------------------------------+
|  Kategori Seçimi                 |
|  ● Klima Servisi               |
|  ○ Elektrik İşleri             |
|  ○ Tesisatcılık                |
+----------------------------------+
|  Talep Detayları                 |
|  Başlık: [Klima soğutmuyor]     |
|  Açıklama: [Textarea]           |
|  Öncelik: [Normal ▼]           |
|  Tarih: [Yarın]                 |
|  Saat: [09:00-12:00]             |
+----------------------------------+
|  [Talep Gönder] [İptal]         |
+----------------------------------+
```

### 3. Talep Detayı (/customer/requests/:id)

```
+----------------------------------+
|  Talep Detayı - #2024-001       |
+----------------------------------+
|  Klima Arıza Talebi             |
|  Durum: 🟡 İşleme Alındı       |
|  Teknisyen: Mehmet Yıldız       |
|  Planlanan: 16 Aralık, 10:00    |
+----------------------------------+
|  Süreç Takibi                    |
|  ✅ Talep Alındı (15/12 14:30)   |
|  ✅ Teknisyen Atandı (15/12 15:00)|
|  🟡 Randevu Planlandı (16/12)   |
|  ⏳ İş Başlayacak               |
|  ⏳ Tamamlanacak                |
+----------------------------------+
|  [📞 Teknisyeni Ara]          |
|  [💬 Mesaj Gönder]           |
+----------------------------------+
```

### 4. Teknisyen Dashboard (/technician)

```
+----------------------------------+
|  Teknisyen Panel - Mehmet Yıldız |
+----------------------------------+
|  Bugünkü Randevular (3)          |
+----------------------------------+
|  10:00 | #001 | Klima | İstanbul |
|        [Başla] [Detay]          |
|  14:00 | #003 | Elektrik | Kad.  |
|        [Başla] [Detay]          |
+----------------------------------+
|  Bekleyen Talepler (5)           |
|  🔴 Acil | Tesisatcılık | Beşiktaş|
|       [Kabul Et] [Detay]        |
+----------------------------------+
|  Bugünkü İstatistikler          |
|  Tamamlanan: 2 | Kalan: 3       |
+----------------------------------+
```

### 5. Talep İşleme (/technician/requests/:id)

```
+----------------------------------+
|  Talep İşleme - #2024-001        |
+----------------------------------+
|  Müşteri: Ahmet Yılmaz          |
|  Adres: Beyoğlu, İstanbul        |
|  Telefon: 0555 123 4567         |
+----------------------------------+
|  Talep Bilgileri                 |
|  Kategori: Klima Servisi         |
|  Açıklama: Klima soğutmuyor      |
|  Öncelik: Yüksek                 |
+----------------------------------+
|  İş Durumu                      |
|  [İşe Başla] [Ara Ver]          |
|  [Tamamla] [Sorun Bildir]       |
+----------------------------------+
|  Notlar: [Textarea]             |
|  Maliyet: [0.00 TL]             |
+----------------------------------+
```

### 6. Admin - Talep Yönetimi (/admin/requests)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Filtreler                       |
|  Durum: [Tümü ▼] Tarih: [Bugün]|
|  [Filtrele] [Temizle]            |
+----------------------------------+
|  Talep Tablosu                   |
|  # | Müşteri | Kategori | Durum   |
|    | [Ata] [Düzenle] [Sil]      |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Workflow management
- ✅ Status tracking system
- ✅ Assignment algorithms
- ✅ Notification system
- ✅ File upload handling
- ✅ Complex relationships
- ✅ Reporting and analytics

### Vue.js + Quasar

- ✅ Multi-role interfaces
- ✅ Real-time status updates
- ✅ Form validation
- ✅ File upload components
- ✅ Calendar integration
- ✅ Map integration
- ✅ Mobile-responsive design

### Genel Beceriler

- ✅ Customer service systems
- ✅ Workflow design
- ✅ Resource allocation
- ✅ Performance tracking
- ✅ User experience design
- ✅ Business process automation

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Status workflow logic
4. Assignment algorithm
5. Notification system
6. Reporting system

### 2. Frontend (Quasar)

1. Multi-role authentication
2. Customer request forms
3. Technician dashboard
4. Admin management panels
5. Real-time status tracking
6. Mobile optimization

### 3. Test ve Optimizasyon

1. Workflow testing
2. Performance optimization
3. Mobile usability
4. Integration testing

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Real-time updates için WebSocket kullanılacak
- SMS bildirimleri için Twilio entegrasyonu
- Harita entegrasyonu için Google Maps
- Dosya yükleme için cloud storage
- Mobile-first yaklaşımla tasarlanacak

## 🗺️ Veritabanı

- service_requests: title, description, priority, status, customer_info
- technicians: name, specialty, availability
- assignments: request_id, technician_id, assigned_date

## 🎓 Kazanımlar

- Workflow management, Status tracking, Assignment logic
