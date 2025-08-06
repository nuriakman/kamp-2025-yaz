# 📋 Anket Programı

## 📋 Proje Tanımı

Online anket oluşturma, paylaşma ve sonuçlarını analiz etme sistemi. Kullanıcılar çoktan seçmeli ve açık uçlu sorularla anket oluşturabilir, paylaşabilir ve sonuçlarını analiz edebilir.

## 🎯 Proje Hedefleri

- Anket oluşturma ve düzenleme sistemi
- Çoktan seçmeli ve açık uçlu soru tipleri
- Anket paylaşımı ve cevaplama sistemi
- Sonuç analizi ve grafik raporlama
- Admin paneli ile anket yönetimi

## 🗺️ Veritabanı Yapısı

### 1. surveys (Anketler)

```sql
id (Primary Key)
title (varchar 200) - Anket başlığı
description (text) - Anket açıklaması
start_date (timestamp) - Başlangıç tarihi
end_date (timestamp) - Bitiş tarihi
is_active (boolean) - Aktif/pasif durumu
is_public (boolean) - Herkese açık mı
allow_anonymous (boolean) - Anonim cevaba izin ver
max_responses (integer) - Maksimum cevap sayısı
response_count (integer) - Mevcut cevap sayısı
created_by (integer) - Oluşturan kullanıcı ID
created_at (timestamp)
updated_at (timestamp)
```

### 2. questions (Sorular)

```sql
id (Primary Key)
survey_id (Foreign Key) - surveys.id
question_text (text) - Soru metni
question_type (enum) - Soru tipi (multiple_choice, single_choice, text, textarea, rating, yes_no)
is_required (boolean) - Zorunlu mu
sort_order (integer) - Sıralama
help_text (text) - Yardım metni
created_at (timestamp)
updated_at (timestamp)
```

### 3. question_options (Soru Seçenekleri)

```sql
id (Primary Key)
question_id (Foreign Key) - questions.id
option_text (varchar 255) - Seçenek metni
sort_order (integer) - Sıralama
is_other (boolean) - "Diğer" seçeneği mi
created_at (timestamp)
updated_at (timestamp)
```

### 4. responses (Cevaplar)

```sql
id (Primary Key)
survey_id (Foreign Key) - surveys.id
respondent_name (varchar 100) - Cevaplayıcı adı
respondent_email (varchar 255) - Cevaplayıcı e-posta
submitted_at (timestamp) - Gönderim tarihi
ip_address (varchar 45) - IP adresi
user_agent (text) - Tarayıcı bilgisi
is_complete (boolean) - Tamamlandı mı
created_at (timestamp)
updated_at (timestamp)
```

### 5. answers (Yanıtlar)

```sql
id (Primary Key)
response_id (Foreign Key) - responses.id
question_id (Foreign Key) - questions.id
option_id (Foreign Key) - question_options.id (nullable)
answer_text (text) - Metin yanıtı
rating_value (integer) - Puanlama değeri
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/surveys/{id} - Anket detayı ve soruları
POST /api/surveys/{id}/responses - Anket cevapla
GET /api/surveys/{id}/results - Anket sonuçları (public ise)
GET /api/surveys/public - Herkese açık anketler
```

### User Endpoints (JWT korumalı)

```
POST /api/surveys - Anket oluştur
PUT /api/surveys/{id} - Anket güncelle
DELETE /api/surveys/{id} - Anket sil
GET /api/user/surveys - Anketlerim
POST /api/surveys/{id}/questions - Soru ekle
PUT /api/questions/{id} - Soru güncelle
DELETE /api/questions/{id} - Soru sil
GET /api/surveys/{id}/analytics - Anket analizi
```

### Admin Endpoints (JWT korumalı)

