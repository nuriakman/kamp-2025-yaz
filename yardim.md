# Yardım

## Genel Komutlar

```bash
# Sunucuyu başlat
php artisan serve

# Başlatılmış tüm sunucuları kapat
pkill -f "php artisan serve"

# Bu portu hangi uygulama kullanyor?
lsof -i :80 | grep "LISTEN"

# 8000 nolu portu dinleyen uygulamalar hangileridir?
netstat -tulpn|grep "8000"

# laravel sunucusuna localhost dışından da bağlanabilme
php artisan serve --host=0.0.0.0 --port=8000

# PHP dosyalarında yazım hatası (syntax error) var mı? kontrolü
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```

## `php artisan tinker` Komutları

Terminalde `php artisan tinker` içinde yapılabilecekler için örnekler:

```php
// Modelin tüm kayıtlarını getirme
$stations = App\Models\ModelAdi::all();
$stations->count(); // Kayıt sayısını kontrol et

// Belirli bir kaydı getirme
$station = App\Models\ModelAdi::find(1);
$station->toArray(); // Veriyi görüntüle

// Yeni kayıt oluşturma testi
$newStation = App\Models\ModelAdi::create([
    'adi' => 'Test İstasyonu',
    'kodu' => 'TEST001',
    // Diğer gerekli alanlar
]);
$newStation->save();

// Kayıt güncelleme testi
$station = App\Models\ModelAdi::first();
$station->adi = 'Güncellenmiş İsim';
$station->save();

// Kayıt silme testi
$station = App\Models\ModelAdi::where('kodu', 'TEST001')->first();
$station?->delete();
```
