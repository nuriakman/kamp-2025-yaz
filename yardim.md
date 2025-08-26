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

# Mindmap


### **Git**

  - git init
  - git add
  - git commit
  - git push
  - git pull
  - git checkout
  - .gitignore
  - repository
  - Git Bash
  - GitHub
  - GitLab
### **Linux / Terminal Komutları**

  - ls, ll
  - cd
  - pwd
  - mkdir
  - rm -rf \*
  - cat, cat id\_, cat …pub
  - sudo
  - apt, apt-get
  - Ubuntu home klasörüne gitme komutu
  - ssh klasörüne gitme komutu
### **SSH**

  - ssh
  - ssh-keygen
  - \~/.ssh dizini
### **Veritabanı / SQL**

  - MySQL
  - MariaDB
  - SQLite
  - Oracle
  - MySQL-MariaDB farkı
  - SQL sorguları (select, update, delete, order by)
  - Veri türleri: varchar, int, float, decimal, datetime
  - ASC / DESC
  - index
  - CRUD işlemleri
  - migration
  - timestamps
  - fillable
  - \$table
  - nullable
### **API**
  - API (temel kavram)
  - REST API
  - SOAP API
  - WS
  - WebSocket API
  - CRUD (Create, Read, Update, Delete)
  - request-response
  - HTTP verbs: GET, POST, PUT, PATCH, DELETE
  - JSON
  - CSV, TSV
  - encoding
  - .env dosyası
  - Todos (örnek API verisi)
### **Frameworkler / Teknolojiler**

  - Laravel (composer create-project, artisan, model, controller, route, migration)
  - Node.js
  - npm
  - composer
  - paket yöneticileri (npm veya diğerleri)
  - framework kavramı
  - bağımlılık yönetimi
### **DNS / Ağ**

  - DNS
  - TLD
  - root DNS
  - ICANN
  - TTL (Time To Live)
  - IP adresleri: 1.1.1.1, 1.1.1.2
### **Programlama Dilleri**

  - JavaScript (fetch, JS fetch komutu)
  - TypeScript (TS vs JS)
  - PHP (Laravel)
### **Web Sunucu / Servisler**

  - Apache
  - Nginx
  - Laragon
  - Adminer
### **Araçlar / Eklentiler**

  - VS Code eklentileri
  - Wakatime
  - Windsurf
  - Vibe Coding
  - Stackedit.io
  - Mermaid
  - Markdown
  - Monotype font
  - killedbygoogle


