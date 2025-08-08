# 💼 Fiyat Teklifi Sistemi

## 📋 Proje Tanımı

Küçük işletmelerin müşteri taleplerine göre profesyonel fiyat teklifleri hazırlayabileceği, hizmet katalogu yönetebileceği ve teklif süreçlerini takip edebileceği sistem.

## 🎯 Proje Hedefleri

- Müşteri talep formu yönetimi
- Hizmet/ürün katalogu oluşturma
- Otomatik fiyat hesaplama
- Profesyonel teklif PDF oluşturma
- Teklif durumu takibi

## 🗺️ Veritabanı Yapısı

### 1. customers (Müşteriler)

```sql
id (Primary Key)
company_name (varchar 255) - Şirket adı
contact_person (varchar 255) - İletişim kişisi
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
address (text) - Adres
tax_number (varchar 20) - Vergi numarası
created_at (timestamp)
updated_at (timestamp)
```

### 2. service_categories (Hizmet Kategorileri)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
description (text) - Kategori açıklaması
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. services (Hizmetler)

```sql
id (Primary Key)
category_id (Foreign Key) - service_categories.id
name (varchar 255) - Hizmet adı
description (text) - Hizmet açıklaması
unit_price (decimal 10,2) - Birim fiyat
unit (varchar 50) - Birim (saat, adet, m2, vb.)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 4. quotes (Teklifler)

```sql
id (Primary Key)
customer_id (Foreign Key) - customers.id
quote_number (varchar 50) - Teklif numarası
title (varchar 255) - Teklif başlığı
description (text) - Teklif açıklaması
subtotal (decimal 10,2) - Ara toplam
tax_rate (decimal 5,2) - Vergi oranı (%)
tax_amount (decimal 10,2) - Vergi tutarı
total_amount (decimal 10,2) - Toplam tutar
status (enum) - Durum (draft, sent, accepted, rejected, expired)
valid_until (date) - Geçerlilik tarihi
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

### 5. quote_items (Teklif Kalemleri)

```sql
id (Primary Key)
quote_id (Foreign Key) - quotes.id
service_id (Foreign Key) - services.id
service_name (varchar 255) - Hizmet adı (teklif anındaki)
quantity (decimal 8,2) - Miktar
unit_price (decimal 10,2) - Birim fiyat
subtotal (decimal 10,2) - Ara toplam
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/services - Hizmet listesi
GET /api/service-categories - Hizmet kategorileri
POST /api/quote-requests - Teklif talebi oluştur
```

### Business Endpoints (JWT korumalı)

```
GET /api/customers - Müşteri listesi
POST /api/customers - Müşteri oluştur
PUT /api/customers/{id} - Müşteri güncelle

GET /api/quotes - Teklif listesi
POST /api/quotes - Teklif oluştur
PUT /api/quotes/{id} - Teklif güncelle
GET /api/quotes/{id}/pdf - Teklif PDF indir
PUT /api/quotes/{id}/status - Teklif durumu güncelle

POST /api/services - Hizmet oluştur
PUT /api/services/{id} - Hizmet güncelle
DELETE /api/services/{id} - Hizmet sil
```

### Auth Endpoints

```
POST /api/auth/login - Giriş yap
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## 🧭 Menü Yapısı

### Ana Menü

- 🏠 Ana Sayfa
- 📋 Teklif Talep Et
- 📞 İletişim
- 👤 Giriş

### Business Menü (Giriş yapıldıktan sonra)

- 📈 Dashboard
- 📄 Teklif Yönetimi
- 👥 Müşteri Yönetimi
- 🛠️ Hizmet Yönetimi
- 📁 Kategori Yönetimi
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü)            |
+----------------------------------+
|  Hero Section                    |
|  "Profesyonel Teklif Hazırlayın" |
|  [Teklif Talep Et]               |
+----------------------------------+
|  Hizmet Kategorileri (Grid)      |
|  💻 Web Tasarım    📱 Mobil      |
|  📦 E-Ticaret    🎨 Grafik      |
+----------------------------------+
|  Özellikler                      |
|  - Hızlı Teklif Hazırlama         |
|  - Profesyonel PDF               |
|  - Müşteri Takibi                |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Teklif Talep Formu (/quote-request)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Teklif Talep Formu              |
+----------------------------------+
|  Şirket Bilgileri                |
|  Şirket Adı: [________________]  |
|  İletişim Kişisi: [____________]  |
|  E-posta: [________________]     |
|  Telefon: [________________]     |
+----------------------------------+
|  Hizmet Seçimi                   |
|  ☑ Web Sitesi Tasarımı           |
|  ☐ E-Ticaret Sitesi             |
|  ☐ Mobil Uygulama               |
+----------------------------------+
|  Proje Detayları                 |
|  [Textarea - Proje açıklaması]   |
+----------------------------------+
|  [Teklif Talep Et]               |
+----------------------------------+
```

