# 🩸 Kan Testi Sistemi

## 📋 Proje Tanımı

Hastanelerde kan testi isteme, sonuç girişi ve rapor görüntüleme işlemlerinin yönetildiği basit bir laboratuvar sistemi.

## 🎯 Proje Hedefleri

- Hasta kayıt ve test isteme
- Laboratuvar sonuç girişi
- Test sonuçlarını görüntüleme
- Rapor yazdırma
- Admin paneli ile test yönetimi

## 🗄️ Veritabanı Yapısı

### 1. patients (Hastalar)

```sql
id (Primary Key)
tc_number (varchar 11) - TC Kimlik No
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
birth_date (date) - Doğum tarihi
gender (enum) - Cinsiyet (male, female)
phone (varchar 20) - Telefon
email (varchar 255) - E-posta
address (text) - Adres
created_at (timestamp)
updated_at (timestamp)
```

### 2. test_types (Test Türleri)

```sql
id (Primary Key)
name (varchar 100) - Test adı
code (varchar 20) - Test kodu
description (text) - Test açıklaması
normal_range_min (decimal 8,2) - Normal değer alt sınır
normal_range_max (decimal 8,2) - Normal değer üst sınır
unit (varchar 20) - Birim
price (decimal 8,2) - Test ücreti
is_active (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### 3. test_requests (Test İstekleri)

```sql
id (Primary Key)
patient_id (Foreign Key) - patients.id
request_number (varchar 20) - İstek numarası
doctor_name (varchar 255) - Doktor adı
requested_date (date) - İstek tarihi
status (enum) - Durum (pending, completed, cancelled)
notes (text) - Notlar
total_price (decimal 10,2) - Toplam ücret
created_at (timestamp)
updated_at (timestamp)
```

### 4. test_request_items (Test İstek Kalemleri)

```sql
id (Primary Key)
test_request_id (Foreign Key) - test_requests.id
test_type_id (Foreign Key) - test_types.id
result_value (decimal 8,2) - Sonuç değeri
result_status (enum) - Sonuç durumu (normal, high, low)
result_date (timestamp) - Sonuç tarihi
notes (text) - Sonuç notları
created_at (timestamp)
updated_at (timestamp)
```

### 5. users (Kullanıcılar)

```sql
id (Primary Key)
name (varchar 255)
email (varchar 255)
password (varchar 255)
role (enum) - Rol (admin, doctor, lab_tech)
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/test-types - Test türlerini listele
POST /api/patients - Hasta kayıt
GET /api/patients/{tc}/tests - Hasta test sonuçları (TC ile)
GET /api/test-requests/{number}/status - Test durumu sorgula
```

### Doctor/Admin Endpoints (JWT korumalı)

```
GET /api/patients - Hasta listesi
POST /api/test-requests - Test istemi oluştur
GET /api/test-requests - Test istemlerini listele
PUT /api/test-requests/{id} - Test istemi güncelle
```

### Lab Tech Endpoints (JWT korumalı)

```
GET /api/lab/pending-tests - Bekleyen testler
PUT /api/lab/test-results/{id} - Test sonucu gir
GET /api/lab/test-requests/{id} - Test detayları
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/test-types - Test türü oluştur
PUT /api/admin/test-types/{id} - Test türü güncelle
DELETE /api/admin/test-types/{id} - Test türü sil
GET /api/admin/reports - Raporlar
```

## 🧭 Menü Yapısı

### Ana Menü

- 🏠 Ana Sayfa
- 🩸 Test Türleri
- 👤 Hasta Kayıt
- 🔍 Sonuç Sorgula
- 👨‍⚕️ Giriş

### Doctor Menü

- 📊 Dashboard
- 👥 Hasta Yönetimi
- 🩸 Test İstemi
- 📋 İstemlerim
- 👤 Profil

### Lab Tech Menü

- 📊 Dashboard
- 🧪 Bekleyen Testler
- 📝 Sonuç Girişi
- 📋 Tamamlanan Testler
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü)            |
+----------------------------------+
|  Hero Section                    |
|  "Kan Testi Sistemi"            |
+----------------------------------+
|  Hızlı İşlemler                  |
|  [Hasta Kayıt] [Test İstemi]     |
|  [Sonuç Sorgula] [Giriş]         |
+----------------------------------+
|  Test Türleri (Grid)             |
|  🩸 Hemogram                     |
|  🧬 Biyokimya                    |
|  🦠 Mikrobiyoloji                |
+----------------------------------+
```

