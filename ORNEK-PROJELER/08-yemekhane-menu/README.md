# 🍽️ Yemekhane Menü Sistemi

## 📋 Proje Tanımı

Günlük yemek menülerini yönetme, kalori hesaplama ve beslenme takip sistemi. Yemekhane personeli menü oluşturabilir, kullanıcılar rezervasyon yapabilir ve beslenme takibi yapabilir.

## 🎯 Proje Hedefleri

- Günlük/haftalık menü planlama ve yönetimi
- Kalori ve besin değeri hesaplama sistemi
- Öğün rezervasyon ve takip sistemi
- Beslenme istatistikleri ve raporlama
- Maliyet hesaplama ve bütçe takibi

## 🗺️ Veritabanı Yapısı

### 1. food_categories (Yemek Kategorileri)

```sql
id (Primary Key)
name (varchar 100) - Kategori adı
description (text) - Açıklama
icon (varchar 100) - İkon
color (varchar 7) - Renk kodu
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 2. foods (Yemekler)

```sql
id (Primary Key)
category_id (Foreign Key) - food_categories.id
name (varchar 255) - Yemek adı
description (text) - Açıklama
ingredients (text) - Malzemeler
calories_per_100g (integer) - 100g başına kalori
protein_per_100g (decimal 5,2) - 100g başına protein (g)
carbs_per_100g (decimal 5,2) - 100g başına karbonhidrat (g)
fat_per_100g (decimal 5,2) - 100g başına yağ (g)
fiber_per_100g (decimal 5,2) - 100g başına lif (g)
preparation_time (integer) - Hazırlama süresi (dakika)
cost_per_portion (decimal 8,2) - Porsiyon başına maliyet
allergy_info (json) - Alerji bilgileri
is_vegetarian (boolean) - Vejetaryen mi
is_vegan (boolean) - Vegan mı
is_gluten_free (boolean) - Gluten free mi
photo (varchar 255) - Yemek fotoğrafı
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 3. menus (Menüler)

```sql
id (Primary Key)
date (date) - Menü tarihi
meal_type (enum) - Öğün tipi (breakfast, lunch, dinner, snack)
total_calories (integer) - Toplam kalori
total_cost (decimal 10,2) - Toplam maliyet
max_capacity (integer) - Maksimum kapasite
current_reservations (integer) - Mevcut rezervasyon sayısı
status (enum) - Durum (draft, published, served, cancelled)
notes (text) - Notlar
created_by (integer) - Oluşturan kullanıcı ID
created_at (timestamp)
updated_at (timestamp)
```

### 4. menu_items (Menü Kalemleri)

```sql
id (Primary Key)
menu_id (Foreign Key) - menus.id
food_id (Foreign Key) - foods.id
portion_size (decimal 6,2) - Porsiyon boyutu (gram)
quantity_available (integer) - Mevcut miktar
quantity_reserved (integer) - Rezerve edilen miktar
is_main_course (boolean) - Ana yemek mi
is_optional (boolean) - Opsiyonel mi
created_at (timestamp)
updated_at (timestamp)
```

### 5. users (Kullanıcılar)

```sql
id (Primary Key)
first_name (varchar 100) - Ad
last_name (varchar 100) - Soyad
email (varchar 255) - E-posta
phone (varchar 20) - Telefon
employee_id (varchar 50) - Çalışan numarası
department (varchar 100) - Departman
role (enum) - Rol (student, employee, staff, admin)
dietary_restrictions (json) - Diyet kısıtlamaları
allergies (json) - Alerjiler
is_active (boolean) - Aktif/pasif durumu
created_at (timestamp)
updated_at (timestamp)
```

### 6. reservations (Rezervasyonlar)

```sql
id (Primary Key)
user_id (Foreign Key) - users.id
menu_id (Foreign Key) - menus.id
reservation_number (varchar 20) - Rezervasyon numarası
status (enum) - Durum (active, served, cancelled, no_show)
special_requests (text) - Özel istekler
reserved_at (timestamp) - Rezervasyon zamanı
served_at (timestamp) - Servis zamanı
rating (integer) - Değerlendirme (1-5)
feedback (text) - Geri bildirim
created_at (timestamp)
updated_at (timestamp)
```

## 🔌 API Endpoint'leri

### Public Endpoints

```
GET /api/menus/today - Bugünkü menüler
GET /api/menus/week - Haftalık menüler
GET /api/menus/{date} - Belirli tarih menüsü
GET /api/foods - Yemek listesi
GET /api/foods/{id} - Yemek detayı
GET /api/categories - Kategori listesi
```

### User Endpoints (JWT korumalı)

