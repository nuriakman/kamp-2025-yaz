# MySQL Kullanıcı Yönetimi Notları

**DİKKAT!** Uzak bağlantı için sunucuda `bind-address` tanımı yapılması gerekmektedir.

Ana sunucuda `bind-address` tanımı yapılması 

**MySQL/MariaDB Config dosyası konumu:**

- MariaDB: `/etc/mysql/mariadb.conf.d/50-server.cnf`
- MySQL: `/etc/mysql/mysql.conf.d/mysqld.cnf`


Config dosyası içinde `bind-address` satırını değiştirerek `0.0.0.0` olarak ayarlamak gerekir.

```bash
vi /etc/mysql/mariadb.conf.d/50-server.cnf

bind-address  = 0.0.0.0

# Servisi tekrar başlat
systemctl restart mariadb

```


## ✅ 1. Kullanıcı Oluşturma

### Belirli Bir IP'den Erişim İzni Vererek
```sql
CREATE USER 'kullanici_adi'@'ip_adresi' IDENTIFIED BY 'guclu_sifre';
```
**Örnek:**
```sql
CREATE USER 'api_user'@'192.168.1.100' IDENTIFIED BY 'Jt#8k$sPq92!';
```

### Birden Fazla IP için Aynı Kullanıcı
```sql
CREATE USER 'api_user'@'192.168.1.100' IDENTIFIED BY 'sifre';
CREATE USER 'api_user'@'165.232.86.153' IDENTIFIED BY 'sifre';
```

### Subnet (Ağ Aralığı) için Kullanıcı
```sql
CREATE USER 'api_user'@'192.168.1.%' IDENTIFIED BY 'sifre';
```

## ✅ 2. Yetkilendirme (GRANT)

### Belirli Bir Veritabanı için Tüm Yetkiler
```sql
GRANT ALL PRIVILEGES ON veritabani_adi.* TO 'kullanici_adi'@'ip_adresi';
FLUSH PRIVILEGES;
```

### Sadece Okuma-Yazma Yetkileri (CRUD)
```sql
GRANT SELECT, INSERT, UPDATE, DELETE ON veritabani_adi.* TO 'kullanici_adi'@'ip_adresi';
FLUSH PRIVILEGES;
```

### Sadece Okuma Yetkisi
```sql
GRANT SELECT ON veritabani_adi.* TO 'kullanici_adi'@'ip_adresi';
FLUSH PRIVILEGES;
```

### Belirli Tablolar için Yetki
```sql
GRANT SELECT, UPDATE ON veritabani.tablo1 TO 'kullanici'@'ip';
GRANT INSERT ON veritabani.tablo2 TO 'kullanici'@'ip';
FLUSH PRIVILEGES;
```

## ✅ 3. Kullanıcı ve Yetki Kontrolü

### Kullanıcı Listesi
```sql
SELECT user, host FROM mysql.user;
```

### Kullanıcı Yetkilerini Görüntüleme
```sql
SHOW GRANTS FOR 'kullanici_adi'@'ip_adresi';
```

## ✅ 4. Kullanıcı Silme ve Yetki Kaldırma

### Kullanıcıyı Tamamen Silme
```sql
DROP USER 'kullanici_adi'@'ip_adresi';
```

### Sadece Belirli Bir IP Erişimini Kaldırma
```sql
DROP USER 'kullanici_adi'@'192.168.1.100';
```

### Tüm Yetkileri Kaldırma (REVOKE)
```sql
REVOKE ALL PRIVILEGES ON veritabani_adi.* FROM 'kullanici_adi'@'ip_adresi';
FLUSH PRIVILEGES;
```

### Belirli Yetkileri Kaldırma
```sql
REVOKE DELETE, DROP ON veritabani_adi.* FROM 'kullanici_adi'@'ip_adresi';
FLUSH PRIVILEGES;
```

## ✅ 5. Kullanıcı Bilgilerini Değiştirme

### Şifre Değiştirme
```sql
ALTER USER 'kullanici_adi'@'ip_adresi' IDENTIFIED BY 'yeni_sifre';
```

### Kullanıcının IP'sini Değiştirme
```sql
RENAME USER 'kullanici_adi'@'eski_ip' TO 'kullanici_adi'@'yeni_ip';
```

## ✅ 6. Önemli Güvenlik Kuralları

1. **Minimum Ayrıcalık Prensibi**: Kullanıcıya sadece ihtiyacı olan yetkileri verin
2. **IP Kısıtlaması**: Mümkün olduğunca spesifik IP veya subnet kullanın
3. **Güçlü Şifreler**: Karmaşık şifreler kullanın
4. **Düzenli Denetim**: Kullanıcı ve yetkileri periyodik olarak gözden geçirin
5. **Wildcard (%) Kısıtlaması**: Production ortamında `'%'` kullanımından kaçının

## ✅ 7. Sık Kullanılan Yetkiler

- **SELECT**: Veri okuma
- **INSERT**: Veri ekleme  
- **UPDATE**: Veri güncelleme
- **DELETE**: Veri silme
- **CREATE**: Tablo/database oluşturma
- **DROP**: Tablo/database silme
- **ALTER**: Tablo yapısını değiştirme
- **ALL PRIVILEGES**: Tüm yetkiler

## ✅ 8. Pratik Örnekler

### Uygulama Kullanıcısı Oluşturma
```sql
CREATE USER 'app_user'@'192.168.1.%' IDENTIFIED BY 'GucluSifre123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON my_app_db.* TO 'app_user'@'192.168.1.%';
FLUSH PRIVILEGES;
```

### Raporlama Kullanıcısı Oluşturma
```sql
CREATE USER 'report_user'@'10.0.0.50' IDENTIFIED BY 'RaporSifre456!';
GRANT SELECT ON my_app_db.* TO 'report_user'@'10.0.0.50';
FLUSH PRIVILEGES;
```

### Kullanıcıyı ve Yetkilerini Temizleme
```sql
REVOKE ALL PRIVILEGES ON my_app_db.* FROM 'eski_user'@'192.168.1.100';
DROP USER 'eski_user'@'192.168.1.100';
FLUSH PRIVILEGES;
```

**Not:** Her `GRANT` veya `REVOKE` işleminden sonra `FLUSH PRIVILEGES;` komutunu çalıştırmak yetki değişikliklerinin hemen etkin olmasını sağlar.