### 2. Hasta Kayıt (/patient-register)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Hasta Kayıt Formu               |
+----------------------------------+
|  Kişisel Bilgiler                |
|  TC No: [___________]            |
|  Ad: [_____] Soyad: [_____]      |
|  Doğum Tarihi: [__/__/____]      |
|  Cinsiyet: [Dropdown]            |
+----------------------------------+
|  İletişim Bilgileri              |
|  Telefon: [___________]          |
|  E-posta: [___________]          |
|  Adres: [Textarea]               |
+----------------------------------+
|  [Kaydet] [Temizle]              |
+----------------------------------+
```

### 3. Test İstemi (/test-request)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Test İstemi Formu               |
+----------------------------------+
|  Hasta Seçimi                    |
|  TC No: [___________] [Ara]      |
|  Hasta: Ahmet Yılmaz (35)        |
+----------------------------------+
|  Test Seçimi                     |
|  ☑ Hemogram (₺50)               |
|  ☐ Glukoz (₺30)                 |
|  ☐ Kolesterol (₺40)             |
+----------------------------------+
|  Doktor: [___________]           |
|  Toplam: ₺120                    |
|  [İstek Oluştur]                |
+----------------------------------+
```

### 4. Sonuç Sorgula (/result-query)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Test Sonucu Sorgula             |
+----------------------------------+
|  TC Kimlik No: [___________]     |
|  [Sorgula]                       |
+----------------------------------+
|  Sonuçlar (varsa)                |
|  📋 İstek No: LAB2024001         |
|     Tarih: 15.06.2024            |
|     Durum: ✅ Tamamlandı         |
|     [Raporu Görüntüle]          |
+----------------------------------+
```

### 5. Test Raporu (/report/:id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  LABORATUVAR TEST RAPORU         |
+----------------------------------+
|  Hasta Bilgileri                 |
|  Ad Soyad: Ahmet Yılmaz          |
|  TC: 12345678901                 |
|  Tarih: 15.06.2024               |
+----------------------------------+
|  Test Sonuçları                  |
|  Test Adı    | Sonuç | Normal    |
|  Hemoglobin  | 14.5  | 12-16     |
|  Glukoz      | 95    | 70-110    |
+----------------------------------+
|  [Yazdır] [PDF İndir]            |
+----------------------------------+
```

### 6. Lab - Bekleyen Testler (/lab/pending)

```
+----------------------------------+
|  Lab Header                      |
+----------------------------------+
|  Bekleyen Testler                |
+----------------------------------+
|  Test Tablosu                    |
|  İstek No | Hasta | Test | Tarih  |
|  LAB001   | Ahmet | Hemogram     |
|          | [Sonuç Gir]          |
+----------------------------------+
```

### 7. Sonuç Girişi (/lab/result/:id)

```
+----------------------------------+
|  Lab Header                      |
+----------------------------------+
|  Test Sonucu Girişi              |
|  İstek: LAB2024001               |
|  Hasta: Ahmet Yılmaz             |
+----------------------------------+
|  Test Sonuçları                  |
|  Hemoglobin: [____] g/dL         |
|  Normal: 12-16 g/dL              |
|  Durum: [Normal/Yüksek/Düşük]    |
+----------------------------------+
|  Notlar: [Textarea]              |
|  [Sonuçları Kaydet]              |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Medical data handling
- ✅ Complex form relationships
- ✅ Status workflow management
- ✅ Report generation
- ✅ Role-based access control
- ✅ Data validation for medical values

### Vue.js + Quasar

- ✅ Multi-step forms
- ✅ Dynamic form validation
- ✅ Print functionality
- ✅ PDF generation
- ✅ Medical report layouts
- ✅ Status indicators

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Gerçek tıbbi veriler kullanılmayacak
- Basit rapor formatı
- PDF export basit tutulacak
- Güvenlik önlemleri temel seviyede
