# Gün 3 - Sabah Oturumu: SQL Pratikleri - Temel Sorgular

## 1. Giriş ve Ön Hazırlık

### 1.1 Bugün Ne Öğreneceğiz?

- Karmaşık SQL sorguları yazma
- Gerçek dünya senaryoları üzerinde çalışma
- Performans açısından verimli sorgular oluşturma

### 1.2 Örnek Veritabanı

Bugünkü çalışmamız için aşağıdaki veritabanı şemasını kullanacağız:

```sql
-- Veritabanı oluşturma
CREATE DATABASE e_ticaret;
USE e_ticaret;

-- Kategoriler tablosu
CREATE TABLE kategoriler (
    kategori_id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_adi VARCHAR(50) NOT NULL,
    aciklama TEXT,
    ust_kategori_id INT,
    FOREIGN KEY (ust_kategori_id) REFERENCES kategoriler(kategori_id)
);

-- Müşteriler tablosu
CREATE TABLE musteriler (
    musteri_id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    eposta VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20),
    kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    aktif_mi BOOLEAN DEFAULT TRUE
);

-- Adresler tablosu
CREATE TABLE adresler (
    adres_id INT AUTO_INCREMENT PRIMARY KEY,
    musteri_id INT NOT NULL,
    baslik VARCHAR(50) NOT NULL,
    adres TEXT NOT NULL,
    sehir VARCHAR(50) NOT NULL,
    ilce VARCHAR(50) NOT NULL,
    posta_kodu VARCHAR(10),
    varsayilan_adres BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (musteri_id) REFERENCES musteriler(musteri_id) ON DELETE CASCADE
);

-- Ürünler tablosu
CREATE TABLE urunler (
    urun_id INT AUTO_INCREMENT PRIMARY KEY,
    urun_adi VARCHAR(100) NOT NULL,
    kategori_id INT NOT NULL,
    aciklama TEXT,
    birim_fiyat DECIMAL(10,2) NOT NULL,
    stok_miktari INT NOT NULL DEFAULT 0,
    eklenme_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    guncellenme_tarihi DATETIME ON UPDATE CURRENT_TIMESTAMP,
    indirimli_fiyat DECIMAL(10,2),
    satista_mi BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (kategori_id) REFERENCES kategoriler(kategori_id)
);

-- Siparişler tablosu
CREATE TABLE siparisler (
    siparis_id INT AUTO_INCREMENT PRIMARY KEY,
    musteri_id INT NOT NULL,
    siparis_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    teslim_tarihi DATETIME,
    kargo_adresi_id INT NOT NULL,
    fatura_adresi_id INT NOT NULL,
    toplam_tutar DECIMAL(10,2) NOT NULL,
    kargo_ucreti DECIMAL(10,2) NOT NULL,
    indirim_tutari DECIMAL(10,2) DEFAULT 0.00,
    odeme_durumu ENUM('bekleniyor', 'tamamlandı', 'iptal', 'iade') DEFAULT 'bekleniyor',
    siparis_durumu ENUM('hazırlanıyor', 'kargoda', 'teslim edildi', 'iptal edildi') DEFAULT 'hazırlanıyor',
    notlar TEXT,
    FOREIGN KEY (musteri_id) REFERENCES musteriler(musteri_id),
    FOREIGN KEY (kargo_adresi_id) REFERENCES adresler(adres_id),
    FOREIGN KEY (fatura_adresi_id) REFERENCES adresler(adres_id)
);

-- Sipariş detayları tablosu
CREATE TABLE siparis_detaylari (
    siparis_detay_id INT AUTO_INCREMENT PRIMARY KEY,
    siparis_id INT NOT NULL,
    urun_id INT NOT NULL,
    miktar INT NOT NULL,
    birim_fiyat DECIMAL(10,2) NOT NULL,
    indirim_orani DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (siparis_id) REFERENCES siparisler(siparis_id) ON DELETE CASCADE,
    FOREIGN KEY (urun_id) REFERENCES urunler(urun_id)
);

-- Yorumlar tablosu
CREATE TABLE yorumlar (
    yorum_id INT AUTO_INCREMENT PRIMARY KEY,
    urun_id INT NOT NULL,
    musteri_id INT NOT NULL,
    puan TINYINT NOT NULL CHECK (puan BETWEEN 1 AND 5),
    baslik VARCHAR(100) NOT NULL,
    yorum_metni TEXT NOT NULL,
    yorum_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    onayli_mi BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (urun_id) REFERENCES urunler(urun_id) ON DELETE CASCADE,
    FOREIGN KEY (musteri_id) REFERENCES musteriler(musteri_id)
);
```

