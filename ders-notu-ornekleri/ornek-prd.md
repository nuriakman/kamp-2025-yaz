# Örnek PRD (Product Requirements Document)

## 1. Proje Adı

E-Ticaret Ürün Yönetim Sistemi

## 2. Amaç ve Kapsam

Küçük ve orta ölçekli işletmelerin ürünlerini kolayca yönetebileceği, kullanıcı dostu bir web uygulaması geliştirmek. Kullanıcılar ürün ekleyip düzenleyebilecek, stok takibi yapabilecek ve siparişleri görebilecek.

## 3. Hedef Kullanıcılar

- İşletme sahipleri
- Mağaza yöneticileri
- Satış personeli

## 4. Temel Özellikler

- Kullanıcı kayıt ve giriş (authentication)
- Ürün ekleme, düzenleme, silme
- Ürün listesi ve detay görüntüleme
- Stok miktarı takibi
- Siparişlerin görüntülenmesi
- Basit raporlama (toplam ürün, stokta azalan ürünler)

## 5. Kullanıcı Rolleri ve Yetkiler

- **Yönetici:** Tüm işlemleri yapabilir (CRUD, rapor, kullanıcı yönetimi)
- **Personel:** Sadece ürün ve stok işlemleri yapabilir

## 6. Kullanıcı Akışları (User Flows)

- Kayıt/Giriş → Ana Sayfa → Ürün Listesi → Ürün Ekle/Düzenle/Sil
- Ürün Listesi → Ürün Detay → Stok Güncelle
- Siparişler → Sipariş Detay

## 7. Temel Ekranlar ve UI

- Giriş/Kayıt ekranı
- Ana sayfa (özet bilgiler)
- Ürün listesi (QTable)
- Ürün ekle/güncelle (QDialog, QInput)
- Sipariş listesi
- Sipariş detay
- Rapor ekranı

## 8. Veri Modeli ve API Tasarımı

### 8.1 Temel Tablolar

- **kullanicilar:** id, ad, email, sifre, rol
- **urunler:** id, isim, aciklama, fiyat, stok, kategori_id
- **kategoriler:** id, ad
- **siparisler:** id, kullanici_id, tarih, toplam_tutar
- **siparis_urunleri:** id, siparis_id, urun_id, miktar, birim_fiyat

### 8.2 Örnek API Endpointleri

- `POST   /api/login`
- `POST   /api/register`
- `GET    /api/urunler`
- `POST   /api/urunler`
- `PUT    /api/urunler/{id}`
- `DELETE /api/urunler/{id}`
- `GET    /api/siparisler`
- `GET    /api/siparisler/{id}`

## 9. Gereksinimler

- Laravel 10+ (backend)
- MySQL (veritabanı)
- Vue.js 3 + Quasar (frontend)
- Pinia (state yönetimi)
- Axios (API iletişimi)

## 10. Başarı Kriterleri

- Tüm ana akışlar sorunsuz çalışmalı
- 5 farklı ürünle demo yapılabilmeli
- Her kullanıcı kendi verilerine erişebilmeli

---

Bu PRD örneği, grup projesi için temel bir çerçeve sunar. Kendi projenize göre başlık ve içerikleri güncelleyebilirsiniz.
