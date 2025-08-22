# Gerekli kütüphaneleri içe aktar
import requests    # HTTP istekleri için
import random      # Rastgele sayı üretmek için
from datetime import datetime, timedelta  # Tarih işlemleri için
import time        # Bekleme süresi eklemek için

# Toplam kaç istek gönderilecek?
toplam_cagri = 100

# API endpoint URL'si
url = "http://localhost/api/veriler"

# 100 kez döngü çalıştır
for i in range(toplam_cagri):
    
    # Rastgele veri seti oluştur
    veri = {
        "lokasyonID": random.randint(1, 5),           # 1-5 arası tam sayı
        "deger1": random.randint(10, 30),             # 10-30 arası tam sayı  
        "deger2": random.randint(31, 70),             # 31-70 arası tam sayı
        "deger3": random.randint(71, 99),             # 71-99 arası tam sayı
        "tarihsaat": (datetime.now() - timedelta(hours=random.randint(0, 720))
                     ).strftime("%Y-%m-%d %H:%M:%S")  # Son 30 gün içinde rastgele zaman
    }
    
    try:
        # POST isteği gönder (JSON formatında)
        response = requests.post(url, json=veri)
        
        # Sonucu kontrol et ve yazdır
        print(f"{i+1}. istek - Durum: {response.status_code}")
        if response.status_code != 200:
            print(f"Hata: {response.text}")
            
    except requests.exceptions.RequestException as e:
        # Ağ hatası durumunda
        print(f"{i+1}. istekte hata: {e}")
    
    # İstekler arasında 0.1 saniye bekle (sunucuyu korumak için)
    time.sleep(0.1)

print("Tüm istekler tamamlandı!")