### 3. Business Dashboard (/dashboard)

```
+----------------------------------+
|  Business Header + Menü          |
+----------------------------------+
|  İstatistikler (Cards)           |
|  [Toplam Teklif] [Bekleyen]      |
|  [Kabul Edilen] [Aylık Ciro]     |
+----------------------------------+
|  Son Teklifler (Tablo)           |
|  No | Müşteri | Tutar | Durum     |
+----------------------------------+
|  Aylık Performans (Chart)        |
+----------------------------------+
```

### 4. Teklif Oluştur (/quotes/create)

```
+----------------------------------+
|  Business Header                 |
+----------------------------------+
|  Teklif Oluştur                  |
+----------------------------------+
|  Müşteri Seçimi                  |
|  Müşteri: [Dropdown] [+ Yeni]     |
+----------------------------------+
|  Teklif Bilgileri                |
|  Başlık: [________________]     |
|  Açıklama: [Textarea]            |
|  Geçerlilik: [Date Picker]       |
+----------------------------------+
|  Hizmet Kalemleri                |
|  Hizmet | Miktar | Birim | Toplam |
|  [+ Kalem Ekle]                 |
+----------------------------------+
|  Toplam: ₺15.750,00              |
|  [Taslak Kaydet] [Teklif Gönder] |
+----------------------------------+
```

### 5. Teklif Listesi (/quotes)

```
+----------------------------------+
|  Business Header                 |
+----------------------------------+
|  [+ Yeni Teklif] [Filtrele]      |
+----------------------------------+
|  Teklif Tablosu                  |
|  No | Müşteri | Tutar | Durum     |
|     | [Görüntüle] [PDF] [Düzenle] |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 6. Teklif PDF Görüntüle (/quotes/:id/pdf)

```
+----------------------------------+
|  ŞİRKET LOGO                     |
|                                  |
|  FİYAT TEKLİFİ                    |
|  Teklif No: QUO-2024-001         |
|  Tarih: 15.06.2024               |
+----------------------------------+
|  Müşteri Bilgileri               |
|  ABC Şirketi                     |
|  Ahmet Yılmaz                    |
|  ahmet@abc.com                   |
+----------------------------------+
|  Hizmet Detayları                |
|  Sıra | Hizmet | Miktar | Tutar   |
|  1    | Web Tasarım | 1 | 5.000   |
|  2    | SEO | 6 Ay | 3.000       |
+----------------------------------+
|  Ara Toplam: 8.000,00 ₺         |
|  KDV (%20): 1.600,00 ₺           |
|  TOPLAM: 9.600,00 ₺              |
+----------------------------------+
|  [Yazdır] [İndir]                |
+----------------------------------+
```

### 7. Hizmet Yönetimi (/services)

```
+----------------------------------+
|  Business Header                 |
+----------------------------------+
|  [+ Yeni Hizmet] [Filtrele]      |
+----------------------------------+
|  Hizmet Tablosu                  |
|  Ad | Kategori | Birim Fiyat      |
|    | [Düzenle] [Sil]             |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Karmaşık iş akışı yönetimi
- ✅ PDF oluşturma (DomPDF/TCPDF)
- ✅ Teklif numarası oluşturma
- ✅ Vergi hesaplama mantığı
- ✅ Durum iş akışı yönetimi
- ✅ Mali hesaplamalar
- ✅ Email notification system

### Vue.js + Quasar

- ✅ Dinamik form oluşturma
- ✅ PDF görüntüleyici entegrasyonu
- ✅ Yazdırma işlevi
- ✅ Chart.js analitik için
- ✅ Gelişmiş tablo operasyonları
- ✅ Çok aşamalı form sihirbazı
- ✅ Para birimi biçimi
- ✅ Tarih seçici entegrasyonu

### Genel Beceriler

- ✅ İş akışı otomasyonu
- ✅ Teklif oluşturma iş akışı
- ✅ Müşteri ilişkileri yönetimi
- ✅ Mali belge oluşturma
- ✅ Hizmet kataloğu yönetimi
- ✅ İş analitiği

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Quote number generation logic
4. PDF generation service
5. Email notification system
6. Tax calculation methods

### 2. Frontend (Quasar)

1. Business dashboard component'i
2. Quote builder component'i
3. PDF viewer component'i
4. Customer management interface
5. Service catalog interface
6. Analytics dashboard

### 3. Test ve Optimizasyon

1. Quote generation flow'unu test et
2. PDF creation'u test et
3. Email notification'ları test et
4. Financial calculation'ları test et

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- PDF generation için Laravel DomPDF kullanılacak
- Vergi hesaplaması basit (%20 KDV) olacak
- Email template'leri basit HTML olacak
- Chart'lar için Chart.js kullanılacak
