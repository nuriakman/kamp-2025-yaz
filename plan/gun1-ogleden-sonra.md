# Gün 1 - Öğleden Sonra Oturumu: Geliştirme Ortamı ve Temel Araçlar

## 1. XAMPP/MAMP Kurulumu ve Yapılandırması

### 1.1 XAMPP Kurulumu (Windows/macOS/Linux)

1. [XAMPP resmi sitesinden](https://www.apachefriends.org/) indirin
2. İşletim sisteminize uygun sürümü seçin
3. Kurulum sihirbazını takip edin
   - Apache, MySQL, PHP ve phpMyAdmin bileşenlerini seçin
   - Kurulum dizinini not alın (genellikle `C:\xampp` veya `/Applications/XAMPP/`)

### 1.2 MAMP Kurulumu (macOS için alternatif)

1. [MAMP resmi sitesinden](https://www.mamp.info/) indirin
2. .pkg dosyasını çalıştırıp kurulumu tamamlayın
3. Uygulamalar klasöründen MAMP'ı başlatın

### 1.3 Apache ve MySQL Servislerini Başlatma

- XAMPP Control Panel üzerinden Apache ve MySQL'i başlatın
- Tarayıcıdan `http://localhost/dashboard/` adresine giderek test edin
- phpMyAdmin'e erişmek için `http://localhost/phpmyadmin` adresini kullanın

## 2. PHP ve Composer Kurulumu

### 2.1 PHP Kurulumu

1. XAMPP/MAMP ile gelen PHP'yi kullanabilirsiniz
   - XAMPP: `C:\xampp\php\php.exe`
   - MAMP: `/Applications/MAMP/bin/php/phpX.X.X/bin/php`
2. Terminalde versiyon kontrolü:
   ```bash
   php -v
   ```

### 2.2 Composer Kurulumu

1. [Composer resmi sitesinden](https://getcomposer.org/download/) indirin
2. Kurulum sihirbazını takip edin
3. Kurulumu doğrulayın:
   ```bash
   composer --version
   ```

## 3. Postman Kurulumu ve Kullanımı

### 3.1 Postman Kurulumu

1. [Postman resmi sitesinden](https://www.postman.com/downloads/) indirin
2. Kurulumu tamamlayın
3. Hesap oluşturun (ücretsiz)

### 3.2 Temel Kullanım

- Yeni bir istek oluşturma
- GET, POST, PUT, DELETE metodlarını kullanma
- Headers ve Body ayarları
- Ortam değişkenleri (Environments)
- Koleksiyonlar oluşturma

## 4. Veritabanı Yönetimi

### 4.1 phpMyAdmin Kullanımı

- Veritabanı oluşturma
- Tablo oluşturma ve yönetme
- SQL sorguları çalıştırma
- Veri içe/dışa aktarma

### 4.2 Örnek Veritabanı Oluşturma

```sql
CREATE DATABASE okul;
USE okul;

CREATE TABLE ogrenciler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(100) NOT NULL,
    ogrenci_no VARCHAR(20) UNIQUE,
    kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO ogrenciler (ad_soyad, ogrenci_no)
VALUES ('Ahmet Yılmaz', '20230001');
```

## 5. VS Code ile Verimli Çalışma

### 5.1 Temel Kısayollar

- `Ctrl+P`: Dosya açma
- `Ctrl+Shift+P`: Komut paleti
- `Ctrl+Shift+\`: Editörü bölme
- `F2`: Yeniden adlandırma
- `F12`: Tanıma git
- `Ctrl+Click`: Tanıma git
- `Alt+↑/↓`: Satırı yukarı/aşağı taşı
- `Shift+Alt+↑/↓`: Satırı kopyalayıp yukarı/aşağı taşı

### 5.2 Terminal Entegrasyonu

- Entegre terminali açma: `` Ctrl+` ``
- Yeni terminal: `Ctrl+Shift+` `
- Terminali bölme: `Ctrl+Shift+5`

## 6. Ödev ve Pratik

1. XAMPP/MAMP kurulumunu tamamlayın
2. phpMyAdmin üzerinden yeni bir veritabanı oluşturun
3. Birkaç örnek tablo ekleyin ve veri girişi yapın
4. Postman kurulumunu yapın ve temel işlemleri deneyin
5. VS Code'da yeni bir PHP dosyası oluşturup çalıştırın:

   ```php
   <?php
   echo "Merhaba Dünya!";

   $isim = "PHP";
   echo "Merhaba $isim!";
   ```

## 7. Yararlı Kaynaklar

- [XAMPP Dokümantasyonu](https://www.apachefriends.org/faq.html)
- [Composer Paketleri](https://packagist.org/)
- [Postman Learning Center](https://learning.postman.com/)
- [phpMyAdmin Dokümantasyonu](https://docs.phpmyadmin.net/)

---

**Not:** Yarınki dersimizde MySQL ve temel SQL sorgularını öğreneceğiz. Bugünkü kurulumları tamamladığınızdan emin olun.
