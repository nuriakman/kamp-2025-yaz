# 👨‍⚕️ Doktor Randevu Sistemi

## 📋 Proje Tanımı

Hastanelerde doktor randevusu alma, randevu takibi ve doktor müsaitlik yönetimi sistemi. Hastalar online randevu alabilir, doktorlar müsaitlik durumlarını yönetebilir.

## 🎯 Proje Hedefleri

- Online randevu alma sistemi
- Doktor müsaitlik takvimi
- Randevu iptal/erteleme işlemleri
- SMS/Email bildirim sistemi
- Admin paneli ile randevu yönetimi

## 🗺️ Veritabanı Yapısı

### 1. doctors (Doktorlar)

```sql
id (Primary Key)
name (varchar 100) - Doktor adı
speciality (varchar 100) - Uzmanlık alanı
phone (varchar 20) - Telefon
email (varchar 255) - E-posta
room_number (varchar 10) - Oda numarası
appointment_duration (integer) - Randevu süresi (dakika)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. appointments (Randevular)

```sql
id (Primary Key)
doctor_id (Foreign Key) - doctors.id
patient_name (varchar 100) - Hasta adı
patient_phone (varchar 20) - Hasta telefon
patient_email (varchar 255) - Hasta e-posta
appointment_date (date) - Randevu tarihi
appointment_time (time) - Randevu saati
status (enum) - Durum (pending, confirmed, cancelled, completed)
notes (text) - Notlar
cancel_reason (text) - İptal nedeni
created_at (timestamp)
updated_at (timestamp)
```

### 3. doctor_schedules (Çalışma Saatleri)

```sql
id (Primary Key)
doctor_id (Foreign Key) - doctors.id
day_of_week (integer) - Haftanın günü (1-7)
start_time (time) - Başlangıç saati
end_time (time) - Bitiş saati
is_available (boolean) - Müsait mi
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/doctors - Aktif doktor listesi
GET /api/doctors/{id}/schedule - Doktor müsaitlik takvimi
GET /api/doctors/{id}/available-slots - Müsait randevu saatleri
POST /api/appointments - Randevu oluştur
GET /api/appointments/{id} - Randevu sorgula
```

### Patient Endpoints (JWT korumalı)

```
GET /api/patient/appointments - Hasta randevuları
PUT /api/patient/appointments/{id} - Randevu güncelle
DELETE /api/patient/appointments/{id} - Randevu iptal et
```

### Doctor Endpoints (JWT korumalı)

```
GET /api/doctor/appointments - Doktor randevuları
PUT /api/doctor/appointments/{id}/confirm - Randevu onayla
PUT /api/doctor/appointments/{id}/complete - Randevu tamamla
GET /api/doctor/schedule - Çalışma programı
PUT /api/doctor/schedule - Program güncelle
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/doctors - Doktor ekle
PUT /api/admin/doctors/{id} - Doktor güncelle
GET /api/admin/appointments - Tüm randevular
GET /api/admin/reports/daily - Günlük rapor
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
- 👨‍⚕️ Doktorlar
- 📅 Randevu Al
- 🔍 Randevu Sorgula
- 👤 Giriş/Kayıt

### Hasta Menü (Giriş sonrası)

- 🏠 Ana Sayfa
- 📅 Randevularım
- 👨‍⚕️ Doktor Ara
- 👤 Profil

### Doktor Menü

- 📈 Kontrol Paneli
- 📅 Randevularım
- 🕰️ Çalışma Saatleri
- 👤 Profil

### Admin Menü

- 📈 Kontrol Paneli
- 👨‍⚕️ Doktor Yönetimi
- 📅 Randevu Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Giriş)    |
+----------------------------------+
|  Hero Section                    |
|  "Kolay Randevu Alma"            |
|  [Randevu Al] [Randevu Sorgula]  |
+----------------------------------+
|  Uzmanlık Alanları (Grid)        |
|  [💗 Kardiyoloji] [🧠 Nöroloji]  |
|  [👁️ Göz] [🦷 Ortopedi]         |
+----------------------------------+
|  Doktorlarımız                   |
|  Dr. Ahmet Yılmaz - Kardiyoloji   |
|  Dr. Ayşe Demir - Nöroloji      |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Randevu Alma (/appointment/new)

