# Öğrenci Not Takip Sistemi

## Proje Tanımı

Öğrencilerin ders notlarını, devam durumlarını ve akademik performanslarını takip eden sistem. Öğretmenler not girebilir, öğrenciler notlarını görebilir, veliler çocuklarını takip edebilir.

## Proje Hedefleri

- Öğrenci ve ders yönetimi sistemi
- Not girişi ve otomatik hesaplama
- Devam takibi ve raporlama
- Akademik performans analizi
- Veli bilgilendirme ve takip sistemi

## Veritabanı Yapısı

### 1. students (Öğrenciler)

```sql
id (Primary Key)
student_number (varchar 20) - Öğrenci numarası
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
class (varchar 50) - Sınıf
email (varchar 255) - E-posta
parent_email (varchar 255) - Veli e-posta
phone (varchar 20) - Telefon
parent_phone (varchar 20) - Veli telefon
birth_date (date) - Doğum tarihi
address (text) - Adres
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. courses (Dersler)

```sql
id (Primary Key)
course_name (varchar 100) - Ders adı
course_code (varchar 20) - Ders kodu
credit (integer) - Kredi
teacher_name (varchar 100) - Öğretmen adı
teacher_id (integer) - Öğretmen ID
semester (varchar 20) - Dönem
class_hours (integer) - Haftalık saat
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. enrollments (Ders Kayıtları)

```sql
id (Primary Key)
student_id (Foreign Key) - students.id
course_id (Foreign Key) - courses.id
enrollment_date (date) - Kayıt tarihi
status (enum) - Durum (active, completed, dropped)
final_grade (decimal 3,2) - Final notu
grade_letter (varchar 2) - Harf notu (AA, BA, BB, CB, CC, DC, DD, FD, FF)
created_at (timestamp)
updated_at (timestamp)
```

### 4. grades (Notlar)

```sql
id (Primary Key)
student_id (Foreign Key) - students.id
course_id (Foreign Key) - courses.id
exam_type (enum) - Sınav tipi (midterm, final, quiz, homework, project)
grade (decimal 5,2) - Not
exam_date (date) - Sınav tarihi
weight (decimal 3,2) - Ağırlık yüzdesi
max_grade (decimal 5,2) - Maksimum puan
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

### 5. attendances (Devam)

```sql
id (Primary Key)
student_id (Foreign Key) - students.id
course_id (Foreign Key) - courses.id
attendance_date (date) - Devam tarihi
status (enum) - Durum (present, absent, late, excused)
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

## API Endpoint'leri

### Public Endpoints

```
GET /api/courses - Aktif ders listesi
GET /api/students/{student_number}/transcript - Transkript sorgula
GET /api/courses/{id}/info - Ders bilgileri
```

### Student Endpoints (JWT korumalı)

```
GET /api/student/profile - Öğrenci profili
GET /api/student/courses - Kayıtlı dersler
GET /api/student/grades - Tüm notlar
GET /api/student/grades/{course_id} - Ders notları
GET /api/student/attendance - Devam durumu
GET /api/student/transcript - Transkript
GET /api/student/gpa - Genel not ortalaması
```

### Teacher Endpoints (JWT korumalı)

```
GET /api/teacher/courses - Verdiğim dersler
GET /api/teacher/students/{course_id} - Ders öğrencileri
POST /api/teacher/grades - Not gir
PUT /api/teacher/grades/{id} - Not güncelle
POST /api/teacher/attendance - Devam al
GET /api/teacher/reports/{course_id} - Ders raporu
```

### Parent Endpoints (JWT korumalı)

```
GET /api/parent/children - Çocuklarım
GET /api/parent/child/{id}/grades - Çocuğun notları
GET /api/parent/child/{id}/attendance - Çocuğun devamı
GET /api/parent/child/{id}/report - Akademik rapor
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/students - Öğrenci ekle
PUT /api/admin/students/{id} - Öğrenci güncelle
POST /api/admin/courses - Ders ekle
PUT /api/admin/courses/{id} - Ders güncelle
POST /api/admin/enrollments - Ders kaydı
GET /api/admin/reports/class - Sınıf raporu
GET /api/admin/reports/course - Ders raporu
```

### Auth Endpoints

