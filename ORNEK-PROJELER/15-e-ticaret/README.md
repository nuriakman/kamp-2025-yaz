# 🛍️ E-Ticaret Sitesi

## 📋 Proje Tanımı

Kullanıcıların ürünleri görüntüleyebileceği, sepete ekleyebileceği ve basit sipariş verebileceği temel bir e-ticaret sistemi.

## 🎯 Proje Hedefleri

- Ürün katalog görüntüleme
- Kategorilere göre filtreleme
- Sepet yönetimi
- Basit sipariş süreci
- Admin paneli ile ürün yönetimi

## 🗄️ Veritabanı Yapısı

### 1. categories (Kategoriler)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
slug (varchar 100) - URL dostu ad
description (text) - Kategori açıklaması
image (varchar 255) - Kategori görseli
is_active (boolean) - Aktif/pasif durumu
sort_order (integer) - Sıralama
created_at (timestamp)
updated_at (timestamp)
```

### 2. products (Ürünler)

```sql
id (Primary Key)
category_id (Foreign Key) - categories.id
name (varchar 255) - Ürün adı
slug (varchar 255) - URL dostu ad
description (text) - Ürün açıklaması
price (decimal 10,2) - Fiyat
discount_price (decimal 10,2) - İndirimli fiyat
stock_quantity (integer) - Stok miktarı
sku (varchar 100) - Stok kodu
image (varchar 255) - Ana ürün görseli
is_active (boolean) - Aktif/pasif durumu
is_featured (boolean) - Öne çıkan ürün
view_count (integer) - Görüntülenme sayısı
created_at (timestamp)
updated_at (timestamp)
```

### 3. orders (Siparişler)

```sql
id (Primary Key)
order_number (varchar 50) - Sipariş numarası
customer_name (varchar 255) - Müşteri adı
customer_email (varchar 255) - Müşteri e-postası
customer_phone (varchar 20) - Müşteri telefonu
shipping_address (text) - Teslimat adresi
total_amount (decimal 10,2) - Toplam tutar
status (enum) - Durum (pending, confirmed, shipped, delivered, cancelled)
notes (text) - Sipariş notları
ordered_at (timestamp) - Sipariş tarihi
created_at (timestamp)
updated_at (timestamp)
```

### 4. order_items (Sipariş Kalemleri)

```sql
id (Primary Key)
order_id (Foreign Key) - orders.id
product_id (Foreign Key) - products.id
product_name (varchar 255) - Ürün adı (sipariş anındaki)
product_price (decimal 10,2) - Ürün fiyatı (sipariş anındaki)
quantity (integer) - Miktar
subtotal (decimal 10,2) - Ara toplam
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
GET /api/categories - Aktif kategorileri listele
GET /api/products - Ürünleri listele (sayfalama, filtreleme)
GET /api/products/{slug} - Ürün detayı (view_count artırır)
GET /api/categories/{slug}/products - Kategoriye ait ürünleri listele
GET /api/products/featured - Öne çıkan ürünleri listele
POST /api/orders - Sipariş oluştur
GET /api/orders/{order_number}/status - Sipariş durumu sorgula
```

### Admin Endpoints (JWT korumalı)

```
GET /api/admin/categories - Tüm kategorileri listele
POST /api/admin/categories - Kategori oluştur
PUT /api/admin/categories/{id} - Kategori güncelle
DELETE /api/admin/categories/{id} - Kategori sil

GET /api/admin/products - Tüm ürünleri listele
POST /api/admin/products - Ürün oluştur
PUT /api/admin/products/{id} - Ürün güncelle
DELETE /api/admin/products/{id} - Ürün sil

GET /api/admin/orders - Siparişleri listele
PUT /api/admin/orders/{id}/status - Sipariş durumu güncelle
GET /api/admin/orders/{id} - Sipariş detayları
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
- 📚 Kategoriler
- 🛍️ Tüm Ürünler
- 🛒 Sepet (badge ile miktar)
- 🔍 Sipariş Sorgula

### Admin Menü (Giriş yapıldıktan sonra)

