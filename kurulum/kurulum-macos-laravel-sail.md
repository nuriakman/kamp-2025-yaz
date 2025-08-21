# Laravel Sail Kullanımı

## ÖNEMLİ

`php artisan` komutları yerine `./vendor/bin/sail artisan` kullanılacak

```text
php artisan migrate       → ./vendor/bin/sail artisan migrate
php artisan tinker        → ./vendor/bin/sail artisan tinker
php artisan queue:work    → ./vendor/bin/sail artisan queue:work
php artisan route:list    → ./vendor/bin/sail artisan route:list
```

## HAZIRLIK

Sıfır bir macOS üzerinde Laravel + Sail + Node.js + Quasar + Vue ortamı için hazırlık komutları:

**Hızlı Kurulum** komutları:

```bash
# 1️⃣ Homebrew kurulumu
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"

# 2️⃣ Git kurulumu
brew install git

# 3️⃣ PHP kurulumu
brew install php
php -v

# 4️⃣ Composer kurulumu
brew install composer
composer -V

# 5️⃣ Docker Desktop
# Manuel: https://www.docker.com/products/docker-desktop
# Kontrol
docker --version
docker compose version

# 6️⃣ Node.js / npm (opsiyonel, host makine için)
brew install node
node -v
npm -v

# 7️⃣ Laravel projeleri için home klasörü
mkdir -p ~/www

# 8️⃣ /etc/hosts kontrol (domain eklemek için)
sudo nano /etc/hosts
# Örnek satır: 127.0.0.1 proje1.test
```

## MacOS Altında Laravel Sail Kurulumu

```bash
#!/bin/bash
set -e

# DOSYA ADI: larave-sail-ilk-kurulum.sh

echo "🔧 Gerekli ortam hazırlanıyor..."

# -----------------------------
# Homebrew güncelle
# -----------------------------
if ! command -v brew &>/dev/null
then
    echo "🍺 Homebrew yüklü değil. Lütfen önce https://brew.sh üzerinden yükle."
    exit 1
fi
brew update
brew upgrade

# -----------------------------
# PHP + MariaDB + Composer
# -----------------------------
echo "🔧 PHP ve MariaDB kuruluyor..."
for pkg in php mariadb composer; do
    if brew list $pkg &>/dev/null; then
        echo "✅ $pkg zaten kurulu."
    else
        brew install $pkg
    fi
done

# Redis (PECL)
if ! pecl list | grep -q redis; then
    pecl install redis
else
    echo "✅ redis PECL zaten kurulu."
fi

# -----------------------------
# Node.js + NVM
# -----------------------------
echo "🔧 Node.js (NVM üzerinden) kuruluyor..."
if [ ! -d "$HOME/.nvm" ]; then
  brew install nvm
  mkdir -p ~/.nvm
fi

export NVM_DIR="$HOME/.nvm"
[ -s "/opt/homebrew/opt/nvm/nvm.sh" ] && \. "/opt/homebrew/opt/nvm/nvm.sh"
[ -s "/opt/homebrew/opt/nvm/etc/bash_completion.d/nvm" ] && \. "/opt/homebrew/opt/nvm/etc/bash_completion.d/nvm"

# Node LTS yükle ve kullan
if ! nvm ls | grep -q "lts"; then
    nvm install --lts
fi
nvm use --lts

# -----------------------------
# Quasar CLI
# -----------------------------
if ! command -v quasar &>/dev/null; then
    echo "🔧 Quasar CLI kuruluyor..."
    npm install -g @quasar/cli
else
    echo "✅ Quasar CLI zaten kurulu."
fi

# -----------------------------
# Vue CLI
# -----------------------------
if ! command -v vue &>/dev/null; then
    echo "🔧 Vue CLI kuruluyor..."
    npm install -g @vue/cli
else
    echo "✅ Vue CLI zaten kurulu."
fi

# -----------------------------
# Apache kurulumu
# -----------------------------
if ! brew list httpd &>/dev/null; then
    echo "🔧 Apache kuruluyor..."
    brew install httpd
fi
brew services start httpd

# -----------------------------
# Laravel home klasörü
# -----------------------------
LARAVEL_HOME="$HOME/www"
mkdir -p "$LARAVEL_HOME"
echo "🔹 Laravel projeleri için home klasörü: $LARAVEL_HOME"

# Apache için özel conf dosyası oluştur
APACHE_EXTRA_CONF="/opt/homebrew/etc/httpd/extra/laravel.conf"
mkdir -p "$(dirname "$APACHE_EXTRA_CONF")"

cat > "$APACHE_EXTRA_CONF" <<EOL
DocumentRoot "$LARAVEL_HOME"

<Directory "$LARAVEL_HOME">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOL

# Apache conf dosyasına include ekle (sadece eklenmemişse)
if ! grep -q "laravel.conf" /opt/homebrew/etc/httpd/httpd.conf; then
    echo "Include /opt/homebrew/etc/httpd/extra/laravel.conf" >> /opt/homebrew/etc/httpd/httpd.conf
fi

brew services restart httpd
echo "🔹 Apache DocumentRoot $LARAVEL_HOME olarak ayarlandı."

# -----------------------------
# MariaDB ve Redis başlat
# -----------------------------
brew services start mariadb
brew services start redis

echo "⚠️ MariaDB root şifresi ayarlanmadı. Manuel olarak ayarlayın:"
echo "   mysql -u root"
echo "   ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';"
echo "   FLUSH PRIVILEGES;"

# -----------------------------
# Kurulum tamamlandı
# -----------------------------
echo "✅ Kurulum tamamlandı!"
echo "➡️ Laravel: composer create-project laravel/laravel myapp"
echo "➡️ Quasar: quasar create myquasarapp"
echo "➡️ Vue.js: vue create myvueapp"
echo "➡️ Apache servisi: brew services start httpd"
echo "➡️ Redis servisi: brew services start redis"
echo "➡️ Laravel projeleri için home klasörü: $HOME/www"

# -----------------------------
# Apache extra conf açıklaması
echo ""
echo "🔹 Apache Extra Config Dosyası:"
echo "   Dosya: $APACHE_EXTRA_CONF"
echo "   İçerik: Laravel home klasörü $LARAVEL_HOME için DocumentRoot ve <Directory> izinleri ayarlanmıştır."
echo "   httpd.conf içine include edildiği için Apache bu ayarları otomatik olarak kullanacaktır."
echo ""

# Servis kontrol ve yönetim
echo ""
echo "🔹 Servisleri kontrol etmek için:"
echo "brew services list | grep httpd"
echo "brew services list | grep mariadb"
echo "brew services list | grep redis"
echo ""
echo "🔹 Servisleri başlatmak için:"
echo "brew services start httpd"
echo "brew services start mariadb"
echo "brew services start redis"
echo ""
echo "🔹 Servisleri durdurmak için:"
echo "brew services stop httpd"
echo "brew services stop mariadb"
echo "brew services stop redis"

```

