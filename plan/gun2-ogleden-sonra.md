# Gün 2 - Öğleden Sonra Oturumu: İleri Düzey SQL ve İlişkisel Veritabanı

## 1. İlişkisel Veritabanı Tasarımı

### 1.1 İlişki Türleri

- **Bire-Bir (One-to-One) İlişki**
- **Bire-Çok (One-to-Many) İlişki**
- **Çoka-Çok (Many-to-Many) İlişki**

### 1.2 Normalizasyon

- **1. Normal Form (1NF)**
- **2. Normal Form (2NF)**
- **3. Normal Form (3NF)**
- Pratik örneklerle normalizasyon uygulaması

## 2. İleri Düzey SQL Sorguları

### 2.1 JOIN İşlemleri

#### INNER JOIN

```sql
-- Öğrenci bilgilerini bölüm isimleriyle birlikte getir
SELECT o.ad_soyad, b.bolum_adi
FROM ogrenciler o
INNER JOIN bolumler b ON o.bolum_id = b.id;
```

#### LEFT JOIN

```sql
-- Tüm bölümleri ve o bölümdeki öğrencileri getir
SELECT b.bolum_adi, o.ad_soyad
FROM bolumler b
LEFT JOIN ogrenciler o ON b.id = o.bolum_id;
```

#### RIGHT JOIN ve FULL OUTER JOIN

```sql
-- Tüm öğrencileri ve bölümlerini getir (MySQL'de FULL JOIN yoktur, UNION kullanılır)
SELECT o.ad_soyad, b.bolum_adi
FROM ogrenciler o
LEFT JOIN bolumler b ON o.bolum_id = b.id
UNION
SELECT o.ad_soyad, b.bolum_adi
FROM ogrenciler o
RIGHT JOIN bolumler b ON o.bolum_id = b.id;
```

### 2.2 Alt Sorgular (Subqueries)

#### WHERE İçinde Alt Sorgu

```sql
-- Ortalamadan yüksek not alan öğrenciler
SELECT ogrenci_id, AVG((vize_notu + final_notu) / 2) as ortalama
FROM ogrenci_ders
GROUP BY ogrenci_id
HAVING ortalama > (SELECT AVG((vize_notu + final_notu) / 2) FROM ogrenci_ders);
```

#### FROM İçinde Alt Sorgu

```sql
-- Her bölümdeki ortalama notları göster
SELECT b.bolum_adi, AVG(od.vize_notu) as ortalama_vize
FROM (
    SELECT ders_id, vize_notu, final_notu
    FROM ogrenci_ders
    WHERE vize_notu IS NOT NULL
) od
INNER JOIN dersler d ON od.ders_id = d.id
INNER JOIN bolumler b ON d.bolum_id = b.id
GROUP BY b.id, b.bolum_adi;
```

### 2.3 Gruplama ve Toplu İşlevler

#### GROUP BY Kullanımı

```sql
-- Her bölümdeki öğrenci sayısı
SELECT b.bolum_adi, COUNT(o.id) as ogrenci_sayisi
FROM bolumler b
LEFT JOIN ogrenciler o ON b.id = o.bolum_id
GROUP BY b.id, b.bolum_adi;
```

#### HAVING Kullanımı

```sql
-- 2'den fazla öğrencisi olan bölümler
SELECT b.bolum_adi, COUNT(o.id) as ogrenci_sayisi
FROM bolumler b
LEFT JOIN ogrenciler o ON b.id = o.bolum_id
GROUP BY b.id, b.bolum_adi
HAVING ogrenci_sayisi > 2;
```

## 3. Görünümler (Views) ve Saklı Yordamlar (Stored Procedures)

### 3.1 Görünüm Oluşturma

```sql
-- Öğrenci not görünümü oluşturma
CREATE VIEW ogrenci_notlari AS
SELECT
    o.ad_soyad,
    d.ders_adi,
    od.vize_notu,
    od.final_notu,
    (od.vize_notu * 0.4 + od.final_notu * 0.6) as gecme_notu,
    CASE
        WHEN (od.vize_notu * 0.4 + od.final_notu * 0.6) >= 60 THEN 'Geçti'
        ELSE 'Kaldı'
    END as durum
FROM ogrenci_ders od
INNER JOIN ogrenciler o ON od.ogrenci_id = o.id
INNER JOIN dersler d ON od.ders_id = d.id;

-- Görünümü kullanma
SELECT * FROM ogrenci_notlari WHERE durum = 'Geçti';
```

