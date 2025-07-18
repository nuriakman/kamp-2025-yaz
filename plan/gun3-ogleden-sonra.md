# Gün 3 - Öğleden Sonra Oturumu: İleri Düzey SQL Pratikleri

## 1. Günün Hedefleri

### 1.1 Bugün Neler Öğreneceğiz?

- Karmaşık SQL sorguları
- Performans optimizasyon teknikleri
- Pencere fonksiyonları (Window Functions)
- Yaygın SQL desenleri ve çözümleri

### 1.2 Kullanacağımız Veritabanı

Sabah oturumunda oluşturduğumuz e-ticaret veritabanını kullanmaya devam edeceğiz. Gerekirse yeni tablolar ekleyeceğiz.

## 2. İleri Düzey SQL Teknikleri

### 2.1 Pencere Fonksiyonları (Window Functions)

```sql
-- Her kategoride satış miktarına göre sıralama
SELECT
    k.kategori_adi,
    u.urun_adi,
    SUM(sd.miktar) as toplam_satis,
    RANK() OVER (PARTITION BY k.kategori_id ORDER BY SUM(sd.miktar) DESC) as siralama,
    ROUND(SUM(sd.miktar * sd.birim_fiyat), 2) as toplam_ciro,
    ROUND(SUM(sd.miktar * sd.birim_fiyat) /
          SUM(SUM(sd.miktar * sd.birim_fiyat)) OVER (PARTITION BY k.kategori_id) * 100, 2) as kategori_icerisindeki_payi
FROM siparis_detaylari sd
INNER JOIN urunler u ON sd.urun_id = u.urun_id
INNER JOIN kategoriler k ON u.kategori_id = k.kategori_id
GROUP BY k.kategori_id, k.kategori_adi, u.urun_id, u.urun_adi
ORDER BY k.kategori_adi, toplam_satis DESC;

-- Müşteri satışlarında hareketli ortalama (3 aylık)
SELECT
    m.musteri_id,
    CONCAT(m.ad, ' ', m.soyad) as musteri_adi,
    DATE_FORMAT(s.siparis_tarihi, '%Y-%m') as ay,
    SUM(s.toplam_tutar) as aylik_ciro,
    ROUND(AVG(SUM(s.toplam_tutar)) OVER (
        PARTITION BY m.musteri_id
        ORDER BY DATE_FORMAT(s.siparis_tarihi, '%Y-%m')
        ROWS BETWEEN 2 PRECEDING AND CURRENT ROW
    ), 2) as hareketli_ortalama_3_ay
FROM musteriler m
INNER JOIN siparisler s ON m.musteri_id = s.musteri_id
WHERE s.siparis_tarihi >= DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR)
GROUP BY m.musteri_id, musteri_adi, ay
ORDER BY m.musteri_id, ay;
```

### 2.2 Özyinelemeli Sorgular (Recursive CTE)

```sql
-- Kategori hiyerarşisini çıkarma
WITH RECURSIVE kategori_hiyerarsisi AS (
    -- Temel sorgu: En üst seviye kategoriler
    SELECT
        kategori_id,
        kategori_adi,
        ust_kategori_id,
        0 as seviye,
        CAST(kategori_adi AS CHAR(1000)) as tam_yol
    FROM kategoriler
    WHERE ust_kategori_id IS NULL

    UNION ALL

    -- Özyinelemeli kısım: Alt kategoriler
    SELECT
        k.kategori_id,
        k.kategori_adi,
        k.ust_kategori_id,
        kh.seviye + 1,
        CONCAT(kh.tam_yol, ' > ', k.kategori_adi) as tam_yol
    FROM kategoriler k
    INNER JOIN kategori_hiyerarsisi kh ON k.ust_kategori_id = kh.kategori_id
    WHERE kh.seviye < 10  -- Sonsuz döngüyü önlemek için
)
SELECT
    CONCAT(REPEAT('    ', seviye), kategori_adi) as kategori_agaci,
    tam_yol,
    seviye
FROM kategori_hiyerarsisi
ORDER BY tam_yol;
```

