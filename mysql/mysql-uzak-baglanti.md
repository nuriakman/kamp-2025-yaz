# MySQL Uzak (Remote) Bağlantı Testi

## ✅ 1.) Kullanıcı tanımlama

```sql

-- İki ayrı kullanıcı oluştur
CREATE USER 'remote_user'@'localhost'  IDENTIFIED BY 'SüperGüçlüŞifre';
CREATE USER 'remote_user'@'<IPADRESI>' IDENTIFIED BY 'SüperGüçlüŞifre';

-- Yetkileri ver: Sadece Okuma-Yazma Yetkileri (CRUD)
GRANT SELECT, INSERT, UPDATE, DELETE ON VERİTABANIADI.* TO 'remote_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON VERİTABANIADI.* TO 'remote_user'@'<IPADRESI>';

FLUSH PRIVILEGES;

-- Şifre değiştirmek için:
ALTER USER 'remote_user'@'localhost'  IDENTIFIED BY 'SüperGüçlüŞifre';
ALTER USER 'remote_user'@'<IPADRESI>' IDENTIFIED BY 'SüperGüçlüŞifre';

FLUSH PRIVILEGES;

```


## ✅ 2.) Sunucuda `bind-address` tanımı yapılması 

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

## ✅ 3.) Uzak (Remote) Makineden Test İşlemi

Server'a bağlanmasını istediğimiz sunucuda test işlemi:

```bash
$ nc -zv  <IPADRESI>   3306

## örnek başarılı çıktı:
## Connection to <IPADRESI> 3306 port [tcp/mysql] succeeded!
```




## ✅ 4.) Uzak (Remote) MySQL Bağlantı test programı

```php
<?php
/**
 * Remote MySQL Bağlantı Testi
 * Bu dosyayı remote sunucuda çalıştırarak MySQL bağlantısını test edin.
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL bağlantı bilgileri - BUNLARI KENDİ AYARLARINIZLA DEĞİŞTİRİN!
$mysql_host = '<IPADRESI>'; // Bağlanılacak MySQL sunucusunun IP'si
$mysql_user = 'remote_user';   // Remote bağlantı için yetkili kullanıcı
$mysql_pass = 'SüperGüçlüŞifre';     // Kullanıcı şifresi
$mysql_db = 'VERİTABANIADI';        // Bağlanılacak veritabanı

// Bağlantı zaman aşımı (saniye)
$timeout = 10;

echo "<h2>Remote MySQL Bağlantı Testi</h2>";
echo "<p><strong>Hedef MySQL Sunucusu:</strong> " . htmlspecialchars($mysql_host) . "</p>";
echo "<p><strong>Kullanıcı:</strong> " . htmlspecialchars($mysql_user) . "</p>";
echo "<p><strong>Veritabanı:</strong> " . htmlspecialchars($mysql_db) . "</p>";
echo "<hr>";

try {
    // Bağlantı denemeden önce IP ve port erişilebilirliğini kontrol et
    echo "<h3>1. Port Erişilebilirlik Testi</h3>";
    $port = 3306;
    $socket = @fsockopen($mysql_host, $port, $errno, $errstr, $timeout);
    
    if ($socket) {
        echo "<p style='color: green;'>✓ Port 3306 erişilebilir</p>";
        fclose($socket);
    } else {
        echo "<p style='color: red;'>✗ Port 3306 erişilemez: $errstr ($errno)</p>";
    }

    // MySQL bağlantısı
    echo "<h3>2. MySQL Bağlantı Testi</h3>";
    
    // mysqli ile bağlantı
    $start_time = microtime(true);
    $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    $end_time = microtime(true);
    
    $connection_time = round(($end_time - $start_time) * 1000, 2); // ms cinsinden
    
    if ($conn->connect_error) {
        throw new Exception("MySQL bağlantı hatası: " . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✓ MySQL bağlantısı başarılı!</p>";
    echo "<p>Bağlantı süresi: " . $connection_time . " ms</p>";
    echo "<p>MySQL Sunucu Versiyonu: " . $conn->server_version . "</p>";
    echo "<p>MySQL Host Info: " . $conn->host_info . "</p>";

    // Basit bir sorgu testi
    echo "<h3>3. Sorgu Testi</h3>";
    $query = "SELECT 1 as test_value, NOW() as server_time, VERSION() as mysql_version";
    $result = $conn->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p style='color: green;'>✓ Sorgu başarılı!</p>";
        echo "<pre>Sorgu Sonucu: " . print_r($row, true) . "</pre>";
        $result->free();
    } else {
        echo "<p style='color: orange;'>⚠ Sorgu hatası: " . $conn->error . "</p>";
    }

    // Veritabanı tablolarını listele (opsiyonel)
    echo "<h3>4. Veritabanı Tabloları</h3>";
    $tables_query = "SHOW TABLES";
    $tables_result = $conn->query($tables_query);
    
    if ($tables_result && $tables_result->num_rows > 0) {
        echo "<p>Tablolar (" . $tables_result->num_rows . " adet):</p>";
        echo "<ul>";
        while ($table = $tables_result->fetch_array()) {
            echo "<li>" . htmlspecialchars($table[0]) . "</li>";
        }
        echo "</ul>";
        $tables_result->free();
    } else {
        echo "<p>Tablo bulunamadı veya listelemeye yetkiniz yok.</p>";
    }

    // Bağlantıyı kapat
    $conn->close();
    echo "<p style='color: green;'>✓ Bağlantı sorunsuz kapatıldı.</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>HATA:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Ek hata ayıklama bilgileri
    echo "<h3>Hata Ayıklama Bilgileri:</h3>";
    echo "<ul>";
    echo "<li>Hata Kodu: " . mysqli_connect_errno() . "</li>";
    echo "<li>Hata Mesajı: " . mysqli_connect_error() . "</li>";
    echo "<li>PHP Versiyonu: " . phpversion() . "</li>";
    echo "<li>MySQLi Desteği: " . (function_exists('mysqli_connect') ? 'Var' : 'Yok') . "</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>Sunucu Bilgileri:</h3>";
echo "<ul>";
echo "<li>Remote Sunucu IP: " . $_SERVER['SERVER_ADDR'] . "</li>";
echo "<li>İstek Yapan IP: " . $_SERVER['REMOTE_ADDR'] . "</li>";
echo "<li>User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "</li>";
echo "</ul>";


```