```
+----------------------------------+
|  Randevu Alma                    |
+----------------------------------+
|  1. Uzmanlık Seçimi              |
|  [Kardiyoloji ▼]                |
+----------------------------------+
|  2. Doktor Seçimi                |
|  ● Dr. Ahmet Yılmaz             |
|  ○ Dr. Mehmet Kaya             |
+----------------------------------+
|  3. Tarih Seçimi                 |
|  [16 Ara] [17 Ara] [18 Ara]     |
+----------------------------------+
|  4. Saat Seçimi                  |
|  [09:00] [09:30] [10:00]        |
|  [10:30] [11:00] [11:30]        |
+----------------------------------+
|  5. Hasta Bilgileri              |
|  Ad Soyad: [____________]        |
|  Telefon: [____________]         |
|  E-posta: [____________]         |
+----------------------------------+
|  [Randevu Al] [İptal]            |
+----------------------------------+
```

### 3. Randevu Sorgulama (/appointment/search)

```
+----------------------------------+
|  Randevu Sorgulama               |
+----------------------------------+
|  Telefon Numarası:               |
|  [0532 123 45 67]               |
|  [Sorgula]                      |
+----------------------------------+
|  Randevu Bilgileri               |
|  Dr. Ahmet Yılmaz               |
|  Kardiyoloji                     |
|  16 Aralık 2024 - 10:30         |
|  Durum: Onaylandı                |
+----------------------------------+
|  [İptal Et] [Ertele]             |
+----------------------------------+
```

### 4. Doktor Kontrol Paneli (/doctor)

```
+----------------------------------+
|  Dr. Ahmet Yılmaz - Kontrol Paneli |
+----------------------------------+
|  Bugünkü Randevular (16 Ara)      |
+----------------------------------+
|  09:00 - Ayşe Yıldırım          |
|  [Tamamla] [Notlar]             |
+----------------------------------+
|  10:30 - Mehmet Kaya            |
|  [Onayla] [Reddet]              |
+----------------------------------+
|  Haftalık Özet                   |
|  Toplam Randevu: 45             |
|  Tamamlanan: 40                 |
|  İptal Edilen: 5                 |
+----------------------------------+
|  [Çalışma Saatleri] [Raporlar]   |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Date/time operations
- ✅ Complex scheduling logic
- ✅ Email/SMS notifications
- ✅ Calendar integration
- ✅ Time slot management
- ✅ Status workflow
- ✅ Conflict detection

### Vue.js + Quasar

- ✅ Calendar components
- ✅ Time picker integration
- ✅ Real-time updates
- ✅ Form validation
- ✅ Mobile-first design
- ✅ Push notifications
- ✅ Offline capabilities

### Genel Beceriler

- ✅ Healthcare system design
- ✅ Appointment scheduling
- ✅ Conflict resolution
- ✅ User experience design
- ✅ Business process automation
- ✅ Data privacy compliance

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Appointment scheduling logic
4. Conflict detection system
5. Notification system (email/SMS)
6. Calendar integration

### 2. Frontend (Quasar)

1. Doktor listesi ve arama
2. Randevu alma akışı
3. Takvim entegrasyonu
4. Hasta kontrol paneli
5. Doktor yönetim paneli
6. Gerçek zamanlı bildirimler

### 3. Test ve Optimizasyon

1. Appointment flow testing
2. Conflict detection testing
3. Performance optimization
4. Mobile responsiveness

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- SMS entegrasyonu için Twilio kullanılabilir
- Email için Laravel Mail kullanılacak
- Takvim entegrasyonu için FullCalendar.js
- Zaman çakışması kontrolü kritik
- KVKK uyumlu hasta bilgisi saklamasi
- GET /api/doctors/{id}/available-slots - Müsait saatler
- POST /api/appointments - Randevu oluştur
- GET /api/appointments/{id}/status - Randevu durumu

1. **Ana Sayfa**: Doktor arama ve seçim
2. **Randevu Alma**: Takvim ve saat seçimi
3. **Randevu Sorgula**: Randevu durumu takibi
4. **Admin Panel**: Randevu yönetimi

## 🎓 Öğrenim Kazanımları

- Takvim component'i kullanımı
- Zaman dilimi yönetimi
- Durum takip sistemi
- Bildirim sistemi entegrasyonu