## Laravel Sail ile Yeni Proje Başlatma ve host ekleme

```bash
#!/bin/bash
set -e

# yeni-laravel-sail-projesi.sh

# -----------------------------
# Kullanıcıdan proje adı ve domain al
# -----------------------------
read -p "Proje adı (ör: proje1): " PROJECT_NAME
read -p "Domain adı (ör: proje1.test): " DOMAIN_NAME

# Laravel home klasörü
LARAVEL_HOME="$HOME/www"
PROJECT_DIR="$LARAVEL_HOME/$PROJECT_NAME"

# -----------------------------
# Laravel projesini oluştur
# -----------------------------
mkdir -p "$LARAVEL_HOME"
cd "$LARAVEL_HOME"

if [ -d "$PROJECT_DIR" ]; then
    echo "⚠️ $PROJECT_NAME zaten mevcut."
else
    echo "📦 Laravel projesi oluşturuluyor: $PROJECT_NAME"
    composer create-project laravel/laravel "$PROJECT_NAME"
fi

# -----------------------------
# Apache VirtualHost ayarları
# -----------------------------
APACHE_CONF="/opt/homebrew/etc/httpd/extra/laravel.conf"

mkdir -p "$(dirname "$APACHE_CONF")"

echo "🔧 Apache VirtualHost ekleniyor: $DOMAIN_NAME"

if ! grep -q "$DOMAIN_NAME" "$APACHE_CONF"; then
    cat >> "$APACHE_CONF" <<EOL
<VirtualHost *:80>
    ServerName $DOMAIN_NAME
    DocumentRoot "$PROJECT_DIR/public"

    <Directory "$PROJECT_DIR/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "/usr/local/var/log/apache2/${PROJECT_NAME}-error.log"
    CustomLog "/usr/local/var/log/apache2/${PROJECT_NAME}-access.log" common
</VirtualHost>
EOL
    echo "✅ VirtualHost eklendi."
else
    echo "⚠️ $DOMAIN_NAME zaten Apache conf dosyasında mevcut."
fi

# -----------------------------
# /etc/hosts güncelle
# -----------------------------
if ! grep -q "$DOMAIN_NAME" /etc/hosts; then
    echo "127.0.0.1   $DOMAIN_NAME" | sudo tee -a /etc/hosts
    echo "✅ /etc/hosts güncellendi."
else
    echo "⚠️ /etc/hosts zaten güncel."
fi

# -----------------------------
# Apache yeniden başlat
# -----------------------------
brew services restart httpd
echo "✅ Apache yeniden başlatıldı."

echo "🎉 Kurulum tamamlandı!"
echo "📂 Proje dizini: $PROJECT_DIR"
echo "🌐 Tarayıcıda: http://$DOMAIN_NAME"

```

