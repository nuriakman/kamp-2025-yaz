# 🏕️ Kamp Öğrenci Kabul Sistemi

## 📋 Proje Tanımı

Eğitim kamplarına öğrenci başvurularının alındığı, başvuruların değerlendirildiği ve kabul edilen öğrencilerin sınıflara atandığı bir yönetim sistemi.

## 🎯 Proje Hedefleri

- Kamp programlarına online başvuru
- Başvuru durumu takibi
- Admin paneli ile başvuru değerlendirme
- Sınıf oluşturma ve öğrenci atama
- Kabul/ret bildirimleri

## 🗄️ Veritabanı Yapısı

### 1. camps (Kamplar)

```sql
id (Primary Key)
name (varchar 255) - Kamp adı
description (text) - Kamp açıklaması
start_date (date) - Başlangıç tarihi
end_date (date) - Bitiş tarihi
max_students (integer) - Maksimum öğrenci sayısı
application_deadline (date) - Başvuru son tarihi
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. applications (Başvurular)

```sql
id (Primary Key)
camp_id (Foreign Key) - camps.id
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
birth_date (date) - Doğum tarihi
education_level (enum) - Eğitim seviyesi (lise, universite, mezun)
motivation_letter (text) - Motivasyon mektubu
status (enum) - Durum (pending, approved, rejected)
notes (text) - Admin notları
applied_at (timestamp) - Başvuru tarihi
reviewed_at (timestamp) - İnceleme tarihi
reviewed_by (Foreign Key) - users.id (inceleyen admin)
created_at (timestamp)
updated_at (timestamp)
```

### 3. classes (Sınıflar)

```sql
id (Primary Key)
camp_id (Foreign Key) - camps.id
name (varchar 100) - Sınıf adı (A, B, C)
instructor_name (varchar 255) - Eğitmen adı
max_capacity (integer) - Maksimum kapasite
current_count (integer) - Mevcut öğrenci sayısı
classroom (varchar 100) - Sınıf/salon adı
created_at (timestamp)
updated_at (timestamp)
```

### 4. student_assignments (Öğrenci Atamaları)

```sql
id (Primary Key)
application_id (Foreign Key) - applications.id
class_id (Foreign Key) - classes.id
assigned_at (timestamp) - Atama tarihi
assigned_by (Foreign Key) - users.id (atayan admin)
created_at (timestamp)
updated_at (timestamp)
```

### 5. users (Kullanıcılar - Laravel varsayılan)

```sql
id (Primary Key)
name (varchar 255)
email (varchar 255)
password (varchar 255)
role (enum) - Rol (admin, instructor)
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/camps - Aktif kampları listele
GET /api/camps/{id} - Kamp detayları
POST /api/applications - Başvuru oluştur
GET /api/applications/{id}/status - Başvuru durumu sorgula (email ile)
```

### Admin Endpoints (JWT korumalı)

```
GET /api/admin/camps - Tüm kampları listele
POST /api/admin/camps - Kamp oluştur
PUT /api/admin/camps/{id} - Kamp güncelle
DELETE /api/admin/camps/{id} - Kamp sil

GET /api/admin/applications - Başvuruları listele (filtreleme ile)
PUT /api/admin/applications/{id}/review - Başvuru değerlendir
GET /api/admin/applications/{id} - Başvuru detayları

GET /api/admin/classes - Sınıfları listele
POST /api/admin/classes - Sınıf oluştur
PUT /api/admin/classes/{id} - Sınıf güncelle
DELETE /api/admin/classes/{id} - Sınıf sil

POST /api/admin/assignments - Öğrenci atama
GET /api/admin/assignments/camp/{camp_id} - Kamp atamalarını listele
DELETE /api/admin/assignments/{id} - Atamayı iptal et
```

### Auth Endpoints

```
POST /api/auth/login - Admin girişi
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## 🧭 Menü Yapısı

### Public Menü

- 🏠 Ana Sayfa
- 🏕️ Kamplar
- 📝 Başvuru Yap
- 🔍 Başvuru Sorgula
- 📞 İletişim

### Admin Menü (Giriş yapıldıktan sonra)

