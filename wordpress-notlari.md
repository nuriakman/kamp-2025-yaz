# Docker ile WordPress Kurulumu

Bu rehberde, docker-compose ve hazır yapılandırmalar ile WordPress'i hızlıca nasıl başlatabileceğinizi adım adım bulabilirsiniz.

## 1. Docker Kurulumu

Docker ve Docker Compose sisteminizde kurulu olmalıdır. Ubuntu için:

```bash
sudo apt update
sudo apt install docker.io docker-compose
```

## 2. Proje Dosya Yapısı

```
docker-wordpress/
├── config/
│   ├── mysql-init/
│   │   └── 01-init.sql
│   └── php.ini
├── db_data/           # Veritabanı verileri (otomatik oluşur)
├── docker-compose.yml
├── wordpress/         # WordPress dosyaları
│   ├── ...
```

## 3. docker-compose.yml İçeriği ve Servisler

- **wordpress:** WordPress uygulaması (port: 8080)
- **db:** MySQL veritabanı (veriler db_data altında tutulur)
- **adminer:** Veritabanı yönetimi için Adminer arayüzü (port: 8088)

## 4. WordPress'i Başlatmak

Proje dizininde aşağıdaki komutu çalıştırın:

```bash
docker-compose up -d
```

WordPress'e [http://localhost:8080](http://localhost:8080) üzerinden erişebilirsiniz.
Adminer için: [http://localhost:8088](http://localhost:8088)

## 5. Durdurma ve Temizleme

Tüm servisleri durdurmak ve verileri temizlemek için:

```bash
docker-compose down -v
```

## 6. Sık Sorulanlar & Notlar

- `db_data/` ve `wordpress/wp-content/uploads/` gibi klasörler .gitignore ile versiyon kontrolüne alınmaz.
- Varsayılan kullanıcı/parolalar docker-compose.yml içindedir. Güvenlik için üretimde değiştirin.
- Adminer ile veritabanı yönetimi kolayca yapılabilir.
- Hata veya izin sorunlarında ilgili klasörlerin sahibi ve izinlerini kontrol edin:
  ```bash
  sudo chown -R 1000:1000 docker-wordpress/db_data
  sudo chown -R 1000:1000 docker-wordpress/wordpress
  ```

---

Daha fazla bilgi için `docker-wordpress/README.md` dosyasına bakabilirsiniz.