- 📊 Dashboard
- 📁 Kategori Yönetimi
- 📦 Ürün Yönetimi
- 📋 Sipariş Yönetimi
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Header (Logo + Menü + Sepet)    |
+----------------------------------+
|  Hero Slider/Banner              |
|  Öne Çıkan Ürünler               |
+----------------------------------+
|  Kategoriler (Grid)              |
|  [Elektronik] [Giyim]           |
|  [Ev & Yaşam] [Spor]            |
+----------------------------------+
|  Öne Çıkan Ürünler (Grid)        |
|  🖼️ Ürün Resmi                   |
|     Ürün Adı                     |
|     ₺199.99 ₺149.99             |
|     [Sepete Ekle]               |
+----------------------------------+
|  Footer                          |
+----------------------------------+
```

### 2. Ürün Listesi (/products)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Breadcrumb + Filtreler          |
|  Kategori: [Dropdown] Sıralama: [Dropdown]|
+----------------------------------+
|  Ürün Grid (3-4 kolon)           |
|  📦 Ürün 1    📦 Ürün 2         |
|     ₺99.99       ₺149.99         |
|     [Sepete Ekle] [Sepete Ekle]  |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 3. Ürün Detay (/product/:slug)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Breadcrumb: Ana > Kategori > Ürün|
+----------------------------------+
|  Ürün Detayı (2 Kolon)           |
|  Sol: Ürün Görseli               |
|  Sağ: Adı, Fiyat, Açıklama       |
|       Miktar: [1] [Sepete Ekle]  |
|       Stok: 15 adet kaldı        |
+----------------------------------+
|  Ürün Açıklaması (Tab'lar)       |
|  [Açıklama] [Özellikler]         |
+----------------------------------+
|  İlgili Ürünler                  |
+----------------------------------+
```

### 4. Sepet (/cart)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Sepetim (X ürün)                |
+----------------------------------+
|  Sepet Tablosu                   |
|  Ürün | Miktar | Fiyat | Toplam  |
|  📦 Ürün Adı                     |
|      [- 2 +] ₺99.99 ₺199.98     |
|      [Kaldır]                   |
+----------------------------------+
|  Toplam: ₺299.97                 |
|  [Alışverişe Devam] [Sipariş Ver]|
+----------------------------------+
```

### 5. Sipariş Formu (/checkout)

```
+----------------------------------+
|  Header                          |
+----------------------------------+
|  Sipariş Bilgileri               |
+----------------------------------+
|  Müşteri Bilgileri               |
|  Ad Soyad: [________________]    |
|  E-posta: [________________]     |
|  Telefon: [________________]     |
+----------------------------------+
|  Teslimat Adresi                 |
|  [Textarea]                      |
+----------------------------------+
|  Sipariş Özeti                   |
|  Ürünler + Toplam Tutar          |
+----------------------------------+
|  [Siparişi Tamamla]              |
+----------------------------------+
```

### 6. Admin Dashboard (/admin)

```
+----------------------------------+
|  Admin Header + Menü             |
+----------------------------------+
|  İstatistikler (Cards)           |
|  [Toplam Ürün] [Kategoriler]     |
|  [Siparişler] [Günlük Satış]     |
+----------------------------------+
|  Son Siparişler (Tablo)          |
|  Sipariş No | Müşteri | Tutar | Durum|
+----------------------------------+
|  Stok Uyarıları                  |
|  Düşük stoklu ürünler listesi    |
+----------------------------------+
```

### 7. Ürün Yönetimi (/admin/products)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  [+ Yeni Ürün] [Filtrele]        |
+----------------------------------+
|  Ürün Tablosu                    |
|  ID | Adı | Kategori | Fiyat | Stok|
|     | [Düzenle] [Sil]           |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

### 8. Sipariş Yönetimi (/admin/orders)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Filtreler: Durum [Dropdown]     |
+----------------------------------+
|  Sipariş Tablosu                 |
|  No | Müşteri | Tutar | Durum    |
|    | [Detay] [Durum Güncelle]   |
+----------------------------------+
|  Sayfalama                       |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ E-commerce model relationships
- ✅ Decimal field handling (prices)
- ✅ Stock management
- ✅ Order number generation
- ✅ Transaction handling
- ✅ Enum status management
- ✅ Price calculations

### Vue.js + Quasar

- ✅ Shopping cart state management
- ✅ LocalStorage kullanımı
- ✅ Quantity input component'i
- ✅ Price formatting
- ✅ Cart badge component'i
- ✅ Product grid layout
- ✅ Image gallery component'i
- ✅ Checkout flow

### Genel Beceriler

- ✅ E-commerce workflow
- ✅ Shopping cart logic
- ✅ Order management
- ✅ Stock tracking
- ✅ Price calculation
- ✅ Customer data handling

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Seeder'ları hazırla (örnek ürünler)
4. Controller'ları oluştur
5. Order logic'ini implement et
6. Stock management ekle

### 2. Frontend (Quasar)

1. Product catalog component'leri
2. Shopping cart functionality
3. Checkout process
4. Order tracking
5. Admin panel component'leri
6. Image upload component'i

### 3. Test ve Optimizasyon

1. Shopping cart flow'unu test et
2. Order process'ini test et
3. Stock management'ı test et
4. Admin panel'i test et

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- Ödeme entegrasyonu dahil edilmeyecek
- Basit stok takibi yapılacak
- Görsel upload basit file input ile
- Kargo hesaplama dahil edilmeyecek
- Kullanıcı kayıt sistemi opsiyonel
