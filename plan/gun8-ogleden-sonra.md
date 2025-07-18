# Gün 8 - Öğleden Sonra Oturumu: Grup Projesine Başlangıç (Kodlama ve İş Bölümü)

## 1. Proje Kod Tabanının Oluşturulması

### 1.1 Proje Dizini ve Versiyon Kontrolü

- Her grup kendi GitHub/GitLab repository'sini oluşturur.
- Ana klasör yapısı:
  - `/backend` (Laravel API)
  - `/frontend` (Quasar SPA)
- .gitignore dosyalarını eklemeyi unutmayın.

### 1.2 Temel Kurulumlar

- Backend: Laravel projesini başlatın, temel migration ve model dosyalarını oluşturun.
- Frontend: Quasar CLI ile yeni proje başlatın, temel sayfa ve bileşenleri ekleyin.

## 2. Temel Özelliklerin Uygulanması

### 2.1 Backend (Laravel)

- Kullanıcı, ürün ve sipariş tabloları için migration ve model oluşturma
- Basit CRUD API endpointleri yazma (örnek: `/api/urunler`, `/api/siparisler`)
- CORS ve authentication (Laravel Sanctum) ayarlarını yapın

### 2.2 Frontend (Quasar)

- Ana sayfa, ürün listesi ve detay sayfası oluşturun
- Axios ile API'den veri çekme
- QTable ile ürün listeleme
- QDialog ile ürün ekleme/güncelleme

## 3. Takım İçi İş Bölümü ve Git Akışı

- Her grup üyesi, farklı bir modül veya ekran üzerinde çalışır (örn. biri backend, biri frontend veya biri ürünler, biri siparişler)
- Git branch mantığı:
  - `main` (veya `master`): Daima çalışan, test edilmiş kod
  - `feature/xxx`: Yeni özellikler için kısa ömürlü dallar
- Sık commit ve push yapın, açıklayıcı commit mesajları kullanın
- Merge/pull request ile kodu ana dala alın

## 4. Kod Gözden Geçirme ve İletişim

- Kısa kod gözden geçirme (code review) oturumları yapın
- Slack, Discord, WhatsApp gibi araçlarla iletişimde kalın
- Karşılaşılan sorunları ve çözümleri belgeleyin

---

Bir sonraki oturumda: Tam işlevli uygulamanın tamamlanması, test, deploy ve sunum hazırlığı.
