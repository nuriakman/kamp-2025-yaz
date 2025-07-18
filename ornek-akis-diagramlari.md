# Örnek Akış Diyagramları (Mermaid)

Bu dosyada, [ornek-prd.md](ornek-prd.md) dokümanındaki temel ekranlar ve önemli kullanıcı akışları için mermaid ile hazırlanmış akış diyagramları yer almaktadır.

---

## 1. Kullanıcı Giriş/Kayıt Akışı

```mermaid
flowchart TD
    GirisEkrani([Giriş / Kayıt Ekranı])
    GirisKontrol{Bilgiler doğru mu?}
    AnaSayfa([Ana Sayfa])
    Hata([Hata Mesajı])
    KayitOl([Kayıt Ol Ekranı])

    GirisEkrani -->|Giriş| GirisKontrol
    GirisEkrani -->|Kayıt Ol| KayitOl
    GirisKontrol -- Evet --> AnaSayfa
    GirisKontrol -- Hayır --> Hata
    Hata --> GirisEkrani
    KayitOl --> GirisKontrol
```

---

## 2. Ürün Yönetimi Akışı

```mermaid
flowchart TD
    AnaSayfa([Ana Sayfa])
    UrunListesi([Ürün Listesi])
    UrunDetay([Ürün Detay])
    UrunEkle([Ürün Ekle])
    UrunGuncelle([Ürün Güncelle])
    UrunSil([Ürün Sil])

    AnaSayfa --> UrunListesi
    UrunListesi -->|Detay| UrunDetay
    UrunListesi -->|Ekle| UrunEkle
    UrunDetay -->|Güncelle| UrunGuncelle
    UrunDetay -->|Sil| UrunSil
    UrunEkle --> UrunListesi
    UrunGuncelle --> UrunListesi
    UrunSil --> UrunListesi
```

---

## 3. Sipariş Yönetimi Akışı

```mermaid
flowchart TD
    AnaSayfa([Ana Sayfa])
    SiparisListesi([Sipariş Listesi])
    SiparisDetay([Sipariş Detay])
    SiparisOlustur([Sipariş Oluştur])

    AnaSayfa --> SiparisListesi
    SiparisListesi -->|Detay| SiparisDetay
    SiparisListesi -->|Yeni Sipariş| SiparisOlustur
    SiparisOlustur --> SiparisListesi
```

---

## 4. Stok Azalma ve Bildirim Akışı

```mermaid
flowchart TD
    UrunEkleGuncelle([Ürün Ekle / Güncelle])
    StokKontrol{Stok < Kritik Seviye?}
    Bildirim([Yöneticiye Bildirim Gönder])
    UrunListesi([Ürün Listesi])

    UrunEkleGuncelle --> StokKontrol
    StokKontrol --Evet--> Bildirim
    StokKontrol --Hayır--> UrunListesi
    Bildirim --> UrunListesi
```

---

## 5. Raporlama Akışı

```mermaid
flowchart TD
    AnaSayfa([Ana Sayfa])
    RaporEkrani([Rapor Ekranı])
    RaporSecimi{Rapor Türü Seçildi mi?}
    RaporGoster([Rapor Sonucu])

    AnaSayfa --> RaporEkrani
    RaporEkrani --> RaporSecimi
    RaporSecimi --Evet--> RaporGoster
    RaporSecimi --Hayır--> RaporEkrani
    RaporGoster --> RaporEkrani
```

---

Her diyagram, PRD'deki ilgili ekran veya iş akışı için temel kullanıcı yolculuğunu ve karar noktalarını özetler. Gerekirse daha fazla akış ekleyebilirim.
