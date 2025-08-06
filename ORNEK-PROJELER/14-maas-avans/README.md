# 💰 Maaş ve Avans Takip Sistemi

## 📋 Proje Tanımı

Çalışan maaş hesaplama, avans talepleri ve bordro yönetim sistemi. İK departmanı maaş hesaplamaları yapabilir, çalışanlar avans talep edebilir ve bordro süreçleri yönetilebilir.

## 🎯 Proje Hedefleri

- Maaş hesaplama ve bordro oluşturma sistemi
- Avans talep, onay ve takip süreci
- Vergi ve kesinti hesaplamaları otomasyonu
- Ödeme takip ve raporlama sistemi
- Çalışan öz-servis portalı

## 🗺️ Veritabanı Yapısı

### 1. employees (Çalışanlar)

```sql
id (Primary Key)
employee_number (varchar 20) - Çalışan numarası
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
hire_date (date) - İşe başlama tarihi
department (varchar 100) - Departman
position (varchar 100) - Pozisyon
base_salary (decimal 10,2) - Temel maaş
tax_number (varchar 20) - Vergi numarası
social_security_number (varchar 20) - SGK numarası
bank_account (varchar 30) - Banka hesap numarası
bank_name (varchar 100) - Banka adı
marital_status (enum) - Medeni durum (single, married)
children_count (integer) - Çocuk sayısı
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. salary_components (Maaş Bileşenleri)

```sql
id (Primary Key)
name (varchar 100) - Bileşen adı
type (enum) - Tip (earning, deduction, tax)
calculation_type (enum) - Hesaplama tipi (fixed, percentage, formula)
amount (decimal 10,2) - Tutar
percentage (decimal 5,2) - Yüzde
formula (text) - Formül
is_taxable (boolean) - Vergiye tabi mi
is_mandatory (boolean) - Zorunlu mu
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. payrolls (Bordrolar)

```sql
id (Primary Key)
employee_id (Foreign Key) - employees.id
period_year (integer) - Dönem yılı
period_month (integer) - Dönem ayı
work_days (integer) - Çalışılan gün
overtime_hours (decimal 5,2) - Mesai saati
gross_salary (decimal 10,2) - Brüt maaş
total_earnings (decimal 10,2) - Toplam kazanc
total_deductions (decimal 10,2) - Toplam kesintiler
income_tax (decimal 10,2) - Gelir vergisi
social_security_employee (decimal 10,2) - SGK çalışan payı
unemployment_insurance (decimal 10,2) - İşsizlik sigortası
net_salary (decimal 10,2) - Net maaş
advance_deduction (decimal 10,2) - Avans kesintisi
status (enum) - Durum (draft, approved, paid)
approved_by (integer) - Onaylayan kullanıcı ID
approved_at (timestamp) - Onay tarihi
paid_at (timestamp) - Ödeme tarihi
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

### 4. advances (Avanslar)

```sql
id (Primary Key)
employee_id (Foreign Key) - employees.id
request_number (varchar 20) - Talep numarası
amount (decimal 10,2) - Talep edilen tutar
reason (text) - Talep nedeni
request_date (date) - Talep tarihi
status (enum) - Durum (pending, approved, rejected, paid, completed)
approved_by (integer) - Onaylayan kullanıcı ID
approved_at (timestamp) - Onay tarihi
rejection_reason (text) - Red nedeni
installment_count (integer) - Taksit sayısı
monthly_deduction (decimal 10,2) - Aylık kesinti
remaining_amount (decimal 10,2) - Kalan tutar
paid_at (timestamp) - Ödeme tarihi
completed_at (timestamp) - Tamamlanma tarihi
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Employee Endpoints (JWT korumalı)

```
GET /api/employee/profile - Çalışan profili
GET /api/employee/payrolls - Bordro geçmişi
GET /api/employee/payrolls/{id} - Bordro detayı
GET /api/employee/advances - Avans geçmişi
POST /api/employee/advances - Avans talebi
GET /api/employee/advances/{id} - Avans detayı
GET /api/employee/salary-slip/{id} - Maaş bordrosu PDF
```

### HR Endpoints (JWT korumalı)

```
GET /api/hr/employees - Çalışan listesi
GET /api/hr/employees/{id} - Çalışan detayı
PUT /api/hr/employees/{id} - Çalışan güncelle
GET /api/hr/payrolls - Bordro listesi
POST /api/hr/payrolls/generate - Bordro oluştur
PUT /api/hr/payrolls/{id}/approve - Bordro onayla
GET /api/hr/advances/pending - Bekleyen avanslar
PUT /api/hr/advances/{id}/approve - Avans onayla
PUT /api/hr/advances/{id}/reject - Avans reddet
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/employees - Çalışan ekle
PUT /api/admin/employees/{id} - Çalışan güncelle
POST /api/admin/salary-components - Maaş bileşeni ekle
PUT /api/admin/salary-components/{id} - Bileşen güncelle
GET /api/admin/reports/payroll - Bordro raporu
GET /api/admin/reports/advance - Avans raporu
GET /api/admin/reports/tax - Vergi raporu
POST /api/admin/bulk-payroll - Toplu bordro
```

## 🧭 Menü Yapısı

### Çalışan Menü