## 2. Temel Sorgu Pratikleri

### 2.1 Temel SELECT Sorguları

```sql
-- Tüm aktif müşterileri getir
SELECT * FROM musteriler WHERE aktif_mi = TRUE;

-- Belirli bir şehirdeki müşterileri say
SELECT sehir, COUNT(*) as musteri_sayisi
FROM adresler
GROUP BY sehir
ORDER BY musteri_sayisi DESC;

-- En yüksek fiyattan en düşüğe sıralanmış ürünler
SELECT urun_adi, birim_fiyat, stok_miktari
FROM urunler
WHERE satista_mi = TRUE
ORDER BY birim_fiyat DESC;
```

### 2.2 JOIN İşlemleri

```sql
-- Müşteri bilgileriyle birlikte siparişleri getir
SELECT
    m.ad,
    m.soyad,
    m.eposta,
    s.siparis_id,
    s.siparis_tarihi,
    s.toplam_tutar
FROM musteriler m
INNER JOIN siparisler s ON m.musteri_id = s.musteri_id
ORDER BY s.siparis_tarihi DESC;

-- Kategori isimleriyle birlikte ürünleri listele
SELECT
    k.kategori_adi,
    u.urun_adi,
    u.birim_fiyat,
    u.stok_miktari
FROM urunler u
INNER JOIN kategoriler k ON u.kategori_id = k.kategori_id
WHERE u.satista_mi = TRUE
ORDER BY k.kategori_adi, u.urun_adi;
```

## 3. İleri Sorgu Teknikleri

### 3.1 Alt Sorgular (Subqueries)

```sql
-- Ortalamanın üzerinde fiyata sahip ürünler
SELECT urun_adi, birim_fiyat
FROM urunler
WHERE birim_fiyat > (SELECT AVG(birim_fiyat) FROM urunler)
ORDER BY birim_fiyat DESC;

-- Hiç sipariş vermemiş müşteriler
SELECT m.ad, m.soyad, m.eposta
FROM musteriler m
WHERE m.musteri_id NOT IN (
    SELECT DISTINCT musteri_id
    FROM siparisler
);
```

### 3.2 Gruplama ve Toplu İşlevler

```sql
-- Her kategorideki ürün sayısı ve ortalama fiyat
SELECT
    k.kategori_adi,
    COUNT(u.urun_id) as urun_sayisi,
    ROUND(AVG(u.birim_fiyat), 2) as ortalama_fiyat,
    MIN(u.birim_fiyat) as en_ucuz,
    MAX(u.birim_fiyat) as en_pahali
FROM kategoriler k
LEFT JOIN urunler u ON k.kategori_id = u.kategori_id
GROUP BY k.kategori_id, k.kategori_adi
HAVING urun_sayisi > 0
ORDER BY urun_sayisi DESC;

-- Aylık sipariş istatistikleri
SELECT
    DATE_FORMAT(siparis_tarihi, '%Y-%m') as ay,
    COUNT(*) as siparis_sayisi,
    SUM(toplam_tutar) as toplam_ciro,
    AVG(toplam_tutar) as ortalama_siparis_tutari
FROM siparisler
WHERE siparis_tarihi >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
GROUP BY ay
ORDER BY ay;
```

