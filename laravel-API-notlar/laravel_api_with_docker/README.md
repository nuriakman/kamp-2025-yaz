# Laravel API Workshop Ortamı

Bu proje, JWT ile güvence altına alınmış basit bir REST API geliştirmeyi öğretmek amacıyla hazırlanan bir atölye (workshop) için başlangıç ortamı sunar.

Proje, öğrencilerin kendi bilgisayarlarında kolayca ve hızla bir geliştirme ortamı kurabilmesi için Docker kullanır.

## Kullanılan Teknolojiler

- **Backend:** Laravel
- **Web Sunucusu:** Apache
- **Veritabanı:** SQLite
- **PHP Sürümü:** 8.3
- **Konteynerizasyon:** Docker

## Özellikler

- **Tek Komutla Kurulum:** `docker-compose up -d` komutu ile tüm ortam ayağa kalkar.
- **Dinamik Port Ataması:** Port çakışmalarını önlemek için 8000-8100 aralığında boş bir port otomatik olarak atanır.
- **Platform Bağımsız:** Docker'ın çalıştığı tüm işletim sistemlerinde (Windows, macOS, Linux) sorunsuzca çalışır.
- **Gerekli Her Şey Dahil:** Apache, PHP, Composer ve gerekli tüm PHP eklentileri imaj içerisinde mevcuttur.

Kurulum ve kullanım detayları için [KURULUM.md](KURULUM.md) dosyasına göz atınız.
