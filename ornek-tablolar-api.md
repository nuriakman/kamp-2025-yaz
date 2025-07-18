# Tablo Yapıları ve API Endpoint Listesi

Bu dosya, [ornek-prd.md](ornek-prd.md) dokümanındaki örnek projeye uygun temel veritabanı tablo yapıları ve REST API endpointlerinin listesini içerir.

---

## 1. Temel Tablo Yapıları

### kullanicilar

| Alan       | Tipi      | Açıklama               |
| ---------- | --------- | ---------------------- |
| id         | INT       | Birincil anahtar       |
| ad         | VARCHAR   | Kullanıcı adı          |
| email      | VARCHAR   | E-posta                |
| sifre      | VARCHAR   | Şifre (hashli)         |
| rol        | ENUM      | 'yönetici', 'personel' |
| created_at | TIMESTAMP | Oluşturulma zamanı     |
| updated_at | TIMESTAMP | Son güncelleme         |

### urunler

| Alan        | Tipi      | Açıklama           |
| ----------- | --------- | ------------------ |
| id          | INT       | Birincil anahtar   |
| isim        | VARCHAR   | Ürün adı           |
| aciklama    | TEXT      | Ürün açıklaması    |
| fiyat       | DECIMAL   | Ürün fiyatı        |
| stok        | INT       | Stok miktarı       |
| kategori_id | INT       | Kategori FK        |
| created_at  | TIMESTAMP | Oluşturulma zamanı |
| updated_at  | TIMESTAMP | Son güncelleme     |

### kategoriler

| Alan       | Tipi      | Açıklama           |
| ---------- | --------- | ------------------ |
| id         | INT       | Birincil anahtar   |
| ad         | VARCHAR   | Kategori adı       |
| created_at | TIMESTAMP | Oluşturulma zamanı |
| updated_at | TIMESTAMP | Son güncelleme     |

### siparisler

| Alan         | Tipi      | Açıklama           |
| ------------ | --------- | ------------------ |
| id           | INT       | Birincil anahtar   |
| kullanici_id | INT       | Kullanıcı FK       |
| tarih        | DATE      | Sipariş tarihi     |
| toplam_tutar | DECIMAL   | Toplam tutar       |
| created_at   | TIMESTAMP | Oluşturulma zamanı |
| updated_at   | TIMESTAMP | Son güncelleme     |

### siparis_urunleri

| Alan        | Tipi    | Açıklama           |
| ----------- | ------- | ------------------ |
| id          | INT     | Birincil anahtar   |
| siparis_id  | INT     | Sipariş FK         |
| urun_id     | INT     | Ürün FK            |
| miktar      | INT     | Siparişteki miktar |
| birim_fiyat | DECIMAL | Sipariş anı fiyat  |

---

## 2. API Endpoint Listesi

### Auth

- `POST   /api/login` → Kullanıcı girişi
- `POST   /api/register` → Kullanıcı kaydı

### Ürünler

- `GET    /api/urunler` → Ürün listesini getir
- `POST   /api/urunler` → Yeni ürün ekle
- `GET    /api/urunler/{id}` → Ürün detayını getir
- `PUT    /api/urunler/{id}` → Ürün güncelle
- `DELETE /api/urunler/{id}` → Ürün sil

### Kategoriler

- `GET    /api/kategoriler` → Kategori listesini getir
- `POST   /api/kategoriler` → Yeni kategori ekle
- `GET    /api/kategoriler/{id}`→ Kategori detayını getir
- `PUT    /api/kategoriler/{id}`→ Kategori güncelle
- `DELETE /api/kategoriler/{id}`→ Kategori sil

### Siparişler

- `GET    /api/siparisler` → Sipariş listesini getir
- `POST   /api/siparisler` → Yeni sipariş oluştur
- `GET    /api/siparisler/{id}` → Sipariş detayını getir

---

Her tablo ve endpoint, temel CRUD ve işlevsellik için gereklidir. Projenin ihtiyacına göre yeni alanlar veya endpointler eklenebilir.