```
GET /api/admin/surveys - Tüm anketler
GET /api/admin/responses - Tüm cevaplar
GET /api/admin/analytics - Genel istatistikler
PUT /api/admin/surveys/{id}/status - Anket durumu güncelle
DELETE /api/admin/surveys/{id} - Anket sil (admin)
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
- 📋 Anketler
- 📊 Sonuçlar
- 📝 Anket Oluştur
- 👤 Giriş/Kayıt

### Kullanıcı Menü (Giriş sonrası)

- 🏠 Ana Sayfa
- 📋 Anketlerim
- 📊 Sonuçlarım
- 📝 Yeni Anket
- 👤 Profil

### Admin Menü

- 📈 Kontrol Paneli
- 📋 Anket Yönetimi
- 👥 Kullanıcı Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Giriş)    |
+----------------------------------+
|  Hero Section                    |
|  "Kolay Anket Oluşturma"        |
|  [Anket Oluştur] [Anketleri Gör]|
+----------------------------------+
|  Popüler Anketler                |
|  📋 Müşteri Memnuniyeti (245 cevap)|
|  📋 Ürün Değerlendirme (189 cevap) |
+----------------------------------+
|  Özellikler                      |
|  ✅ Kolay anket oluşturma        |
|  ✅ Canlı sonuç takibi          |
|  ✅ Grafik raporlar              |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Anket Oluşturma (/surveys/create)

```
+----------------------------------+
|  Yeni Anket Oluştur              |
+----------------------------------+
|  Anket Bilgileri                 |
|  Başlık: [_______________]      |
|  Açıklama: [_______________]    |
|  Başlangıç: [16.12.2024]        |
|  Bitiş: [31.12.2024]            |
+----------------------------------+
|  Sorular (2/5)                  |
|  1. Yaşınız nedir?               |
|     Tip: [Açık uçlu ▼]         |
|     [Sil] [Düzenle]              |
+----------------------------------+
|  2. Hangi şehirde yaşıyorsunuz?  |
|     Tip: [Çoktan seçmeli ▼]     |
|     ○ Ankara ○ İstanbul ○ İzmir |
|     [Sil] [Düzenle]              |
+----------------------------------+
|  [+ Soru Ekle] [Kaydet] [İptal] |
+----------------------------------+
```

### 3. Anket Cevaplama (/surveys/:id/respond)

```
+----------------------------------+
|  Müşteri Memnuniyeti Anketi      |
+----------------------------------+
|  Soru 1/5                       |
|  Yaşınız nedir?                 |
|  [25____________]               |
+----------------------------------+
|  Soru 2/5                       |
|  Hangi şehirde yaşıyorsunuz?    |
|  ● Ankara                       |
|  ○ İstanbul                     |
|  ○ İzmir                        |
|  ○ Diğer: [_______]            |
+----------------------------------+
|  [Önceki] [Sonraki] [Gönder]    |
|  ●●○○○ (2/5 tamamlandı)        |
+----------------------------------+
```

### 4. Anket Sonuçları (/surveys/:id/results)

```
+----------------------------------+
|  Müşteri Memnuniyeti - Sonuçlar  |
+----------------------------------+
|  Genel Bilgiler                 |
|  Toplam Cevap: 245              |
|  Tamamlanma Oranı: %89          |
|  Ortalama Süre: 3.2 dk          |
+----------------------------------+
|  Soru 1: Yaş Dağılımı           |
|  [Pasta Grafik]                 |
|  18-25: %35 | 26-35: %40        |
|  36-45: %20 | 46+: %5           |
+----------------------------------+
|  Soru 2: Şehir Dağılımı          |
|  [Bar Grafik]                   |
|  Ankara: 98 (%40)               |
|  İstanbul: 73 (%30)             |
|  İzmir: 49 (%20)                |
+----------------------------------+
|  [PDF İndir] [Excel İndir]       |
+----------------------------------+
```

### 5. Kullanıcı Paneli (/user/surveys)

```
+----------------------------------+
|  Anketlerim (5 aktif, 2 biten)  |
+----------------------------------+
|  📋 Müşteri Memnuniyeti        |
|  245 cevap | Aktif              |
|  Oluşturulma: 5 gün önce        |
|  [Sonuçlar] [Düzenle] [Paylaş]   |
+----------------------------------+
|  📋 Ürün Değerlendirme         |
|  189 cevap | 5 gün kaldı         |
|  Oluşturulma: 10 gün önce       |
|  [Sonuçlar] [Düzenle] [Paylaş]   |
+----------------------------------+
|  📋 Etkinlik Geri Bildirimi     |
|  67 cevap | Bitti               |
|  [Sonuçlar] [Arşivle]           |
+----------------------------------+
|  [Yeni Anket Oluştur]           |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Dynamic form generation
- ✅ Complex data relationships
- ✅ Statistical calculations
- ✅ Data export (PDF/Excel)
- ✅ Anonymous response handling
- ✅ Survey logic and validation
- ✅ Bulk data processing

### Vue.js + Quasar

- ✅ Dynamic form components
- ✅ Chart.js integration
- ✅ Drag-and-drop interfaces
- ✅ Real-time progress tracking
- ✅ Data visualization
- ✅ Multi-step forms
- ✅ Responsive survey design

### Genel Beceriler

- ✅ Survey design principles
- ✅ Data analysis and reporting
- ✅ User experience optimization
- ✅ Statistical data processing
- ✅ Anonymous data handling
- ✅ Export functionality

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Dynamic form generation
4. Statistical calculation engine
5. Data export functionality
6. Anonymous response system

### 2. Frontend (Quasar)

1. Survey creation interface
2. Dynamic form components
3. Response collection system
4. Results visualization
5. Chart integration
6. Export functionality

### 3. Test ve Optimizasyon

1. Form validation testing
2. Statistical accuracy verification
3. Performance optimization
4. Cross-browser compatibility

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Chart.js ile grafik gösterimleri
- PDF/Excel export için Laravel Excel
- Anonim cevap sistemi için IP tracking
- Drag-and-drop soru sıralama
- Real-time sonuç güncellemeleştirme
- Drag-drop interface
- Survey logic implementation
- Data analytics
- Export functionality

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Soru tipleri: çoktan seçmeli, açık uçlu, rating
- Basit analitik raporlar
- PDF export opsiyonel
