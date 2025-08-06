# 📝 Blog Sitesi

## 📋 Proje Tanımı

Kullanıcıların blog yazılarını okuyabileceği, kategorilere göre filtreleyebileceği ve yöneticilerin yazı yönetimi yapabileceği basit bir blog sistemi.

## 🎯 Proje Hedefleri

- Blog yazılarını listeleme ve okuma
- Kategorilere göre filtreleme
- Yazar bilgileri gösterme
- Admin paneli ile yazı yönetimi
- Yorum sistemi (basit)

## 🗄️ Veritabanı Yapısı

### 1. categories (Kategoriler)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
slug (varchar 100) - URL dostu ad
description (text) - Kategori açıklaması
color (varchar 7) - Kategori rengi (#hex)
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. posts (Blog Yazıları)

```sql
id (Primary Key)
category_id (Foreign Key) - categories.id
user_id (Foreign Key) - users.id (yazar)
title (varchar 255) - Yazı başlığı
slug (varchar 255) - URL dostu başlık
excerpt (text) - Yazı özeti
content (longtext) - Yazı içeriği
featured_image (varchar 255) - Öne çıkan görsel
is_published (boolean) - Yayınlanma durumu
published_at (timestamp) - Yayınlanma tarihi
view_count (integer) - Görüntülenme sayısı
created_at (timestamp)
updated_at (timestamp)
```

### 3. comments (Yorumlar)

```sql
id (Primary Key)
post_id (Foreign Key) - posts.id
name (varchar 100) - Yorum yapan adı
email (varchar 255) - E-posta
comment (text) - Yorum içeriği
is_approved (boolean) - Onay durumu
created_at (timestamp)
updated_at (timestamp)
```

### 4. users (Kullanıcılar - Laravel varsayılan)

```sql
id (Primary Key)
name (varchar 255)
email (varchar 255)
password (varchar 255)
bio (text) - Yazar biyografisi
avatar (varchar 255) - Profil fotoğrafı
is_admin (boolean) - Admin yetkisi
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/posts - Blog yazılarını listele (sayfalama ile)
GET /api/posts/{slug} - Tek yazı detayı (view_count artırır)
GET /api/categories - Aktif kategorileri listele
GET /api/categories/{slug}/posts - Kategoriye ait yazıları listele
GET /api/posts/{id}/comments - Yazıya ait onaylı yorumları listele
POST /api/posts/{id}/comments - Yeni yorum ekle
```

### Admin Endpoints (JWT korumalı)

```
POST /api/categories - Kategori oluştur
PUT /api/categories/{id} - Kategori güncelle
DELETE /api/categories/{id} - Kategori sil

POST /api/posts - Yazı oluştur
PUT /api/posts/{id} - Yazı güncelle
DELETE /api/posts/{id} - Yazı sil

GET /api/comments - Tüm yorumları listele
PUT /api/comments/{id}/approve - Yorumu onayla
DELETE /api/comments/{id} - Yorumu sil
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
- 📚 Kategoriler
- 📝 Tüm Yazılar
- 👤 Giriş/Çıkış

### Admin Menü (Giriş yapıldıktan sonra)

- 📊 Dashboard
- 📝 Yazı Yönetimi
- 📁 Kategori Yönetimi
- 💬 Yorum Yönetimi
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü)            |
+----------------------------------+
|  Hero Section                    |
|  Son Blog Yazıları               |
+----------------------------------+
|  Öne Çıkan Yazı (Büyük Card)     |
|  [Resim]                         |
|  Başlık + Özet + Yazar + Tarih   |
+----------------------------------+
|  Diğer Yazılar (Grid 2x2)        |
|  [Yazı 1] [Yazı 2]              |
|  [Yazı 3] [Yazı 4]              |
+----------------------------------+
|  Kategoriler (Chip'ler)          |
|  [Teknoloji] [Sağlık] [Spor]     |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Yazı Detay (/post/:slug)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Breadcrumb: Ana > Kategori > Yazı|
+----------------------------------+
|  Yazı Başlığı                    |
|  Yazar + Tarih + Kategori        |
+----------------------------------+
|  Öne Çıkan Görsel                |
+----------------------------------+
|  Yazı İçeriği                    |
|  (Markdown formatında)           |
+----------------------------------+
|  Yazar Bilgisi (Card)            |
|  [Avatar] Ad + Bio               |
+----------------------------------+
|  Yorumlar Bölümü                 |
|  Yorum Formu + Yorum Listesi     |
+----------------------------------+
|  İlgili Yazılar                  |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 3. Kategori Sayfası (/category/:slug)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Kategori Başlığı ve Açıklaması  |
|  [Kategori Rengi ile vurgu]      |
+----------------------------------+
|  Yazı Sayısı: X yazı bulundu     |
+----------------------------------+
|  Yazı Listesi (Card'lar)         |
|  📄 Yazı Başlığı                 |
|      Özet + Yazar + Tarih        |
|      [Devamını Oku]              |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 4. Admin Dashboard (/admin)

```
+----------------------------------+
|  Admin Header + Menü             |
+----------------------------------+
|  İstatistikler (Cards)           |
|  [Toplam Yazı] [Kategoriler]     |
|  [Yorumlar] [Görüntülenme]       |
+----------------------------------+
|  Son Yazılar (Tablo)             |
|  Başlık | Kategori | Durum | Tarih|
+----------------------------------+
|  Bekleyen Yorumlar               |
|  Yorum | Yazı | Tarih | [Onayla] |
+----------------------------------+
```

### 5. Yazı Yönetimi (/admin/posts)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Yazı] [Filtrele]        |
+----------------------------------+
|  Yazı Tablosu                    |
|  ID | Başlık | Kategori | Durum  |
|     | [Düzenle] [Sil]           |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 6. Yazı Oluştur/Düzenle (/admin/posts/create)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Form (2 Kolon)                  |
|  Sol: Başlık, İçerik (Editor)    |
|  Sağ: Kategori, Durum, Görsel    |
+----------------------------------+
|  [Taslak Kaydet] [Yayınla]       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Model ilişkileri (hasMany, belongsTo, hasOne)
- ✅ Slug generation
- ✅ Scope'lar (published, category)
- ✅ Mutator ve Accessor kullanımı
- ✅ File upload işlemleri
- ✅ Pagination
- ✅ Eager loading (::with)

### Vue.js + Quasar

- ✅ Dynamic routing (:slug parametresi)
- ✅ Component composition
- ✅ Props ve emit kullanımı
- ✅ Computed properties
- ✅ Watchers
- ✅ Rich text editor entegrasyonu
- ✅ Image upload component'i
- ✅ Infinite scroll

### Genel Beceriler

- ✅ Content management system (CMS) mantığı
- ✅ SEO dostu URL yapısı
- ✅ Comment moderation sistemi
- ✅ File management
- ✅ Date formatting
- ✅ Text truncation

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Seeder'ları hazırla (örnek veri)
4. Controller'ları oluştur
5. API Resource'ları tanımla
6. File upload middleware'i ekle

### 2. Frontend (Quasar)

1. Layout component'ini oluştur
2. Blog list component'i
3. Blog detail component'i
4. Comment component'i
5. Admin panel component'leri
6. Rich text editor entegrasyonu

### 3. Test ve Optimizasyon

1. API endpoint'lerini test et
2. Frontend routing'i test et
3. Image upload'ı test et
4. Performance optimizasyonu

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Rich text editor olarak Quasar'ın QEditor component'i kullanılacak
- Görsel upload basit file input ile yapılacak
- Yorum sistemi basit tutulacak (sadece ad, email, yorum)
- SEO optimizasyonu temel seviyede olacak