### 3.2 Saklı Yordam Oluşturma

```sql
-- Öğrenci eklemek için saklı yordam
DELIMITER //
CREATE PROCEDURE ogrenci_ekle(
    IN p_ad_soyad VARCHAR(100),
    IN p_ogrenci_no VARCHAR(20),
    IN p_bolum_id INT
)
BEGIN
    INSERT INTO ogrenciler (ad_soyad, ogrenci_no, bolum_id)
    VALUES (p_ad_soyad, p_ogrenci_no, p_bolum_id);

    SELECT LAST_INSERT_ID() as yeni_ogrenci_id;
END //
DELIMITER ;

-- Saklı yordamı çağırma
CALL ogrenci_ekle('Yeni Öğrenci', '20230005', 1);
```

## 4. İndeksler ve Performans Optimizasyonu

### 4.1 İndeks Oluşturma

```sql
-- Tek sütunlu indeks
CREATE INDEX idx_ogrenci_adi ON ogrenciler(ad_soyad);

-- Bileşik indeks
CREATE INDEX idx_ogrenci_bolum ON ogrenciler(bolum_id, ad_soyad);

-- Benzersiz indeks
CREATE UNIQUE INDEX idx_ogrenci_no ON ogrenciler(ogrenci_no);
```

### 4.2 Sorgu Optimizasyonu

- EXPLAIN kullanımı
- İndeks kullanımını kontrol etme
- Sorgu performansını artırma teknikleri

## 5. Pratik Uygulama: Kütüphane Yönetim Sistemi

### 5.1 Veritabanı Şeması

```sql
CREATE TABLE uyeler (
    uye_id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    eposta VARCHAR(100) UNIQUE,
    kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kitaplar (
    kitap_id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) UNIQUE,
    baslik VARCHAR(200) NOT NULL,
    yazar VARCHAR(100),
    yayin_yili INT,
    stok_adet INT DEFAULT 0
);

CREATE TABLE odunc (
    odunc_id INT AUTO_INCREMENT PRIMARY KEY,
    uye_id INT,
    kitap_id INT,
    odunc_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    iade_tarihi DATE,
    gerceklesti_mi BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (uye_id) REFERENCES uyeler(uye_id),
    FOREIGN KEY (kitap_id) REFERENCES kitaplar(kitap_id)
);
```

### 5.2 Örnek Sorgular

```sql
-- En çok kitap ödünç alan üyeler
SELECT u.ad, u.soyad, COUNT(o.odunc_id) as odunc_kitap_sayisi
FROM uyeler u
LEFT JOIN odunc o ON u.uye_id = o.uye_id
GROUP BY u.uye_id, u.ad, u.soyad
ORDER BY odunc_kitap_sayisi DESC
LIMIT 5;

-- Şu an ödünç verilen kitaplar
SELECT k.baslik, k.yazar, u.ad, u.soyad, o.odunc_tarihi
FROM odunc o
INNER JOIN kitaplar k ON o.kitap_id = k.kitap_id
INNER JOIN uyeler u ON o.uye_id = u.uye_id
WHERE o.iade_tarihi IS NULL;
```

## 6. Ödev

1. Kütüphane yönetim sistemi veritabanını oluşturun ve örnek veriler ekleyin
2. Aşağıdaki sorguları yazın:
   - En çok okunan 3 kitabı listeleyin
   - Hiç kitap ödünç almayan üyeleri bulun
   - Her ay kaç kitap ödünç alındığını gösteren bir rapor oluşturun
3. Aşağıdaki saklı yordamları oluşturun:
   - Kitap ödünç verme
   - Kitap iade etme
   - Ceza hesaplama (geç iade durumunda)

## 7. Yararlı Kaynaklar

- [MySQL JOIN Görsel Rehberi](https://www.codeproject.com/Articles/33052/Visual-Representation-of-SQL-Joins)
- [SQL Performance Explained](https://use-the-index-luke.com/)
- [MySQL Stored Procedures](https://www.mysqltutorial.org/mysql-stored-procedure-tutorial.aspx)

---

**Not:** Yarınki dersimizde Laravel framework'üne giriş yapacağız. Bugün öğrendiğiniz SQL bilgileri Laravel'de ORM kullanırken çok işinize yarayacak.
