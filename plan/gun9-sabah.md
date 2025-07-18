# Gün 9 - Sabah Oturumu: Uygulamanın Tamamlanması, Test ve Deploy

## 1. Uygulamanın Son Hali ve Eksiklerin Tamamlanması

- Her grup, eksik kalan modül veya ekranları tamamlar
- Son işlevsellik ve UI düzenlemeleri yapılır
- Hatalar ve buglar hızlıca giderilir

## 2. Test Süreci

### 2.1 Manuel Test

- Tüm kullanıcı akışları tek tek denenir (kayıt, giriş, ürün ekleme, sipariş verme, vs.)
- Hatalı durumlar ve uç senaryolar kontrol edilir

### 2.2 Otomatik Test (isteğe bağlı)

- Basit API endpoint testleri (Postman koleksiyonu ile)
- Frontend'de temel bileşenlerin çalışıp çalışmadığı kontrol edilir

## 3. Deploy Hazırlığı

### 3.1 Backend Deploy

- Laravel projesini sunucuya veya bir cloud servisine (örn. Heroku, DigitalOcean, Render) yükleme
- .env dosyası ve veritabanı ayarlarının güncellenmesi
- Migration ve seeding işlemlerinin çalıştırılması

### 3.2 Frontend Deploy

- Quasar projesini build etme:

```bash
quasar build
```

- `dist/spa` klasörünü sunucuya veya Netlify/Vercel gibi servislere yükleme

### 3.3 Ortak Deploy Sorunları ve Çözümleri

- CORS hataları
- API URL'lerinin doğru yapılandırılması
- Ortak .env örnek dosyası paylaşımı

## 4. Sunum Hazırlığı

- Proje tanıtım sunumu için kısa bir slayt veya demo hazırlanır
- Projenin amacı, mimarisi, temel özellikleri ve demo akışı sunumda yer almalı
- Her grup üyesi, geliştirdiği kısmı kısaca anlatır

---

Öğleden sonra: Proje sunumları, değerlendirme ve kapanış.