- 📊 Dashboard
- 🏕️ Kamp Yönetimi
- 📋 Başvuru Yönetimi
- 🏫 Sınıf Yönetimi
- 👥 Öğrenci Atamaları
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü)            |
+----------------------------------+
|  Hero Section                    |
|  "Eğitim Kamplarına Katılın"     |
|  [Kampları Görüntüle]           |
+----------------------------------+
|  Aktif Kamplar (Cards)           |
|  🏕️ Kamp Adı                     |
|     Tarih: 15-30 Haziran         |
|     Kontenjan: 25/50             |
|     [Başvur] [Detay]             |
+----------------------------------+
|  İstatistikler                   |
|  [Toplam Kamp] [Başvuru] [Öğrenci]|
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Kamp Detay (/camp/:id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Kamp Başlığı ve Tarihler        |
|  📅 15-30 Haziran 2024           |
+----------------------------------+
|  Kamp Bilgileri (2 Kolon)        |
|  Sol: Açıklama, Program          |
|  Sağ: Tarihler, Kontenjan        |
|       Başvuru Durumu             |
+----------------------------------+
|  [Başvuru Yap] [Geri Dön]        |
+----------------------------------+
```

### 3. Başvuru Formu (/application/:camp_id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Başvuru Formu: [Kamp Adı]       |
+----------------------------------+
|  Kişisel Bilgiler                |
|  Ad: [____] Soyad: [____]        |
|  E-posta: [________________]     |
|  Telefon: [________________]     |
|  Doğum Tarihi: [__/__/____]      |
+----------------------------------+
|  Eğitim Bilgileri                |
|  Seviye: [Dropdown]              |
+----------------------------------+
|  Motivasyon Mektubu              |
|  [Textarea - 500 karakter]       |
+----------------------------------+
|  [Başvuruyu Gönder]              |
+----------------------------------+
```

### 4. Başvuru Sorgula (/application-status)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Başvuru Durumu Sorgula          |
+----------------------------------+
|  E-posta: [________________]     |
|  [Sorgula]                       |
+----------------------------------+
|  Sonuç (varsa)                   |
|  ✅ Başvurunuz Onaylandı         |
|  Kamp: Yazılım Geliştirme        |
|  Sınıf: A Sınıfı                 |
|  Eğitmen: Ahmet Yılmaz           |
+----------------------------------+
```

### 5. Admin Dashboard (/admin)

```
+----------------------------------+
|  Admin Header + Menü             |
+----------------------------------+
|  İstatistikler (Cards)           |
|  [Aktif Kamplar] [Bekleyen]      |
|  [Onaylanan] [Reddedilen]        |
+----------------------------------+
|  Son Başvurular (Tablo)          |
|  Ad Soyad | Kamp | Durum | Tarih |
+----------------------------------+
|  Kamp Doluluk Oranları (Chart)   |
+----------------------------------+
```

### 6. Başvuru Yönetimi (/admin/applications)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Filtreler                       |
|  Kamp: [Dropdown] Durum: [Dropdown]|
+----------------------------------+
|  Başvuru Tablosu                 |
|  Ad | E-posta | Kamp | Durum     |
|    | [Görüntüle] [Onayla] [Reddet]|
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 7. Başvuru Detay (/admin/applications/:id)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Başvuru Bilgileri (Card)        |
|  Ad Soyad, E-posta, Telefon      |
|  Doğum Tarihi, Eğitim Seviyesi   |
+----------------------------------+
|  Motivasyon Mektubu (Card)       |
|  [Metin içeriği...]              |
+----------------------------------+
|  Değerlendirme (Card)            |
|  Notlar: [Textarea]              |
|  [Onayla] [Reddet] [Beklet]      |
+----------------------------------+
```

### 8. Sınıf Yönetimi (/admin/classes)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Sınıf] Kamp: [Dropdown] |
+----------------------------------+
|  Sınıf Kartları                  |
|  🏫 A Sınıfı                     |
|     Eğitmen: Ahmet Yılmaz        |
|     Öğrenci: 15/20               |
|     [Düzenle] [Öğrenciler]       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Enum kullanımı (status, education_level)
- ✅ Date handling ve validation
- ✅ Complex model relationships
- ✅ Query scopes ve filtering
- ✅ Soft deletes
- ✅ Event listeners (başvuru onaylandığında email)
- ✅ Custom validation rules

### Vue.js + Quasar

- ✅ Multi-step forms
- ✅ Date picker component'i
- ✅ Select/dropdown component'leri
- ✅ Table component'i (QTable)
- ✅ Status badges
- ✅ Modal dialogs
- ✅ Form validation
- ✅ Chart.js entegrasyonu

### Genel Beceriler

- ✅ Application workflow management
- ✅ Status tracking sistemi
- ✅ Capacity management
- ✅ Email notification sistemi
- ✅ Admin approval process
- ✅ Data filtering ve search

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Enum'ları tanımla
4. Controller'ları oluştur
5. Validation rule'larını ekle
6. Email notification'ları ekle

### 2. Frontend (Quasar)

1. Public sayfaları oluştur
2. Başvuru formu component'i
3. Admin panel layout'u
4. Başvuru yönetim component'leri
5. Sınıf yönetim component'leri
6. Dashboard ve istatistikler

### 3. Test ve Optimizasyon

1. Başvuru akışını test et
2. Admin onay sürecini test et
3. Email notification'ları test et
4. Kapasite kontrollerini test et

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Email notification'ları basit tutulacak
- Chart'lar için Chart.js kullanılacak
- File upload (CV, fotoğraf) opsiyonel
- Ödeme sistemi dahil edilmeyecek