## 3. Performans Optimizasyonu

### 3.1 EXPLAIN Kullanımı

```sql
-- Sorgu planını analiz etme
EXPLAIN
SELECT
    u.urun_adi,
    k.kategori_adi,
    COUNT(sd.siparis_detay_id) as satis_sayisi
FROM urunler u
INNER JOIN kategoriler k ON u.kategori_id = k.kategori_id
LEFT JOIN siparis_detaylari sd ON u.urun_id = sd.urun_id
GROUP BY u.urun_id, u.urun_adi, k.kategori_adi
ORDER BY satis_sayisi DESC
LIMIT 10;
```

### 3.2 İndeks Kullanımı ve Optimizasyon

```sql
-- Mevcut indeksleri görüntüleme
SHOW INDEX FROM siparisler;

-- Eksik indeksleri tespit etme (MySQL 5.7+)
SELECT
    table_schema,
    table_name,
    index_name,
    GROUP_CONCAT(column_name ORDER BY seq_in_index) as columns,
    index_type,
    comment
FROM information_schema.statistics
WHERE table_schema = 'e_ticaret'
GROUP BY table_schema, table_name, index_name, index_type, comment;

-- Performans için önerilen indeksler
CREATE INDEX idx_siparis_tarihi ON siparisler(siparis_tarihi);
CREATE INDEX idx_urun_kategori ON urunler(kategori_id, satista_mi);
CREATE INDEX idx_siparis_musteri ON siparisler(musteri_id, siparis_tarihi);
```

## 4. Gerçek Dünya Senaryoları

### 4.1 RFM Analizi

```sql
-- Müşteri segmentasyonu için RFM analizi
WITH rfm AS (
    SELECT
        m.musteri_id,
        CONCAT(m.ad, ' ', m.soyad) as musteri_adi,
        DATEDIFF(CURRENT_DATE, MAX(s.siparis_tarihi)) as recency,
        COUNT(DISTINCT s.siparis_id) as frequency,
        SUM(s.toplam_tutar) as monetary,

        -- Recency puanı (en yeni en yüksek puan)
        NTILE(5) OVER (ORDER BY DATEDIFF(CURRENT_DATE, MAX(s.siparis_tarihi)) DESC) as r_score,

        -- Frequency puanı (en sık alışveriş yapan en yüksek puan)
        NTILE(5) OVER (ORDER BY COUNT(DISTINCT s.siparis_id)) as f_score,

        -- Monetary puanı (en çok harcama yapan en yüksek puan)
        NTILE(5) OVER (ORDER BY SUM(s.toplam_tutar)) as m_score
    FROM musteriler m
    INNER JOIN siparisler s ON m.musteri_id = s.musteri_id
    WHERE s.siparis_tarihi >= DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR)
    GROUP BY m.musteri_id, musteri_adi
)
SELECT
    musteri_id,
    musteri_adi,
    recency,
    frequency,
    monetary,
    r_score,
    f_score,
    m_score,
    CONCAT(r_score, f_score, m_score) as rfm_cell,
    CASE
        WHEN r_score >= 4 AND f_score >= 4 AND m_score >= 4 THEN 'Sadık Müşteriler'
        WHEN r_score >= 3 AND f_score >= 3 AND m_score >= 3 THEN 'Potansiyel Sadık Müşteriler'
        WHEN r_score >= 2 AND f_score >= 2 AND m_score >= 2 THEN 'Yeni Müşteriler'
        WHEN r_score >= 1 AND f_score >= 1 AND m_score >= 1 THEN 'Kaybolmaya Yüz Tutmuş Müşteriler'
        ELSE 'Uykudaki Müşteriler'
    END as segment
FROM rfm
ORDER BY r_score DESC, f_score DESC, m_score DESC;
```

### 4.2 Stok Optimizasyonu