## MySQl Root Password Change

```bash
brew services stop mariadb
sudo mysqld_safe --skip-grant-tables &
sleep 5
mysql -u root <<EOF
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'yeni_sifre';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF
mysqladmin -u admin -pyeni_sifre shutdown
brew services start mariadb
```

## brew ile yüklenen her şeyi temizleme

```bash
#!/bin/bash

# DOSYA ADI: brew-clean.sh

# Brew Temizleme Scripti
# Sadece Brew ile sınırlı temizlik işlemleri yapar

set -e

# Renkli çıktı fonksiyonları
red=$(tput setaf 1)
green=$(tput setaf 2)
yellow=$(tput setaf 3)
blue=$(tput setaf 4)
reset=$(tput sgr0)

print_step() {
    echo "${blue}[ADIM]${reset} $1"
}

print_success() {
    echo "${green}[BAŞARILI]${reset} $1"
}

print_warning() {
    echo "${yellow}[UYARI]${reset} $1"
}

print_error() {
    echo "${red}[HATA]${reset} $1"
}

# Brew'in kurulu olup olmadığını kontrol et
check_brew_installed() {
    if ! command -v brew &> /dev/null; then
        print_error "Brew kurulu görünmüyor. Script sonlandırılıyor."
        exit 1
    fi
}

# Onay iste
confirm_action() {
    local prompt="$1 (e/h): "
    read -p "$prompt" -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Ee]$ ]]; then
        echo "İşlem iptal edildi."
        exit 0
    fi
}

# Brew paketlerini temizle
clean_brew_packages() {
    print_step "Brew paketleri temizleniyor..."

    # Formülleri kaldır
    local formulas=$(brew list --formula -1)
    if [ -n "$formulas" ]; then
        echo "Kaldırılacak formüller:"
        echo "$formulas"
        confirm_action "Formülleri kaldırmak istiyor musunuz?"
        brew uninstall --force --ignore-dependencies $formulas
    else
        print_warning "Kaldırılacak formül bulunamadı"
    fi

    # Cask uygulamalarını kaldır
    local casks=$(brew list --cask -1)
    if [ -n "$casks" ]; then
        echo "Kaldırılacak cask uygulamaları:"
        echo "$casks"
        confirm_action "Cask uygulamalarını kaldırmak istiyor musunuz?"
        brew uninstall --cask --force $casks
    else
        print_warning "Kaldırılacak cask uygulaması bulunamadı"
    fi

    # Bağımlılıkları temizle
    print_step "Kullanılmayan bağımlılıklar temizleniyor..."
    brew autoremove

    # Önbelleği temizle
    print_step "Brew önbelleği temizleniyor..."
    brew cleanup --prune=all

    print_success "Brew paketleri temizlendi"
}

# Brew'i tamamen kaldır
uninstall_brew_completely() {
    print_step "Brew tamamen kaldırılıyor..."

    confirm_action "Brew'i tamamen kaldırmak istiyor musunuz? (Sadece paketler değil, Brew'in kendisi de kaldırılacak)"

    # Brew'i kaldır
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/uninstall.sh)"

    # Kalan dosyaları temizle
    print_step "Kalan Brew dosyaları temizleniyor..."
    sudo rm -rf /usr/local/Homebrew
    sudo rm -rf /usr/local/Caskroom
    sudo rm -rf /usr/local/Cellar
    sudo rm -rf /opt/homebrew
    sudo rm -rf /usr/local/var/homebrew
    sudo rm -f /usr/local/bin/brew

    # Kullanıcı dizinindeki Brew dosyalarını temizle
    rm -rf ~/.homebrew
    rm -rf ~/Library/Caches/Homebrew
    rm -rf ~/Library/Logs/Homebrew

    print_success "Brew tamamen kaldırıldı"
}

# Brew doktorunu çalıştır
run_brew_doctor() {
    print_step "Brew doktor çalıştırılıyor (sorun kontrolü)..."
    brew doctor
}

# Ana işlem
main() {
    clear
    echo "========================================"
    echo "        Brew Temizleme Scripti"
    echo "========================================"
    echo ""

    check_brew_installed

    # Seçenekler
    echo "Lütfen bir işlem seçin:"
    echo "1) Sadece Brew paketlerini temizle (Brew kurulu kalır)"
    echo "2) Brew'i tamamen kaldır (paketler ve Brew'in kendisi)"
    echo "3) Brew doktor çalıştır (sorunları kontrol et)"
    echo ""
    read -p "Seçiminiz (1-3): " choice

    case $choice in
        1)
            clean_brew_packages
            ;;
        2)
            clean_brew_packages
            uninstall_brew_completely
            ;;
        3)
            run_brew_doctor
            ;;
        *)
            print_error "Geçersiz seçim"
            exit 1
            ;;
    esac

    print_success "İşlem tamamlandı"
}

# Scripti çalıştır
main "$@"
```
