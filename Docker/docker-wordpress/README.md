# WordPress Docker Kurulumu

Bu depo, WordPress, MySQL ve Adminer içeren bir Docker ortamı sağlar.

## Gereksinimler

- Docker
- Docker Compose

## Kurulum

1. Depoyu klonlayın veya dosyaları indirin
2. Terminali açıp proje dizinine gidin:
   ```bash
   cd /path/to/docker-wordpress
   ```
3. Docker konteynerlerini başlatın:
   ```bash
   docker-compose up -d
   ```

## Kullanım

- **WordPress**: http://localhost:8080
- **Adminer (Veritabanı Yönetimi)**: http://localhost:8081
  - Sistem: MySQL
  - Sunucu: db
  - Kullanıcı: wordpress
  - Şifre: wordpress
  - Veritabanı: wordpress

## WordPress Kurulumu

1. Tarayıcıdan http://localhost:8080 adresine gidin
2. Dil seçin ve "Devam et" butonuna tıklayın
3. Veritabanı bilgilerini girin:
   - Veritabanı adı: wordpress
   - Kullanıcı adı: wordpress
   - Şifre: wordpress
   - Veritabanı Sunucusu: db
   - Tablo Öneki: wp\_ (veya istediğiniz bir önek)
4. "Gönder" butonuna tıklayın
5. WordPress kurulum sihirbazını takip edin

## Yönetim Paneli

- WordPress Yönetici Paneli: http://localhost:8080/wp-admin
- Adminer (Veritabanı Yönetimi): http://localhost:8081

## Dosya Yapısı

- `wordpress/`: WordPress dosyaları
- `db_data/`: MySQL veritabanı dosyaları
- `config/`: Yapılandırma dosyaları
  - `php.ini`: PHP yapılandırma dosyası
  - `mysql-init/`: MySQL başlangıç scriptleri

## Komutlar

- Tüm konteynerleri başlat: `docker-compose up -d`
- Konteynerleri durdur: `docker-compose down`
- Logları görüntüle: `docker-compose logs -f`
- Tüm verileri sil (dikkatli kullanın): `docker-compose down -v`

## Güncelleme

WordPress'i güncellemek için:

```bash
docker-compose pull
docker-compose up -d
```

## Sorun Giderme

- **İzin sorunları**: WordPress dizininde izinleri düzeltmek için:

  ```bash
  sudo chown -R www-data:www-data wordpress/
  ```

- **MySQL bağlantı sorunları**: Konteynerlerin doğru sırada başladığından emin olun. MySQL'in tamamen başlaması biraz zaman alabilir.

## Lisans

MIT
