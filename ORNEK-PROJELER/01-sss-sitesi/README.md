# 🔍 SSS (Sıkça Sorulan Sorular) Sitesi

## 📋 Proje Tanımı

Kullanıcıların sıkça sorulan soruları kategorilere göre görüntüleyebileceği, arama yapabileceği ve yöneticilerin soru-cevap ekleyip düzenleyebileceği basit bir SSS sistemi.

## 🎯 Proje Hedefleri

- Kategorilere göre soru-cevap listeleme
- Arama fonksiyonu
- Admin paneli ile CRUD işlemleri
- Responsive tasarım

## 🗄️ Veritabanı Yapısı

### 1. categories (Kategoriler)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
description (text) - Kategori açıklaması
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. faqs (SSS'ler)

```sql
id (Primary Key)
category_id (Foreign Key) - categories.id
question (text) - Soru
answer (text) - Cevap
is_active (boolean) - Aktif/pasif durumu
view_count (integer) - Görüntülenme sayısı
created_at (timestamp)
updated_at (timestamp)
```

### 3. users (Kullanıcılar - Laravel varsayılan)

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
GET /api/categories - Aktif kategorileri listele
GET /api/categories/{id}/faqs - Kategoriye ait SSS'leri listele
GET /api/faqs - Tüm aktif SSS'leri listele
GET /api/faqs/search?q={query} - SSS'lerde arama
GET /api/faqs/{id} - Tek SSS detayı (view_count artırır)
```

### Admin Endpoints (JWT korumalı)

```
POST /api/categories - Kategori oluştur
PUT /api/categories/{id} - Kategori güncelle
DELETE /api/categories/{id} - Kategori sil

POST /api/faqs - SSS oluştur
PUT /api/faqs/{id} - SSS güncelle
DELETE /api/faqs/{id} - SSS sil
```

### Auth Endpoints

```
POST /api/auth/login - Giriş yap
POST /api/auth/logout - Çıkış yap
GET /api/auth/me - Kullanıcı bilgileri
```

## Menü Yapısı

### Ana Menü

- Ana Sayfa
- Kategoriler
- Arama
- Giriş/Çıkış

### Admin Menü (Giriş yapıldıktan sonra)

- 📈 Kontrol Paneli
- 📁 Kategori Yönetimi
- ❓ SSS Yönetimi
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Arama)    |
+----------------------------------+
|  Hero Section                    |
|  "Aradığınız cevabı bulun"       |
+----------------------------------+
|  Popüler Kategoriler (Grid)      |
|  [Kategori 1] [Kategori 2]       |
|  [Kategori 3] [Kategori 4]       |
+----------------------------------+
|  En Çok Görüntülenen SSS'ler     |
|  1. Soru başlığı...              |
|  2. Soru başlığı...              |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Kategori Detay (/category/:id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Breadcrumb: Ana > Kategori Adı  |
+----------------------------------+
|  Kategori Başlığı ve Açıklaması  |
+----------------------------------+
|  SSS Listesi (Accordion)         |
|  ▼ Soru 1                        |
|    Cevap 1...                    |
|  ▶ Soru 2                        |
|  ▶ Soru 3                        |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 3. Arama Sayfası (/search)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Arama Kutusu                    |
|  [Arama terimi giriniz...]  [🔍] |
+----------------------------------+
|  Sonuçlar (X sonuç bulundu)      |
|  📄 Soru başlığı                 |
|      Cevap özeti...              |
|      Kategori: Genel             |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 4. Admin Kontrol Paneli (/admin)

```
+----------------------------------+
|  Admin Header + Menü             |
+----------------------------------+
|  İstatistikler (Cards)           |
|  [Toplam SSS] [Kategoriler]      |
|  [Görüntülenme] [Aktif SSS]      |
+----------------------------------+
|  Son Eklenen SSS'ler (Tablo)     |
|  Soru | Kategori | Tarih | Durum |
+----------------------------------+
```

### 5. SSS Yönetimi (/admin/faqs)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni SSS Ekle] [Filtrele]    |
+----------------------------------+
|  SSS Tablosu                     |
|  ID | Soru | Kategori | Durum    |
|     | [Düzenle] [Sil]           |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Model ilişkileri (hasMany, belongsTo)
- ✅ Resource Controller kullanımı
- ✅ API Resource sınıfları
- ✅ Eloquent sorguları ve filtreleme
- ✅ JWT Authentication
- ✅ Middleware kullanımı
- ✅ Validation rules

### Vue.js + Quasar

- ✅ Component yapısı
- ✅ Vue Router kullanımı
- ✅ Axios ile API çağrıları
- ✅ v-for döngüleri
- ✅ Conditional rendering (v-if, v-show)
- ✅ Form handling
- ✅ Quasar components (QCard, QExpansionItem, QTable)
- ✅ Responsive design

### Genel Beceriler

- ✅ CRUD işlemleri
- ✅ Arama fonksiyonu implementasyonu
- ✅ Admin panel geliştirme
- ✅ User experience (UX) tasarımı
- ✅ Error handling
- ✅ Loading states

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Controller'ları oluştur
4. API route'larını tanımla
5. Validation rule'larını ekle
6. JWT authentication'ı yapılandır

### 2. Frontend (Quasar)

1. Sayfa component'lerini oluştur
2. API service'lerini hazırla
3. Router yapılandırması
4. State management (Vuex/Pinia)
5. UI component'lerini geliştir
6. Responsive tasarımı tamamla

### 3. Test ve Optimizasyon

1. API endpoint'lerini test et
2. Frontend fonksiyonlarını test et
3. Performance optimizasyonu
4. Error handling iyileştirmeleri

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Görsel tasarım basit tutulacak, fonksiyon odaklı olacak
- Temel CRUD işlemleri ve ilişkili veri çekme odağında
- Arama fonksiyonu basit LIKE sorgusu ile yapılacak
