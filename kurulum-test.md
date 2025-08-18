# Kurulumların test edilmesi

```bash
php --version
composer --version
node --version
npm --version
quasar --version
git --version
git --global
git config --global --list
gemini --version
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
