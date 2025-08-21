# Laragon ile Kurulum

Windows 11 + Laragon ortamında Laravel + Quasar workflow için işlemler

---

## 🔹 1. Laragon Kurulumu

1. [Laragon indir](https://laragon.org/download/) → **Full** versiyon.
2. Kurulumda `C:\laragon` seç.
3. Laragon başlat → “Start All” de → Apache, MySQL, Node.js hazır.

---

## 🔹 2. Laravel Kurulumu

- Terminal aç (Laragon → Menu → Terminal).
- Proje dizinine git:

  ```bash
  cd C:\laragon\www
  ```

- Laravel projesi oluştur:

  ```bash
  composer create-project laravel/laravel backend
  ```

- URL otomatik olur: `http://backend.test`

---

## 🔹 3. Quasar (Vue + Quasar Framework) Kurulumu

- Node.js Laragon’a kurulu değilse: **Menu → Tools → Quick Add → Node.js**
- Quasar projesi oluştur:

  ```bash
  cd C:\laragon\www
  npm create quasar@latest frontend
  ```

- Çalıştır:

  ```bash
  cd frontend
  quasar dev
  ```

- Quasar dev server açılır: `http://localhost:9000`

---

## 🔹 4. API – Frontend Bağlantısı

- Laravel API: `http://backend.test/api/...`
- Quasar frontend’den Axios ile çağır:
  `frontend/src/boot/axios.js` içine:

  ```js
  import { boot } from 'quasar/wrappers';
  import axios from 'axios';

  const api = axios.create({ baseURL: 'http://backend.test/api' });

  export default boot(({ app }) => {
    app.config.globalProperties.$api = api;
  });

  export { api };
  ```

---

## 🔹 5. Development Workflow

- **Laravel backend**: `php artisan serve` (gerekirse) → `http://backend.test`
- **Quasar frontend**: `quasar dev` → `http://localhost:9000`
- İkisi ayrı çalışır ama API çağrıları backend’e gider.

---

## 🔹 6. Production Workflow

1. Quasar’ı build et:

   ```bash
   cd frontend
   quasar build
   ```

2. Çıktı: `frontend/dist/spa`
3. Bu klasörü → `backend/public` içine kopyala.
4. Artık **Laravel + Quasar tek domain** altında çalışır (`http://backend.test`).

---

💡 **Sonuç:**

- **Geliştirme aşaması** → ayrı server (Laravel + Quasar dev).
- **Canlıya geçiş** → Quasar build’i Laravel `public` içine kopyalanır.