```
POST /api/reservations - Rezervasyon oluştur
GET /api/reservations - Kullanıcı rezervasyonları
PUT /api/reservations/{id}/cancel - Rezervasyon iptal
POST /api/reservations/{id}/feedback - Geri bildirim
GET /api/user/nutrition-stats - Beslenme istatistikleri
GET /api/user/meal-history - Yemek geçmişi
```

### Staff Endpoints (JWT korumalı)

```
GET /api/staff/reservations/today - Bugünkü rezervasyonlar
PUT /api/staff/reservations/{id}/serve - Servis yap
GET /api/staff/menu/{id}/stats - Menü istatistikleri
POST /api/staff/inventory/update - Stok güncelle
```

### Admin Endpoints (JWT korumalı)

```
POST /api/admin/foods - Yemek ekle
PUT /api/admin/foods/{id} - Yemek güncelle
POST /api/admin/menus - Menü oluştur
PUT /api/admin/menus/{id} - Menü güncelle
GET /api/admin/reports/daily - Günlük rapor
GET /api/admin/reports/weekly - Haftalık rapor
GET /api/admin/reports/nutrition - Beslenme raporu
GET /api/admin/reports/cost - Maliyet raporu
```

## 🧭 Menü Yapısı

### Kullanıcı Menü

- 🏠 Ana Sayfa
- 🍽️ Bugünkü Menü
- 📅 Haftalık Menü
- 📋 Rezervasyonlarım
- 📈 Beslenme Takibi
- 👤 Profil

### Personel Menü

- 📈 Dashboard
- 🍽️ Bugünkü Servis
- 📋 Rezervasyonlar
- 📦 Stok Durumu
- 📄 Raporlar
- 👤 Profil

### Admin Menü

- 📈 Dashboard
- 🍽️ Yemek Yönetimi
- 📅 Menü Planlama
- 👥 Kullanıcı Yönetimi
- 📄 Raporlar
- 👤 Profil

## 🎨 UI Yapısı (Quasar)

### 1. Ana Sayfa (/)

```
+----------------------------------+
|  Yemekhane Menü Sistemi         |
+----------------------------------+
|  Bugünkü Menü (16 Aralık)       |
+----------------------------------+
|  🍳 Kahvaltı (07:30-09:00)     |
|  • Menemen, Ekmek, Çay          |
|  Kapasite: 45/50                |
|  [Rezervasyon Yap]              |
+----------------------------------+
|  🍲 Öğle (12:00-14:00)        |
|  • Mercimek Çorbası, Tavuk Pirzola|
|  • Pilav, Salata                |
|  Kapasite: 120/150              |
|  [Rezervasyon Yap]              |
+----------------------------------+
|  [Haftalık Menü] [Rezervasyonlarım]|
+----------------------------------+
```

### 2. Bugünkü Menü (/menu/today)

```
+----------------------------------+
|  Bugünkü Menü - 16 Aralık 2024  |
+----------------------------------+
|  🍳 Kahvaltı (07:30-09:00)     |
+----------------------------------+
|  🍳 Menemen                   |
|     Kalori: 180 kcal            |
|     Protein: 12g | Karb: 8g     |
|  🍞 Ekmek                      |
|     Kalori: 120 kcal            |
|  ☕ Çay                         |
|     Kalori: 5 kcal              |
+----------------------------------+
|  Toplam: 305 kcal               |
|  Müsait: 45/50 kişi             |
|  [Rezervasyon Yap] [Detaylar]   |
+----------------------------------+
```

### 3. Rezervasyon Oluşturma (/reservations/new)

```
+----------------------------------+
|  Yeni Rezervasyon                |
+----------------------------------+
|  Menü Seçimi                     |
|  Tarih: [16 Aralık 2024 ▼]     |
|  Öğün: [Öğle Yemeği ▼]       |
+----------------------------------+
|  Menü Detayları                  |
|  • Mercimek Çorbası (85 kcal)    |
|  • Tavuk Pirzola (220 kcal)     |
|  • Pilav (180 kcal)             |
|  • Salata (45 kcal)             |
+----------------------------------+
|  Özel İstekler                   |
|  [Az tuzlu olsun]               |
+----------------------------------+
|  [Rezervasyon Yap] [İptal]      |
+----------------------------------+
```

### 4. Rezervasyonlarım (/reservations)

