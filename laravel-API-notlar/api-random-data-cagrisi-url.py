import requests
import random
from datetime import datetime, timedelta
import time
from urllib.parse import urlencode

# Toplam çağrı sayısı
toplam_cagri = 100

# API endpoint
base_url = "http://localhost/api/veriler"

for i in range(toplam_cagri):
    # Rastgele verileri oluştur
    params = {
        "lokasyonID": random.randint(1, 5),
        "deger1": random.randint(10, 30),
        "deger2": random.randint(31, 70),
        "deger3": random.randint(71, 99),
        "tarihsaat": (datetime.now() - timedelta(hours=random.randint(0, 720))
                     ).strftime("%Y-%m-%d %H:%M:%S")
    }
    
    # URL parametrelerini ekle
    url = f"{base_url}?{urlencode(params)}"
    
    try:
        # GET isteği gönder (URL parametresi olarak)
        response = requests.get(url)
        # response = requests.post(url)  # --> POST ile göndermek için
        
        # Sonucu kontrol et
        print(f"{i+1}. istek - Durum: {response.status_code}")
        if response.status_code != 200:
            print(f"Hata: {response.text}")
            
    except requests.exceptions.RequestException as e:
        print(f"{i+1}. istekte hata: {e}")
    
    # İstekler arasında kısa bekleme
    time.sleep(0.1)

print("Tüm istekler tamamlandı!")