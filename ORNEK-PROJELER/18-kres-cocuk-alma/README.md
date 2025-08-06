# 🏠 Kreş Çocuk Alma Sistemi

## 📋 Proje Tanımı

Kreşlerde velilerin çocuklarını güvenli bir şekilde teslim alabilmesi için geliştirilmiş dijital takip ve onay sistemi.

## 🎯 Proje Hedefleri

- Veli kimlik doğrulama sistemi
- Çocuk teslim alma süreci yönetimi
- Görevli bildirim sistemi
- Güvenlik kayit tutma
- Mobil uyumlu arayüz

## 🗺️ Veritabanı Yapısı

### 1. parents (Veliler)

```sql
id (Primary Key)
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
phone (varchar 20) - Telefon
email (varchar 255) - E-posta
pin_code (varchar 6) - PIN kodu
photo (varchar 255) - Veli fotoğrafı
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. children (Çocuklar)

```sql
id (Primary Key)
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
birth_date (date) - Doğum tarihi
class_name (varchar 50) - Sınıf adı
photo (varchar 255) - Çocuk fotoğrafı
special_notes (text) - Özel notlar (alerji, vb.)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. parent_child (Veli-Çocuk İlişkisi)

```sql
id (Primary Key)
parent_id (Foreign Key) - parents.id
child_id (Foreign Key) - children.id
relationship (enum) - Yakınlık (mother, father, guardian)
is_authorized (boolean) - Alma yetkisi
created_at (timestamp)
updated_at (timestamp)
```

### 4. staff (Görevliler)

```sql
id (Primary Key)
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
role (enum) - Rol (teacher, admin, security)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 5. pickup_requests (Alma Talepleri)

```sql
id (Primary Key)
parent_id (Foreign Key) - parents.id
child_id (Foreign Key) - children.id
request_code (varchar 10) - Talep kodu
status (enum) - Durum (pending, preparing, ready, completed, cancelled)
requested_at (timestamp) - Talep zamanı
prepared_by (Foreign Key) - staff.id (hazırlayan görevli)
prepared_at (timestamp) - Hazırlama zamanı
completed_at (timestamp) - Teslim zamanı
notes (text) - Notlar
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Parent Endpoints

```
POST /api/parent/login - Veli girişi (telefon + PIN)
GET /api/parent/children - Veli çocukları
POST /api/parent/pickup-request - Çocuk alma talebi
GET /api/parent/pickup-status/{code} - Talep durumu sorgula
```

### Staff Endpoints (JWT korumalı)

```
GET /api/staff/pending-requests - Bekleyen talepler
PUT /api/staff/requests/{id}/prepare - Çocuğu hazırla
PUT /api/staff/requests/{id}/complete - Teslimi tamamla
GET /api/staff/children - Çocuk listesi
GET /api/staff/parents - Veli listesi
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/parents - Veli ekle
PUT /api/admin/parents/{id} - Veli güncelle
POST /api/admin/children - Çocuk ekle
PUT /api/admin/children/{id} - Çocuk güncelle
POST /api/admin/parent-child - Veli-çocuk ilişkisi
GET /api/admin/reports - Raporlar
```

### Auth Endpoints

