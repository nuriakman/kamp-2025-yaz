# Gün 2 - Sabah Oturumu: Veritabanı Temelleri ve SQL'e Giriş

## 1. Veritabanı Kavramları

### 1.1 Veritabanı Nedir?

- Verilerin düzenli bir şekilde saklandığı yapı
- İlişkisel veritabanı yönetim sistemleri (RDBMS)
- Tablo, kayıt ve alan kavramları

### 1.2 İlişkisel Veritabanı Bileşenleri

- **Tablolar**: Verilerin saklandığı yapılar
- **Sütunlar (Kolonlar)**: Her bir veri alanı
- **Satırlar (Kayıtlar)**: Her bir veri girişi
- **Anahtarlar**: Birincil anahtar (Primary Key), Yabancı anahtar (Foreign Key)

## 2. MySQL ile Çalışma

### 2.1 phpMyAdmin Arayüzü

- Veritabanı oluşturma
- Tablo oluşturma ve yönetme
- SQL sekmesini kullanma

### 2.2 Temel Veri Tipleri

- **Sayısal Tipler**: INT, FLOAT, DECIMAL
- **Metin Tipleri**: VARCHAR, TEXT, LONGTEXT
- **Tarih ve Zaman**: DATE, DATETIME, TIMESTAMP
- **Mantıksal**: BOOLEAN, TINYINT(1)

## 3. SQL Temelleri

### 3.1 Veritabanı ve Tablo İşlemleri

```sql
-- Veritabanı oluşturma
CREATE DATABASE okul;

-- Veritabanını kullanma
USE okul;

-- Tablo oluşturma
CREATE TABLE ogrenciler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(100) NOT NULL,
    ogrenci_no VARCHAR(20) UNIQUE,
    kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    aktif_mi BOOLEAN DEFAULT true
);
```

### 3.2 Temel CRUD İşlemleri

#### Veri Ekleme (INSERT)

```sql
-- Tekli kayıt ekleme
INSERT INTO ogrenciler (ad_soyad, ogrenci_no)
VALUES ('Ahmet Yılmaz', '20230001');

-- Çoklu kayıt ekleme
INSERT INTO ogrenciler (ad_soyad, ogrenci_no)
VALUES
    ('Ayşe Demir', '20230002'),
    ('Mehmet Kaya', '20230003'),
    ('Zeynep Şahin', '20230004');
```

#### Veri Sorgulama (SELECT)

```sql
-- Tüm kayıtları getir
SELECT * FROM ogrenciler;

-- Belirli sütunları seçme
SELECT id, ad_soyad, ogrenci_no
FROM ogrenciler;

-- Koşullu sorgulama (WHERE)
SELECT * FROM ogrenciler
WHERE ad_soyad LIKE 'A%';

-- Sıralama (ORDER BY)
SELECT * FROM ogrenciler
ORDER BY ad_soyad ASC;

-- Sınırlama (LIMIT)
SELECT * FROM ogrenciler
LIMIT 2;
```

## 4. Pratik Uygulama

### 4.1 Örnek Veritabanı Tasarımı

```sql
-- Bölümler tablosu
CREATE TABLE bolumler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bolum_adi VARCHAR(100) NOT NULL,
    aciklama TEXT
);

-- Öğrenciler tablosu (güncellenmiş)
ALTER TABLE ogrenciler
ADD COLUMN bolum_id INT,
ADD FOREIGN KEY (bolum_id) REFERENCES bolumler(id);

-- Dersler tablosu
CREATE TABLE dersler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ders_adi VARCHAR(100) NOT NULL,
    kredi INT,
    bolum_id INT,
    FOREIGN KEY (bolum_id) REFERENCES bolumler(id)
);

-- Öğrenci ders kayıtları
CREATE TABLE ogrenci_ders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogrenci_id INT,
    ders_id INT,
    kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    vize_notu FLOAT,
    final_notu FLOAT,
    FOREIGN KEY (ogrenci_id) REFERENCES ogrenciler(id),
    FOREIGN KEY (ders_id) REFERENCES dersler(id)
);
```

### 4.2 Örnek Veri Girişi

```sql
-- Bölüm ekleme
INSERT INTO bolumler (bolum_adi, aciklama)
VALUES
    ('Bilgisayar Mühendisliği', 'Bilgisayar bilimleri ve yazılım mühendisliği eğitimi'),
    ('Elektrik-Elektronik Müh.', 'Elektrik ve elektronik mühendisliği eğitimi'),
    ('Endüstri Mühendisliği', 'Endüstri ve sistem mühendisliği eğitimi');

-- Ders ekleme
INSERT INTO dersler (ders_adi, kredi, bolum_id)
VALUES
    ('Veri Yapıları', 4, 1),
    ('Algoritma Analizi', 3, 1),
    ('Devre Teorisi', 4, 2),
    ('Üretim Planlama', 3, 3);

-- Öğrenci güncelleme (bölüm atama)
UPDATE ogrenciler
SET bolum_id = 1
WHERE id IN (1, 3);

UPDATE ogrenciler
SET bolum_id = 2
WHERE id = 2;

UPDATE ogrenciler
SET bolum_id = 3
WHERE id = 4;
```

## 5. Ödev

1. Yukarıdaki veritabanı şemasını kendi sunucunuzda oluşturun
2. Her tabloya en az 5'er adet örnek veri ekleyin
3. Aşağıdaki sorguları yazın:
   - Tüm öğrencileri bölüm isimleriyle birlikte listeleyin
   - Her bölümdeki öğrenci sayısını gösterin
   - Not ortalaması 70'in üzerinde olan öğrencileri bulun

## 6. Yararlı Kaynaklar

- [MySQL Resmi Dokümantasyonu](https://dev.mysql.com/doc/)
- [SQL Öğrenme Rehberi](https://www.w3schools.com/sql/)
- [MySQL Workbench İndirme](https://dev.mysql.com/downloads/workbench/)

---

**Not:** Öğleden sonraki oturumda daha detaylı SQL sorguları ve ilişkisel veritabanı kavramlarını işleyeceğiz.
