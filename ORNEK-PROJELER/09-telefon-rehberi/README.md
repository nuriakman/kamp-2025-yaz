# 📞 Telefon Rehberi

## 📋 Proje Tanımı

Kurumsal telefon rehberi sistemi. Çalışanların iletişim bilgilerini, çalıştıkları birim, unvan ve servis bilgileri ile birlikte yöneten sistem.

## 🎯 Proje Hedefleri

- Çalışan iletişim bilgilerini listeleme
- Birim ve servislere göre filtreleme
- Arama fonksiyonu (ad, soyad, telefon)
- Admin paneli ile çalışan yönetimi
- Hiyerarşik organizasyon yapısı

## 🗄️ Veritabanı Yapısı

### 1. departments (Birimler)

```sql
id (Primary Key)
name (varchar 100) - Birim adı
code (varchar 10) - Birim kodu
description (text) - Birim açıklaması
manager_name (varchar 255) - Birim müdürü
location (varchar 255) - Birim konumu
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. services (Servisler)

```sql
id (Primary Key)
department_id (Foreign Key) - departments.id
name (varchar 100) - Servis adı
code (varchar 10) - Servis kodu
description (text) - Servis açıklaması
head_name (varchar 255) - Servis şefi
extension (varchar 10) - Dahili numara
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. positions (Unvanlar)

```sql
id (Primary Key)
title (varchar 100) - Unvan adı
level (integer) - Unvan seviyesi (1-10)
description (text) - Unvan açıklaması
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 4. employees (Çalışanlar)

```sql
id (Primary Key)
department_id (Foreign Key) - departments.id
service_id (Foreign Key) - services.id
position_id (Foreign Key) - positions.id
employee_number (varchar 20) - Personel numarası
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
mobile (varchar 20) - Cep telefonu
extension (varchar 10) - Dahili numara
office_location (varchar 255) - Ofis konumu
hire_date (date) - İşe giriş tarihi
is_active (boolean) - Aktif/pasif durumu
photo (varchar 255) - Profil fotoğrafı
created_at (timestamp)
updated_at (timestamp)
```

### 5. users (Kullanıcılar - Laravel varsayılan)

```sql
id (Primary Key)
name (varchar 255)
email (varchar 255)
password (varchar 255)
is_admin (boolean) - Admin yetkisi
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/employees - Çalışanları listele (sayfalama, arama)
GET /api/employees/{id} - Çalışan detayı
GET /api/departments - Birimleri listele
GET /api/departments/{id}/employees - Birime ait çalışanları listele
GET /api/services - Servisleri listele
GET /api/services/{id}/employees - Servise ait çalışanları listele
GET /api/positions - Unvanları listele
GET /api/search?q={query} - Genel arama (ad, soyad, telefon, email)
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/departments - Birim oluştur
PUT /api/admin/departments/{id} - Birim güncelle
DELETE /api/admin/departments/{id} - Birim sil

POST /api/admin/services - Servis oluştur
PUT /api/admin/services/{id} - Servis güncelle
DELETE /api/admin/services/{id} - Servis sil

POST /api/admin/positions - Unvan oluştur
PUT /api/admin/positions/{id} - Unvan güncelle
DELETE /api/admin/positions/{id} - Unvan sil

POST /api/admin/employees - Çalışan oluştur
PUT /api/admin/employees/{id} - Çalışan güncelle
DELETE /api/admin/employees/{id} - Çalışan sil
```

### Auth Endpoints

```
POST /api/auth/login - Admin girişi
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## 🧭 Menü Yapısı

### Ana Menü

- 🏠 Ana Sayfa
- 👥 Çalışanlar
- 🏢 Birimler
- 🔧 Servisler
- 🔍 Arama
- 👤 Giriş

### Admin Menü (Giriş yapıldıktan sonra)

