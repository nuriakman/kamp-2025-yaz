# Laravel API Workshop Ortamı (LAMP)

Bu proje, JWT ile güvence altına alınmış basit bir REST API geliştirmeyi öğretmek amacıyla hazırlanan bir atölye (workshop) için başlangıç ortamı sunar.

Proje, standart bir LAMP (Linux, Apache, MySQL, PHP) yığını üzerinde çalışacak şekilde yapılandırılmıştır.

## İçindekiler

- [KURULUM.md](KURULUM.md) Kurulum ve kullanım detaylar
- [DERS-NOTU.md](./DERS-NOTU.md) API Ders Notu
- [CURL.md](./CURL.md) cURL ile test etme talimatları
- [POSTMAN.md](./POSTMAN.md) Postman ile test etme talimatları
- [POSTMAN-COLLECTION.md](./POSTMAN-COLLECTION.md) Postman Koleksiyonu ile test etme talimatları
- [DERS-NOTU-JWT.md](./DERS-NOTU-JWT.md) JWT Entegrasyonu Ders Notu
- [JWT-NASIL-CALISIR.md](./JWT-NASIL-CALISIR.md) JWT Nasıl Çalışır Ders Notu

## Kullanılan Teknolojiler

- **Backend:** Laravel
- **Web Sunucusu:** Apache
- **Veritabanı:** MySQL / MariaDB
- **PHP Sürümü:** 8.1+

## Özellikler

- **Standart Kurulum:** Herhangi bir LAMP ortamında standart `composer` ve `artisan` komutlarıyla kurulabilir.
- **Esnek Veritabanı:** MySQL veya MariaDB ile çalışır.
- **JWT Entegrasyonu:** `tymon/jwt-auth` kütüphanesi ile güvenli endpoint'ler oluşturmaya hazır.