```
POST /api/auth/staff-login - Görevli girişi
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## 🧭 Menü Yapısı

### Veli Menü

- 🏠 Ana Sayfa
- 👶 Çocuklarım
- 📱 Çocuk Al
- 🔍 Durum Sorgula
- 📞 İletişim

### Görevli Menü

- 📈 Dashboard
- 📝 Bekleyen Talepler
- 👶 Çocuk Listesi
- 👥 Veli Listesi
- 👤 Profil

### Admin Menü

- 📈 Dashboard
- 👶 Çocuk Yönetimi
- 👥 Veli Yönetimi
- 👨‍🏫 Görevli Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Veli Giriş Sayfası (/parent-login)

```
+----------------------------------+
|  Kreş Logo                      |
+----------------------------------+
|  Veli Girişi                     |
+----------------------------------+
|  Telefon: [0555 123 4567]       |
|  PIN Kod: [••••••]              |
+----------------------------------+
|  [Giriş Yap]                    |
+----------------------------------+
|  Yardım için: 0212 555 0123     |
+----------------------------------+
```

### 2. Veli Ana Sayfa (/parent-dashboard)

```
+----------------------------------+
|  Hoşgeldiniz, Ayşe Hanım        |
+----------------------------------+
|  Çocuklarınız                    |
+----------------------------------+
|  👶 Ahmet Yılmaz (4 yaş)       |
|     Sınıf: Papatya Sınıfı        |
|     [Çocuk Al] [Detay]           |
+----------------------------------+
|  👧 Zeynep Yılmaz (6 yaş)      |
|     Sınıf: Lale Sınıfı           |
|     [Çocuk Al] [Detay]           |
+----------------------------------+
|  Son İşlemler                    |
|  ✅ Ahmet - Bugün 16:30 alındı    |
+----------------------------------+
```

### 3. Çocuk Alma Talebi (/pickup-request)

```
+----------------------------------+
|  Çocuk Alma Talebi                |
+----------------------------------+
|  Çocuk Seçimi                     |
|  ● Ahmet Yılmaz                  |
|  ○ Zeynep Yılmaz                 |
+----------------------------------+
|  Talep Detayları                  |
|  Zaman: Şimdi                     |
|  Notlar: [Textarea]              |
+----------------------------------+
|  [Talep Gönder]                  |
+----------------------------------+
```

### 4. Talep Durumu (/pickup-status)

```
+----------------------------------+
|  Talep Durumu                    |
+----------------------------------+
|  Talep Kodu: PKP-2024-001       |
|  Çocuk: Ahmet Yılmaz              |
+----------------------------------+
|  Durum: 🟡 Hazırlanıyor          |
|  ✅ Talep Alındı (16:25)          |
|  🟡 Hazırlanıyor (16:27)        |
|  ⏳ Teslime Hazır                 |
|  ⏳ Teslim Edildi                |
+----------------------------------+
|  Tahmini Süre: 5 dakika          |
|  [Yenile] [Ana Sayfa]            |
+----------------------------------+
```

### 5. Görevli Dashboard (/staff-dashboard)

```
+----------------------------------+
|  Görevli Panel - Ayşe Öğretmen   |
+----------------------------------+
|  Bekleyen Talepler (3)           |
+----------------------------------+
|  🟡 PKP-001 | Ahmet | 16:25     |
|     [Hazırla] [Detay]            |
|  🟡 PKP-002 | Zeynep | 16:28    |
|     [Hazırla] [Detay]            |
+----------------------------------+
|  Hazır Bekleyenler (1)            |
|  🟢 PKP-003 | Mehmet | 16:20    |
|     [Teslim Et] [Detay]          |
+----------------------------------+
|  Bugünkü İstatistikler           |
|  Toplam: 15 | Tamamlanan: 12    |
+----------------------------------+
```

### 6. Çocuk Hazırlama (/staff/prepare/:id)

```
+----------------------------------+
|  Çocuk Hazırlama                  |
+----------------------------------+
|  Talep: PKP-2024-001            |
|  Çocuk: Ahmet Yılmaz              |
|  Veli: Ayşe Yılmaz               |
+----------------------------------+
|  Çocuk Bilgileri                  |
|  📷 [Fotoğraf]                  |
|  Yaş: 4                          |
|  Sınıf: Papatya Sınıfı            |
|  Özel Notlar: Alerji yok         |
+----------------------------------+
|  Eşyaları Kontrol Et             |
|  ☑ Çanta                         |
|  ☑ Mont                          |
|  ☐ Oyuncak                       |
+----------------------------------+
|  [Hazır] [İptal]                 |
+----------------------------------+
```

### 7. Admin - Çocuk Yönetimi (/admin/children)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Çocuk] [Filtrele]        |
+----------------------------------+
|  Çocuk Tablosu                   |
|  Ad | Sınıf | Veli | Durum        |
|    | [Düzenle] [Sil]             |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ PIN-based authentication
- ✅ Many-to-many relationships
- ✅ Status workflow management
- ✅ Real-time notifications
- ✅ File upload (photos)
- ✅ Security logging
- ✅ Role-based permissions

### Vue.js + Quasar

- ✅ Mobile-first design
- ✅ Real-time status updates
- ✅ Camera integration
- ✅ Push notifications
- ✅ Offline capability
- ✅ Touch-friendly interface
- ✅ Progressive Web App (PWA)

### Genel Beceriler

- ✅ Child safety systems
- ✅ Parent verification
- ✅ Staff workflow management
- ✅ Security protocols
- ✅ Mobile UX design
- ✅ Emergency procedures

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. PIN authentication system
4. Status workflow logic
5. Notification system
6. Security logging

### 2. Frontend (Quasar)

1. Mobile-responsive layout
2. Parent login interface
3. Staff dashboard
4. Real-time status updates
5. Camera integration
6. PWA configuration

### 3. Test ve Optimizasyon

1. Security flow'unu test et
2. Mobile usability test et
3. Real-time updates test et
4. Emergency scenarios test et

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- PIN kodu 6 haneli olacak
- Fotoğraf yükleme zorunlu
- Real-time updates için WebSocket kullanılacak
- Mobile-first yaklaşımla tasarlanacak
- Güvenlik logları detaylı tutulacak
