# 🔍 Ürün Özellikleri ve Karşılaştırma

## 📋 Proje Tanımı

Kullanıcıların ürünleri özelliklerine göre karşılaştırabileceği, filtreleyebileceği ve detaylı özellik bilgilerini görüntüleyebileceği sistem.

## 🎯 Proje Hedefleri

- Ürün özelliklerini detaylı listeleme
- Yan yana ürün karşılaştırma
- Özellik bazlı filtreleme
- Ürün puanlama sistemi
- Admin paneli ile ürün ve özellik yönetimi

## 🗄️ Veritabanı Yapısı

### 1. categories (Kategoriler)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
description (text) - Kategori açıklaması
is_active (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### 2. products (Ürünler)

```sql
id (Primary Key)
category_id (Foreign Key) - categories.id
name (varchar 255) - Ürün adı
brand (varchar 100) - Marka
model (varchar 100) - Model
description (text) - Ürün açıklaması
price (decimal 10,2) - Fiyat
image (varchar 255) - Ürün görseli
rating (decimal 3,2) - Ortalama puan (0.00-5.00)
is_active (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### 3. features (Özellikler)

```sql
id (Primary Key)
category_id (Foreign Key) - categories.id
name (varchar 100) - Özellik adı
unit (varchar 20) - Birim (GB, inch, MP, vb.)
data_type (enum) - Veri tipi (text, number, boolean)
is_comparable (boolean) - Karşılaştırılabilir mi?
sort_order (integer) - Sıralama
created_at (timestamp)
updated_at (timestamp)
```

### 4. product_features (Ürün Özellikleri)

```sql
id (Primary Key)
product_id (Foreign Key) - products.id
feature_id (Foreign Key) - features.id
value (text) - Özellik değeri
numeric_value (decimal 10,2) - Sayısal değer (karşılaştırma için)
created_at (timestamp)
updated_at (timestamp)
```

### 5. users (Kullanıcılar)

```sql
id (Primary Key)
name (varchar 255)
email (varchar 255)
password (varchar 255)
is_admin (boolean)
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/categories - Kategorileri listele
GET /api/products - Ürünleri listele (filtreleme ile)
GET /api/products/{id} - Ürün detayı
GET /api/products/compare?ids=1,2,3 - Ürün karşılaştırma
GET /api/categories/{id}/features - Kategoriye ait özellikleri listele
GET /api/categories/{id}/products - Kategoriye ait ürünleri listele
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/categories - Kategori oluştur
PUT /api/admin/categories/{id} - Kategori güncelle
DELETE /api/admin/categories/{id} - Kategori sil

POST /api/admin/products - Ürün oluştur
PUT /api/admin/products/{id} - Ürün güncelle
DELETE /api/admin/products/{id} - Ürün sil

POST /api/admin/features - Özellik oluştur
PUT /api/admin/features/{id} - Özellik güncelle
DELETE /api/admin/features/{id} - Özellik sil

POST /api/admin/product-features - Ürün özelliği ata
PUT /api/admin/product-features/{id} - Ürün özelliği güncelle
DELETE /api/admin/product-features/{id} - Ürün özelliği sil
```

## 🧭 Menü Yapısı

### Ana Menü

- 🏠 Ana Sayfa
- 📱 Kategoriler
- 🔍 Ürün Arama
- ⚖️ Karşılaştır
- 👤 Giriş

### Admin Menü

- 📊 Dashboard
- 📁 Kategori Yönetimi
- 📦 Ürün Yönetimi
- 🏷️ Özellik Yönetimi
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü)            |
+----------------------------------+
|  Hero Section                    |
|  "Ürünleri Karşılaştır"         |
+----------------------------------+
|  Kategoriler (Grid)              |
|  📱 Telefon    💻 Laptop         |
|  📷 Kamera     🎧 Kulaklık       |
+----------------------------------+
|  Öne Çıkan Karşılaştırmalar      |
|  iPhone vs Samsung               |
|  MacBook vs ThinkPad             |
+----------------------------------+
```

### 2. Kategori Sayfası (/category/:id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Kategori: Akıllı Telefonlar     |
+----------------------------------+
|  Filtreler (Sidebar)             |
|  Marka: [☑ Apple] [☐ Samsung]    |
|  RAM: [☐ 4GB] [☑ 8GB] [☐ 12GB]   |
|  Fiyat: [Min] - [Max]            |
+----------------------------------+
|  Ürün Listesi + Karşılaştır     |
|  [☐] iPhone 14 Pro              |
|      ⭐⭐⭐⭐⭐ 4.8              |
|      ₺45.000                     |
|  [☑] Samsung S23                |
|      ⭐⭐⭐⭐☆ 4.5              |
|      ₺35.000                     |
+----------------------------------+
|  [Seçilenleri Karşılaştır (2)]   |
+----------------------------------+
```

### 3. Ürün Detay (/product/:id)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Ürün Bilgileri (2 Kolon)        |
|  Sol: Görsel + Temel Bilgiler    |
|  Sağ: Özellikler Tablosu         |
|       Ekran: 6.1 inch            |
|       RAM: 8 GB                  |
|       Depolama: 256 GB           |
+----------------------------------+
|  [Karşılaştırmaya Ekle]          |
|  [Benzer Ürünler]               |
+----------------------------------+
```

### 4. Karşılaştırma (/compare)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Ürün Karşılaştırma (3 Kolon)    |
|  Özellik    | iPhone 14 | S23    |
|  ---------- | --------- | ------ |
|  Ekran      | 6.1"      | 6.1"   |
|  RAM        | 8 GB      | 8 GB   |
|  Kamera     | 48 MP     | 50 MP  |
|  Fiyat      | ₺45.000   | ₺35.000|
+----------------------------------+
|  [Farklılıkları Vurgula]         |
|  [PDF İndir] [Paylaş]            |
+----------------------------------+
```

### 5. Admin - Ürün Yönetimi

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Ürün] [Filtrele]        |
+----------------------------------+
|  Ürün Tablosu                    |
|  Ad | Marka | Kategori | Durum    |
|    | [Özellikler] [Düzenle] [Sil]|
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Pivot table relationships
- ✅ Dynamic filtering
- ✅ Complex queries with joins
- ✅ Data type handling
- ✅ Comparison logic
- ✅ Aggregation queries

### Vue.js + Quasar

- ✅ Multi-select functionality
- ✅ Dynamic table generation
- ✅ Comparison interface
- ✅ Advanced filtering UI
- ✅ Data visualization
- ✅ Responsive tables

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Karşılaştırma maksimum 3 ürün ile sınırlı
- Basit filtreleme sistemi
- Görsel karşılaştırma grafikleri opsiyonel