```
POST /api/auth/login - Giriş yap
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## Menü Yapısı

### Öğrenci Menü

- Ana Sayfa
- Notlarım
- Devam Durumum
- Transkriptim
- Not Ortalamam
- Profil

### Öğretmen Menü

- Kontrol Paneli
- Derslerim
- Not Girişi
- Devam Al
- Raporlar
- Profil

### Veli Menü

- Ana Sayfa
- Çocuklarım
- Akademik Durum
- Devam Takibi
- Bildirimler
- Profil

### Admin Menü

- Kontrol Paneli
- Öğrenci Yönetimi
- Ders Yönetimi
- Raporlar
- Sistem Ayarları
- Profil

## UI Yapısı (Quasar)

### 1. Öğrenci Ana Sayfa (/student)

```
+----------------------------------+
|  Hoşgeldiniz, Ahmet Yılmaz      |
+----------------------------------+
|  Genel Not Ortalaması: 3.45      |
|  Bu Dönem: 3.52                 |
+----------------------------------+
|  Son Sınavlar                   |
|  Matematik - Vize: 85            |
|  Fizik - Ödev: 92               |
|  Kimya - Quiz: 78                |
+----------------------------------+
|  Devam Durumu                    |
|  Bu Hafta: 5/5 ders             |
|  Genel: %95 devam               |
+----------------------------------+
|  Hızlı Erişim                   |
|  [Notlarım] [Transkript]        |
+----------------------------------+
```

### 2. Not Girişi - Öğretmen (/teacher/grades)

```
+----------------------------------+
|  Not Girişi - Matematik 101     |
+----------------------------------+
|  Sınav Tipi: [Vize Sınavı ▼]    |
|  Tarih: [16.12.2024]            |
|  Ağırlık: [%40]                 |
+----------------------------------+
|  Öğrenci Listesi                |
|  12345 - Ahmet Yılmaz [85___]    |
|  12346 - Ayşe Demir  [92___]    |
|  12347 - Mehmet Kaya [78___]    |
|  12348 - Fatma Şahin [___]      |
+----------------------------------+
|  [Kaydet] [Temizle] [İptal]     |
+----------------------------------+
```

### 3. Öğrenci Not Detayı (/student/grades/:course)

```
+----------------------------------+
|  Matematik 101 - Notlarım       |
+----------------------------------+
|  Sınav Geçmişi                  |
|  Vize 1    | 85  | %25 | 21.25  |
|  Ödev 1    | 92  | %10 | 9.20   |
|  Quiz 1    | 78  | %5  | 3.90   |
|  Vize 2    | --  | %25 | --     |
|  Final     | --  | %35 | --     |
+----------------------------------+
|  Mevcut Ortalama: 34.35/65      |
|  Harf Notu: Henüz Yok           |
+----------------------------------+
|  Devam Durumu: 12/14 (%86)      |
+----------------------------------+
```

### 4. Veli Panel (/parent)

```
+----------------------------------+
|  Veli Paneli - Ayşe Yılmaz      |
+----------------------------------+
|  Çocuklarım                     |
|  👦 Ahmet Yılmaz (9-A)        |
|  GNO: 3.45 | Devam: %95         |
|  [Detaylar] [Raporlar]          |
+----------------------------------+
|  👧 Zeynep Yılmaz (7-B)       |
|  GNO: 3.78 | Devam: %98         |
|  [Detaylar] [Raporlar]          |
+----------------------------------+
|  Son Bildirimler                 |
|  • Ahmet'in Matematik sınavı     |
|  • Zeynep'in devamsızlığı        |
+----------------------------------+
```

### 5. Admin Kontrol Paneli (/admin)

```
+----------------------------------+
|  Admin Panel - Sistem Yöneticisi |
+----------------------------------+
|  Genel İstatistikler              |
|  Toplam Öğrenci: 1,245          |
|  Aktif Ders: 87                 |
|  Öğretmen: 45                   |
|  Ortalama GNO: 2.78             |
+----------------------------------+
|  Bu Hafta                       |
|  Yeni Kayıt: 12                 |
|  Girilen Not: 234               |
|  Alınan Devam: 1,890            |
+----------------------------------+
|  Hızlı İşlemler                 |
|  [Öğrenci Ekle] [Ders Oluştur]   |
|  [Rapor Al] [Ayarlar]           |
+----------------------------------+
```

## Öğrenim Kazanımları

### Laravel API

- Complex grade calculations
- GPA computation algorithms
- Academic reporting
- Parent-child relationships
- Grade calculation algorithms
- Academic calendar integration
- Role-based access control
- Report generation
- Email notification system
- Data visualization for academic progress

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Not hesaplama: vıze %40, final %60
- Devam zorunluluğu %70
- Basit email bildirim sistemi