- 📊 Dashboard
- 👥 Çalışan Yönetimi
- 🏢 Birim Yönetimi
- 🔧 Servis Yönetimi
- 🎯 Unvan Yönetimi
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Arama)    |
+----------------------------------+
|  Hero Section                    |
|  "Kurumsal Telefon Rehberi"      |
|  [Arama Kutusu]                  |
+----------------------------------+
|  Hızlı Erişim (Cards)            |
|  [Tüm Çalışanlar] [Birimler]     |
|  [Servisler] [Unvanlar]          |
+----------------------------------+
|  İstatistikler                   |
|  Toplam: 150 Çalışan             |
|  12 Birim, 45 Servis             |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Çalışan Listesi (/employees)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Filtreler                       |
|  Birim: [Dropdown] Servis: [Dropdown]|
|  Unvan: [Dropdown] [Filtrele]    |
+----------------------------------+
|  Çalışan Kartları (Grid)         |
|  👤 Ad Soyad                     |
|     Unvan - Birim                |
|     📞 0212 555 0123             |
|     📧 email@firma.com           |
|     [Detay]                      |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 3. Çalışan Detay (/employee/:id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Çalışan Profili (Card)          |
|  👤 Profil Fotoğrafı             |
|     Ad Soyad                     |
|     Unvan                        |
+----------------------------------+
|  İletişim Bilgileri (2 Kolon)    |
|  Sol: Telefon, Cep, Dahili       |
|  Sağ: E-posta, Ofis Konumu       |
+----------------------------------+
|  Organizasyon Bilgileri          |
|  Birim: İnsan Kaynakları         |
|  Servis: Bordro Servisi          |
|  İşe Giriş: 15.06.2020           |
+----------------------------------+
|  [Geri Dön] [Düzenle]            |
+----------------------------------+
```

### 4. Birimler (/departments)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Birimler                        |
+----------------------------------+
|  Birim Kartları                  |
|  🏢 İnsan Kaynakları             |
|     Müdür: Ahmet Yılmaz          |
|     Çalışan: 15 kişi             |
|     Konum: 3. Kat                |
|     [Çalışanları Gör]           |
+----------------------------------+
```

### 5. Arama Sonuçları (/search)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Arama: "ahmet" (5 sonuç)        |
+----------------------------------+
|  Sonuç Listesi                   |
|  👤 Ahmet Yılmaz                 |
|     Müdür - İnsan Kaynakları     |
|     📞 0212 555 0123             |
|  👤 Mehmet Ahmet                 |
|     Uzman - IT Departmanı        |
|     📞 0212 555 0124             |
+----------------------------------+
```

### 6. Admin Dashboard (/admin)

```
+----------------------------------+
|  Admin Header + Menü             |
+----------------------------------+
|  İstatistikler (Cards)           |
|  [Toplam Çalışan] [Birimler]     |
|  [Servisler] [Aktif Unvanlar]    |
+----------------------------------+
|  Son Eklenen Çalışanlar (Tablo)  |
|  Ad | Birim | Unvan | Tarih      |
+----------------------------------+
|  Birim Dağılımı (Chart)          |
+----------------------------------+
```

### 7. Çalışan Yönetimi (/admin/employees)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Çalışan] [Filtrele]     |
+----------------------------------+
|  Çalışan Tablosu                 |
|  Ad | Birim | Unvan | Telefon    |
|    | [Düzenle] [Sil]            |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 8. Çalışan Oluştur/Düzenle (/admin/employees/create)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Çalışan Formu (2 Kolon)         |
|  Sol: Kişisel Bilgiler           |
|       Ad, Soyad, E-posta         |
|  Sağ: Organizasyon Bilgileri     |
|       Birim, Servis, Unvan       |
+----------------------------------+
|  İletişim Bilgileri              |
|  Telefon, Cep, Dahili, Konum     |
+----------------------------------+
|  [Kaydet] [İptal]                |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Hierarchical model relationships
- ✅ Multiple foreign keys
- ✅ Advanced search queries
- ✅ Filtering and sorting
- ✅ Employee number generation
- ✅ Image upload handling
- ✅ Data validation

### Vue.js + Quasar

- ✅ Advanced filtering interface
- ✅ Search functionality
- ✅ Card-based layouts
- ✅ Profile components
- ✅ Hierarchical data display
- ✅ Image upload component
- ✅ Contact card design
- ✅ Organizational chart

### Genel Beceriler

- ✅ HR management system logic
- ✅ Organizational structure modeling
- ✅ Contact management
- ✅ Search and filter implementation
- ✅ Employee data handling
- ✅ Hierarchical data relationships

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Seeder'ları hazırla (örnek veriler)
4. Controller'ları oluştur
5. Search functionality implement et
6. Image upload ekle

### 2. Frontend (Quasar)

1. Employee list ve detail component'leri
2. Search ve filter component'leri
3. Department ve service component'leri
4. Admin panel component'leri
5. Profile card component'i
6. Image upload component'i

### 3. Test ve Optimizasyon

1. Search functionality'yi test et
2. Filter kombinasyonlarını test et
3. Admin CRUD işlemlerini test et
4. Performance optimizasyonu

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Profil fotoğrafı upload'ı basit tutulacak
- Organizasyon şeması opsiyonel
- Export functionality (Excel, PDF) dahil edilmeyecek
- Bulk import özelliği dahil edilmeyecek
