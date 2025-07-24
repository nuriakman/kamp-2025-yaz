# Kurulum Notları

Bu belge, Laravel API atölyesi için geliştirme ortamını nasıl kuracağınızı ve çalıştıracağınızı adım adım açıklamaktadır.

## Gereksinimler

Başlamadan önce bilgisayarınızda **Docker** ve **Docker Compose**'un kurulu olduğundan emin olun.

- [Docker Desktop Kurulumu (Windows/macOS)](https://www.docker.com/products/docker-desktop/)
- [Docker Engine Kurulumu (Linux)](https://docs.docker.com/engine/install/)

## Adım 1: Proje Dosyalarını İndirin

Bu projedeki tüm dosyaları (`docker-compose.yml`, `Dockerfile`, `vhost.conf`, `README.md` ve bu dosya) bilgisayarınızda boş bir klasöre indirin veya bir Git deposu olarak klonlayın.

## Adım 2: Geliştirme Ortamını Başlatın

Terminali veya komut istemcisini açın ve proje dosyalarının bulunduğu klasöre gidin.

Aşağıdaki komutu çalıştırarak Docker konteynerini oluşturun ve başlatın:

```bash
docker-compose up -d
```

Bu komut:
1.  Gerekli olan PHP ve Apache ortamını içeren Docker imajını oluşturur.
2.  Eğer mevcut değilse, yeni bir Laravel projesi oluşturur.
3.  Gerekli bağımlılıkları `composer` ile kurar.
4.  Veritabanı dosyasını oluşturur ve başlangıç göçlerini (migrations) çalıştırır.
5.  Apache sunucusunu arka planda başlatır.

*İlk kurulum internet hızınıza bağlı olarak birkaç dakika sürebilir.*

## Adım 3: Çalışan Portu Öğrenin

Ortam, bilgisayarınızda 8000 ile 8100 arasında boş olan ilk porta otomatik olarak kurulur. Hangi portun atandığını öğrenmek için aşağıdaki komutu çalıştırın:

```bash
docker-compose ps
```

Çıktıda `PORTS` sütununun altında `0.0.0.0:80XX->80/tcp` gibi bir ifade göreceksiniz. Buradaki `80XX` değeri, projenizin çalıştığı port numarasıdır.

Örneğin, `0.0.0.0:8001->80/tcp` yazıyorsa, projenize `http://localhost:8001` adresinden erişebilirsiniz.

## Adım 4: Ortamı Doğrulayın

Tarayıcınızı açın ve bir önceki adımda öğrendiğiniz adrese gidin (örneğin, `http://localhost:8001`). Ekranda Laravel başlangıç sayfasını görüyorsanız, kurulum başarıyla tamamlanmış demektir.

## Geliştirme Sırasında Kullanılabilecek Komutlar

- **Artisan Komutlarını Çalıştırmak:**
  ```bash
  docker-compose exec app php artisan <komut>
  ```
  Örnek: `docker-compose exec app php artisan make:model Product`

- **Logları (Kayıtları) Görüntülemek:**
  ```bash
  docker-compose logs -f
  ```

- **Ortamı Durdurmak:**
  ```bash
  docker-compose down
  ```

- **Ortamı Tamamen Kaldırmak (Veritabanı ve imaj dahil):**
  ```bash
  docker-compose down --volumes --rmi all
  ```

Artık API'nizi geliştirmeye hazırsınız! İyi kodlamalar!