```
+----------------------------------+
|  Rezervasyonlarım                |
+----------------------------------+
|  Aktif Rezervasyonlar (2)        |
+----------------------------------+
|  🍲 16 Aralık - Öğle Yemeği   |
|     Saat: 12:30                 |
|     Durum: 🟢 Onaylandı         |
|     [Detay] [İptal]              |
+----------------------------------+
|  🍽️ 17 Aralık - Akşam Yemeği |
|     Saat: 19:00                 |
|     Durum: 🟡 Beklemede         |
|     [Detay] [İptal]              |
+----------------------------------+
|  Geçmiş (5)                      |
|  [Tümünü Gör]                   |
+----------------------------------+
```

### 5. Beslenme Takibi (/nutrition)

```
+----------------------------------+
|  Beslenme Takibi                 |
+----------------------------------+
|  Bugünkü Özet                    |
|  Kalori: 1,250 / 2,000 kcal     |
|  Protein: 65g / 80g             |
|  Karbonhidrat: 180g / 250g      |
|  Yağ: 45g / 65g                 |
+----------------------------------+
|  📈 Haftalık Grafik            |
|  [Kalori] [Protein] [Karb] [Yağ]|
+----------------------------------+
|  Son Yemekler                    |
|  • Öğle: Tavuk Pirzola (530 kcal)|
|  • Kahvaltı: Menemen (305 kcal)  |
+----------------------------------+
```

### 6. Personel - Bugünkü Servis (/staff/today)

```
+----------------------------------+
|  Personel Panel - Fatma Hanım   |
+----------------------------------+
|  Bugünkü Servis Durumu           |
+----------------------------------+
|  🍳 Kahvaltı (Tamamlandı)       |
|     Servis: 48/50 kişi          |
|     Kalan: 2 porsiyon           |
+----------------------------------+
|  🍲 Öğle (Devam Ediyor)        |
|     Servis: 95/150 kişi         |
|     Bekleyen: 25 rezervasyon    |
|     [Servis Yap] [Stok Güncelle]|
+----------------------------------+
|  Hızlı İşlemler                 |
|  [QR Kod Tara] [Manuel Giriş]   |
+----------------------------------+
```

### 7. Admin - Menü Planlama (/admin/menu-planning)

```
+----------------------------------+
|  Admin Header                    |
+----------------------------------+
|  Menü Planlama - Hafta 51        |
+----------------------------------+
|  Pazartesi | Salı | Çarş | Perş |
|  [+] Kahv. | [+] | [+] | [+]    |
|  [+] Öğle  | [+] | [+] | [+]    |
|  [+] Akşam | [+] | [+] | [+]    |
+----------------------------------+
|  Yemek Havuzu                    |
|  🔍 [Yemek Ara]                |
|  • Mercimek Çorbası [Ekle]       |
|  • Tavuk Pirzola [Ekle]         |
+----------------------------------+
|  [Kaydet] [Yayınla] [Önizleme]   |
+----------------------------------+
```

## 🎓 Öğrenim Kazanımları

### Laravel API

- ✅ Complex data relationships
- ✅ Nutritional calculations
- ✅ Reservation system logic
- ✅ Inventory management
- ✅ Reporting and analytics
- ✅ Cost calculation
- ✅ Scheduling algorithms

### Vue.js + Quasar

- ✅ Calendar components
- ✅ Chart.js integration
- ✅ QR code scanning
- ✅ Real-time capacity updates
- ✅ Drag-and-drop planning
- ✅ Mobile-first design
- ✅ Progressive Web App

### Genel Beceriler

- ✅ Food service management
- ✅ Nutritional analysis
- ✅ Capacity planning
- ✅ Cost management
- ✅ User experience design
- ✅ Health and dietary systems

## 🚀 Geliştirme Adımları

### 1. Backend (Laravel API)

1. Migration'ları oluştur
2. Model'leri ve ilişkileri tanımla
3. Nutritional calculation logic
4. Reservation system
5. Inventory tracking
6. Reporting system

### 2. Frontend (Quasar)

1. Menu display components
2. Reservation interface
3. Nutrition tracking dashboard
4. Staff service interface
5. Admin planning tools
6. Mobile optimization

### 3. Test ve Optimizasyon

1. Reservation flow testing
2. Calculation accuracy
3. Performance optimization
4. Mobile usability

## 📝 Notlar

- Proje 2 kişilik grup için 3 günde tamamlanabilir
- QR kod entegrasyonu için QuaggaJS kullanılacak
- Grafik gösterimi için Chart.js
- Real-time updates için WebSocket
- PWA özellikleri mobil kullanım için
- Beslenme verileri USDA database'den alınabilir

## 🗺️ Veritabanı

- meals: name, category, calories, ingredients, price
- daily_menus: date, meal_id, meal_type (breakfast/lunch/dinner)
- reservations: user_id, menu_date, meal_type, quantity

## 🎓 Kazanımlar

- Calendar integration, Nutrition calculation, Reservation system
