# Kurulum

## Ubuntu için kurulum

### Genel Kurulumlar

```bash
# Sistemi güncelle
sudo apt update && sudo apt upgrade -y

# Gerekli paketler
sudo apt install -y git curl unzip php-cli php-mbstring php-xml composer mariadb-server

# NVM kurulumu
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc

# Node.js LTS kurulumu
nvm install --lts
nvm use --lts

# MariaDB servisini başlat
sudo systemctl enable mariadb
sudo systemctl start mariadb

# MariaDB root şifresini "root" yap
sudo mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'root'; FLUSH PRIVILEGES;"

# Laravel global kurulum
composer global require laravel/installer
echo 'export PATH="$HOME/.config/composer/vendor/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

# Vue CLI
npm install -g @vue/cli

# Quasar CLI kurulumu
npm install -g @quasar/cli

# Laravel global kurulum
composer global require laravel/installer
```

### Git Kurulumu ve Ayarları

```bash
# Git Kurulumu ve ayarları
sudo apt install git
git config --global user.name "Nuri Akman"
git config --global user.email "nuriakman@gmail.com"

# Public Key üretilmesi
ssh-keygen -t rsa -b 4096 -C "nuriakman@gmail.com"

# Github'a public key eklenecek
cat ~/.ssh/id_rsa.pub

```

### Kurulumların test edilmesi

```bash
php --version
composer --version
node --version
npm --version
quasar --version
git --version
git --global
git config --global --list
```

### Google Gemini Kurulumu

```bash
npm install -g @google/gemini-cli
```

## Laravel, VueJS ve Quasar Örnek Proje Başlatması

### Laravel Projesi kurulumu

```bash
composer create-project laravel/laravel my-laravel-project
cd my-laravel-project
php artisan migrate
php artisan serve
```

### VueJS Projesi kurulumu

```bash
npm create vue@latest my-vuejs-project
cd my-vuejs-project
npm install
npm run format
npm run dev

# Opsiyonel: Git ile başlatma
git init && git add -A && git commit -m "initial commit"
```

### Quasar Projesi kurulumu

```bash
npm init quasar@latest my-quasar-project
cd my-quasar-project
npm install
quasar dev
# veya npm run dev
```