## 4. Pratik Uygulama: E-Ticaret Sorguları

### 4.1 Müşteri Analizi

```sql
-- En çok harcama yapan 10 müşteri
SELECT
    m.musteri_id,
    CONCAT(m.ad, ' ', m.soyad) as musteri_adi,
    COUNT(s.siparis_id) as toplam_siparis,
    SUM(s.toplam_tutar) as toplam_harcama,
    AVG(s.toplam_tutar) as ortalama_siparis_tutari
FROM musteriler m
INNER JOIN siparisler s ON m.musteri_id = s.musteri_id
WHERE s.siparis_tarihi >= DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR)
GROUP BY m.musteri_id, musteri_adi
ORDER BY toplam_harcama DESC
LIMIT 10;

-- Tekrar eden müşteri oranı
SELECT
    COUNT(DISTINCT musteri_id) as toplam_musteri,
    COUNT(DISTINCT CASE WHEN siparis_sayisi > 1 THEN musteri_id END) as tekrar_eden_musteri,
    ROUND(COUNT(DISTINCT CASE WHEN siparis_sayisi > 1 THEN musteri_id END) /
          COUNT(DISTINCT musteri_id) * 100, 2) as tekrar_orani
FROM (
    SELECT
        musteri_id,
        COUNT(*) as siparis_sayisi
    FROM siparisler
    GROUP BY musteri_id
) as musteri_siparisleri;
```

### 4.2 Ürün ve Stok Analizi

```sql
-- Stok seviyesi düşük ürünler
SELECT
    u.urun_adi,
    k.kategori_adi,
    u.stok_miktari,
    u.birim_fiyat,
    u.stok_miktari * u.birim_fiyat as stok_degeri
FROM urunler u
INNER JOIN kategoriler k ON u.kategori_id = k.kategori_id
WHERE u.stok_miktari < 10
ORDER BY u.stok_miktari, stok_degeri DESC;

-- En çok satan 10 ürün
SELECT
    u.urun_id,
    u.urun_adi,
    k.kategori_adi,
    SUM(sd.miktar) as toplam_satis_miktari,
    SUM(sd.miktar * sd.birim_fiyat) as toplam_ciro,
    COUNT(DISTINCT s.siparis_id) as siparis_sayisi
FROM siparis_detaylari sd
INNER JOIN urunler u ON sd.urun_id = u.urun_id
INNER JOIN kategoriler k ON u.kategori_id = k.kategori_id
INNER JOIN siparisler s ON sd.siparis_id = s.siparis_id
WHERE s.siparis_tarihi >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
GROUP BY u.urun_id, u.urun_adi, k.kategori_adi
ORDER BY toplam_satis_miktari DESC
LIMIT 10;
```

## 5. Ödev

1. Yukarıdaki veritabanı şemasını oluşturun ve örnek veriler ekleyin (her tabloya en az 20'şer kayıt)
2. Aşağıdaki sorguları yazın:
   - Her kategoride en çok satan 3 ürünü listeleyin
   - Son 1 ayda en çok satış yapılan 5 şehri ve toplam satış tutarlarını bulun
   - İndirim oranı en yüksek 10 ürünü listeleyin
   - En çok yorum alan 5 ürünü ve ortalama puanlarını gösterin
3. Aşağıdaki raporları oluşturun:
   - Aylık büyüme oranını gösteren bir rapor
   - Müşteri segmentasyonu (yüksek/orta/düşük harcama)
   - Ürün kategorilerine göre satış dağılımı

## 6. Yararlı Kaynaklar

- [SQLZoo](https://sqlzoo.net/)
- [LeetCode Database Problems](https://leetcode.com/problemset/database/)
- [HackerRank SQL](https://www.hackerrank.com/domains/sql)

---

**Not:** Öğleden sonraki oturumda daha karmaşık SQL sorguları ve performans optimizasyonu konularını işleyeceğiz.