- 🏠 Ana Sayfa
- 💰 Maaş Bordrolarım
- 💳 Avans Taleplerim
- 📝 Yeni Avans Talebi
- 📄 Belgelerim
- 👤 Profil

### İK Menü

- 📈 Kontrol Paneli
- 👥 Çalışan Yönetimi
- 💰 Bordro Yönetimi
- 💳 Avans Yönetimi
- 📄 Raporlar
- 👤 Profil

### Admin Menü

- 📈 Kontrol Paneli
- 👥 Çalışan Yönetimi
- 💰 Maaş Bileşenleri
- 📄 Raporlar
- ⚙️ Sistem Ayarları
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Çalışan Ana Sayfa (/employee)

```
+----------------------------------+
|  Hoşgeldiniz, Ahmet Yılmaz      |
+----------------------------------+
|  Son Bordro (Kasım 2024)         |
+----------------------------------+
|  Brüt Maaş: 15,000.00 TL        |
|  Kesintiler: -3,250.00 TL        |
|  Net Maaş: 11,750.00 TL         |
|  [Bordroyu Gör] [PDF İndir]      |
+----------------------------------+
|  Aktif Avanslar (1)              |
|  Avans: 5,000 TL                |
|  Kalan: 3,000 TL (6 taksit)     |
|  [Detaylar]                     |
+----------------------------------+
|  Hızlı İşlemler                 |
|  [💳 Avans Talep Et]          |
|  [📄 Belgelerim]               |
+----------------------------------+
```

### 2. Bordro Detayı (/employee/payrolls/:id)

```
+----------------------------------+
|  Bordro Detayı - Kasım 2024     |
+----------------------------------+
|  Kazanclar                       |
|  Temel Maaş: 15,000.00 TL       |
|  Mesai: 500.00 TL               |
|  Toplam Brüt: 15,500.00 TL      |
+----------------------------------+
|  Kesintiler                      |
|  Gelir Vergisi: 1,550.00 TL     |
|  SGK Çalışan: 2,170.00 TL       |
|  İşsizlik Sig.: 155.00 TL       |
|  Avans Kesinti: 500.00 TL       |
|  Toplam Kesinti: 4,375.00 TL    |
+----------------------------------+
|  Net Maaş: 11,125.00 TL         |
|  [💾 PDF İndir] [📧 E-posta]  |
+----------------------------------+
```

### 3. Avans Talebi (/employee/advance/new)

```
+----------------------------------+
|  Yeni Avans Talebi               |
+----------------------------------+
|  Talep Bilgileri                 |
|  Tutar: [5000.00 TL]            |
|  Taksit Sayısı: [6 ay ▼]       |
|  Aylık Kesinti: 833.33 TL       |
+----------------------------------+
|  Talep Nedeni                    |
|  [Acil sağlık gideri]           |
+----------------------------------+
|  Maaş Durumu                    |
|  Mevcut Net: 11,750 TL          |
|  Kesinti Sonrası: 10,917 TL     |
|  Uygun Tutar: ✅ 5,000 TL        |
+----------------------------------+
|  [Talep Gönder] [İptal]         |
+----------------------------------+
```

### 4. İK Kontrol Paneli (/hr)

```
+----------------------------------+
|  İK Panel - Ayşe Yıldırım       |
+----------------------------------+
|  Bu Ay Özet (Aralık 2024)       |
+----------------------------------+
|  Toplam Çalışan: 45            |
|  Hazırlanan Bordro: 42/45       |
|  Bekleyen Avans: 8               |
|  Toplam Maaş: 675,000 TL        |
+----------------------------------+
|  Bekleyen İşlemler               |
|  💰 3 Bordro Onayı Bekliyor    |
|  💳 8 Avans Onayı Bekliyor     |
|  [Bordro Yönetimi] [Avans Yönetimi]|
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Complex financial calculations
- ✅ Tax and deduction algorithms
- ✅ PDF generation (TCPDF/DomPDF)
- ✅ Approval workflow systems
- ✅ Financial reporting
- ✅ Data validation and security
- ✅ Scheduled tasks for payroll

### Vue.js + Quasar

- ✅ Financial data visualization
- ✅ Complex form handling
- ✅ PDF viewer integration
- ✅ Chart.js for reports
- ✅ Role-based interfaces
- ✅ Responsive design
- ✅ Print-friendly layouts

### Genel Beceriler

- ✅ Payroll system design
- ✅ Financial calculations
- ✅ HR process automation
- ✅ Compliance and regulations
- ✅ Security best practices
- ✅ Audit trail implementation

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Salary calculation engine
4. Tax calculation algorithms
5. PDF generation system
6. Approval workflow

### 2. Frontend (Quasar)

1. Çalışan self-servis portalı
2. İK yönetim arayüzü
3. Bordro hesaplama formları
4. Avans talep sistemi
5. Raporlama kontrol paneli
6. PDF görüntüleyici entegrasyonu

### 3. Test ve Optimizasyon

1. Hesaplama doğruluğu testleri
2. Güvenlik testleri
3. Performans optimizasyonu
4. Uyumluluk doğrulaması

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Türkiye vergi mevzuatına uygun hesaplamalar
- PDF oluşturma için TCPDF kullanılacak
- Güvenlik önlemleri kritik önemde
- Audit trail tüm işlemler için tutulacak
- Backup ve recovery planları gerekli