```sql
-- ABC Analizi ile stok optimizasyonu
WITH urun_analiz AS (
    SELECT
        u.urun_id,
        u.urun_adi,
        k.kategori_adi,
        u.stok_miktari,
        u.birim_fiyat,
        u.stok_miktari * u.birim_fiyat as stok_degeri,
        COALESCE(SUM(sd.miktar), 0) as toplam_satis_miktari,
        COALESCE(SUM(sd.miktar * sd.birim_fiyat), 0) as toplam_ciro,
        COALESCE(COUNT(DISTINCT s.siparis_id), 0) as siparis_sayisi
    FROM urunler u
    LEFT JOIN siparis_detaylari sd ON u.urun_id = sd.urun_id
    LEFT JOIN siparisler s ON sd.siparis_id = s.siparis_id
    LEFT JOIN kategoriler k ON u.kategori_id = k.kategori_id
    WHERE s.siparis_tarihi >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY u.urun_id, u.urun_adi, k.kategori_adi, u.stok_miktari, u.birim_fiyat
),
stok_abc AS (
    SELECT
        *,
        SUM(stok_degeri) OVER (ORDER BY stok_degeri DESC) / SUM(stok_degeri) OVER () as kumulatif_oran
    FROM urun_analiz
)
SELECT
    urun_id,
    urun_adi,
    kategori_adi,
    stok_miktari,
    birim_fiyat,
    stok_degeri,
    toplam_satis_miktari,
    toplam_ciro,
    siparis_sayisi,
    CASE
        WHEN kumulatif_oran <= 0.8 THEN 'A Sınıfı'
        WHEN kumulatif_oran <= 0.95 THEN 'B Sınıfı'
        ELSE 'C Sınıfı'
    END as abc_sinifi,
    CASE
        WHEN stok_miktari = 0 THEN 'STOKTA YOK'
        WHEN stok_miktari <= 5 THEN 'KRİTİK SEVİYE'
        WHEN stok_miktari <= 10 THEN 'DÜŞÜK STOK'
        WHEN stok_miktari > 50 AND siparis_sayisi < 5 THEN 'FAZLA STOK'
        ELSE 'NORMAL'
    END as stok_durumu,
    CASE
        WHEN siparis_sayisi = 0 THEN 'SATIŞ YOK'
        WHEN siparis_sayisi < 5 THEN 'AZ SATAN'
        WHEN siparis_sayisi < 20 THEN 'ORTA SEVİYE SATIŞ'
        ELSE 'ÇOK SATAN'
    END as satis_durumu
FROM stok_abc
ORDER BY stok_degeri DESC;
```

## 5. Ödev ve Pratik

1. Yukarıdaki RFM analizi sorgusunu çalıştırın ve müşteri segmentlerinizi analiz edin
2. ABC analizi sonuçlarına göre stok optimizasyonu yapın
3. Aşağıdaki analizleri gerçekleştirin:
   - Haftanın hangi günü en çok satış yapılıyor?
   - Hangi saat aralıklarında siparişler yoğunlaşıyor?
   - En çok hangi ürünler birlikte satılıyor? (Markaşet analizi)
   - Müşteri edinme maliyeti (CAC) ve müşteri ömür boyu değeri (LTV) hesaplamaları

## 6. Yararlı Kaynaklar

- [MySQL 8.0 Reference Manual - Window Functions](https://dev.mysql.com/doc/refman/8.0/en/window-functions.html)
- [Use The Index, Luke!](https://use-the-index-luke.com/)
- [SQL Performance Explained](https://www.amazon.com/SQL-Performance-Explained-Everything-Developers/dp/3950307826)
- [Modern SQL](https://modern-sql.com/)

---

**Not:** Bu SQL eğitiminin sonuna geldik. Bir sonraki derste Laravel framework'üne giriş yapacağız ve bu öğrendiğimiz SQL bilgilerini Laravel Eloquent ORM ile nasıl kullanacağımızı göreceğiz.
