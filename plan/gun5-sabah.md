# Gün 5 - Sabah Oturumu: Vue.js Temelleri

## 1. Vue.js'e Giriş

### 1.1 Vue.js Nedir?

- İlerleyen (progressive) JavaScript framework'ü
- Reaktif ve bileşen tabanlı mimari
- Öğrenmesi kolay, entegrasyonu basit
- Virtual DOM kullanımı

### 1.2 Vue 3 Yenilikleri

- Composition API
- Daha iyi TypeScript desteği
- Daha küçük paket boyutu
- Daha iyi performans
- Fragment'ler
- Teleport

## 2. Vue Projesi Oluşturma

### 2.1 Vue CLI ile Proje Oluşturma

```bash
# Vue CLI kurulumu
npm install -g @vue/cli

# Yeni proje oluşturma
vue create e-ticaret-frontend

# Gerekli paketlerin kurulumu
cd e-ticet-frontend
npm install axios vue-router@4 pinia @vueuse/core
```

### 2.2 Vite ile Proje Oluşturma (Alternatif)

```bash
# Yeni proje oluşturma
npm create vite@latest e-ticaret-frontend -- --template vue

# Gerekli paketlerin kurulumu
cd e-ticaret-frontend
npm install
npm install axios vue-router@4 pinia @vueuse/core
```

## 3. Temel Vue Konseptleri

### 3.1 Uygulama Örneği

`src/App.vue`:

```vue
<template>
  <div class="app">
    <header>
      <h1>{{ siteBaslik }}</h1>
      <nav>
        <router-link to="/">Ana Sayfa</router-link> |
        <router-link to="/urunler">Ürünler</router-link> |
        <router-link to="/sepet">Sepet ({{ sepetUrunSayisi }})</router-link>
      </nav>
    </header>

    <main>
      <router-view />
    </main>

    <footer>
      <p>© 2025 E-Ticaret Uygulaması</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useSepetStore } from './stores/sepet';

// Reactive state
const siteBaslik = ref('E-Ticaret Mağazası');
const router = useRouter();
const sepetStore = useSepetStore();

// Computed property
const sepetUrunSayisi = computed(() => sepetStore.toplamUrunSayisi);

// Lifecycle hook
onMounted(() => {
  console.log('Uygulama başlatıldı');
});

// Method
const anaSayfayaGit = () => {
  router.push('/');
};
</script>

<style>
.app {
  font-family: Arial, sans-serif;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
  border-bottom: 1px solid #eee;
  margin-bottom: 30px;
}

nav a {
  margin: 0 10px;
  text-decoration: none;
  color: #2c3e50;
}

nav a.router-link-active {
  color: #42b983;
  font-weight: bold;
}

footer {
  margin-top: 50px;
  padding: 20px 0;
  border-top: 1px solid #eee;
  text-align: center;
  color: #666;
}
</style>
```

## 4. Vue Router

### 4.1 Router Yapılandırması

`src/router/index.js`:

```javascript
import { createRouter, createWebHistory } from 'vue-router';
import AnaSayfa from '../views/AnaSayfa.vue';
import UrunListesi from '../views/UrunListesi.vue';
import UrunDetay from '../views/UrunDetay.vue';
import Sepet from '../views/Sepet.vue';
import GirisYap from '../views/GirisYap.vue';
import KayitOl from '../views/KayitOl.vue';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/',
    name: 'AnaSayfa',
    component: AnaSayfa,
  },
  {
    path: '/urunler',
    name: 'UrunListesi',
    component: UrunListesi,
  },
  {
    path: '/urun/:id',
    name: 'UrunDetay',
    component: UrunDetay,
    props: true,
  },
  {
    path: '/sepet',
    name: 'Sepet',
    component: Sepet,
    meta: { requiresAuth: true },
  },
  {
    path: '/giris',
    name: 'GirisYap',
    component: GirisYap,
    meta: { guestOnly: true },
  },
  {
    path: '/kayit',
    name: 'KayitOl',
    component: KayitOl,
    meta: { guestOnly: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  },
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  // Sayfa kimlik doğrulama gerektiriyor mu?
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!authStore.isAuthenticated) {
      next({ name: 'GirisYap', query: { redirect: to.fullPath } });
    } else {
      next();
    }
  }
  // Sadece misafir kullanıcılar erişebilir mi?
  else if (to.matched.some((record) => record.meta.guestOnly)) {
    if (authStore.isAuthenticated) {
      next({ name: 'AnaSayfa' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
```

## 5. State Yönetimi (Pinia)

### 5.1 Store Oluşturma

`src/stores/sepet.js`:

```javascript
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';

export const useSepetStore = defineStore('sepet', () => {
  const toast = useToast();
  const urunler = ref([]);

  // Getters
  const toplamUrunSayisi = computed(() => {
    return urunler.value.reduce((toplam, urun) => toplam + urun.miktar, 0);
  });

  const toplamFiyat = computed(() => {
    return urunler.value.reduce((toplam, urun) => {
      return toplam + (urun.fiyat * urun.miktar);
    }, 0);
  });

  // Actions
  function sepeteEkle(urun, miktar = 1) {
    const index = urunler.value.findIndex(item => item.id === urun.id);

    if (index !== -1) {
      urunler.value[index].miktar += miktar;
      toast.success(`${urun.urun_adi} sepete eklendi (${urunler.value[index].miktar} adet)`);
    } else {
      urunler.value.push({
        id: urun.id,
        urun_adi: urun.urun_adi,
        fiyat: urun.indirimli_fiyat || urun.birim_fiyat,
        resim: urun.resim_yolu,
        miktar: miktar
      });
      toast.success(`${urun.urun_adi} sepete eklendi`);
    }

    // LocalStorage'a kaydet
    localStorage.setItem('sepet', JSON.stringify(urunler.value));
  }

  function sepettenCikar(urunId) {
    const index = urunler.value.findIndex(item => item.id === urunId);
    if (index !== -1) {
      const urunAdi = urunler.value[index].urun_adi;
      urunler.value.splice(index, 1);
      toast.warning(`${urunAdi} sepetten çıkarıldı`);

      // LocalStorage'ı güncelle
      localStorage.setItem('sepet', JSON.stringify(urunler.value));
    }
  }

  function miktarGuncelle(urunId, yeniMiktar) {
    const urun = urunler.value.find(item => item.id === urunId);
    if (urun) {
      urun.miktar = Math.max(1, yeniMiktar);

      // LocalStorage'ı güncelle
      localStorage.setItem('sepet', JSON.stringify(urunler.value));
    }
  }

  function sepetiBosalt() {
    urunler.value = [];
    localStorage.removeItem('sepet');
  }

  // LocalStorage'dan sepeti yükle
  function sepetiYukle() {
    const kayitliSepet = localStorage.getItem('sepet');n    if (kayitliSepet) {
      urunler.value = JSON.parse(kayitliSepet);
    }
  }

  return {
    urunler,
    toplamUrunSayisi,
    toplamFiyat,
    sepeteEkle,
    sepettenCikar,
    miktarGuncelle,
    sepetiBosalt,
    sepetiYukle
  };
});
```

## 6. API İletişimi (Axios)

### 6.1 API Servisi Oluşturma

`src/services/api.js`:

```javascript
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import router from '@/router';

const API_URL = 'http://localhost:8000/api/v1';

// Axios instance oluşturma
const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  timeout: 10000,
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();

    // Eğer kullanıcı giriş yapmışsa token'ı ekle
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response.data;
  },
  async (error) => {
    const originalRequest = error.config;
    const authStore = useAuthStore();

    // 401 Unauthorized hatası ve token yenileme işlemi
    if (
      error.response?.status === 401 &&
      !originalRequest._retry &&
      authStore.refreshToken
    ) {
      originalRequest._retry = true;

      try {
        // Token'ı yenile
        await authStore.refreshToken();

        // Orijinal isteği tekrar dene
        originalRequest.headers.Authorization = `Bearer ${authStore.token}`;
        return api(originalRequest);
      } catch (refreshError) {
        // Token yenileme başarısız oldu, çıkış yap
        await authStore.logout();
        router.push({
          name: 'GirisYap',
          query: { redirect: router.currentRoute.value.fullPath },
        });
        return Promise.reject(refreshError);
      }
    }

    // Diğer hatalar
    if (error.response?.data?.message) {
      error.message = error.response.data.message;
    }

    return Promise.reject(error);
  },
);

// API metodları
export default {
  // Auth
  login: (credentials) => api.post('/auth/login', credentials),
  register: (userData) => api.post('/auth/register', userData),
  logout: () => api.post('/auth/logout'),
  getProfile: () => api.get('/auth/me'),

  // Ürünler
  getUrunler: (params = {}) => api.get('/urunler', { params }),
  getUrun: (id) => api.get(`/urunler/${id}`),
  createUrun: (data) =>
    api.post('/urunler', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  updateUrun: (id, data) =>
    api.post(`/urunler/${id}`, data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  deleteUrun: (id) => api.delete(`/urunler/${id}`),

  // Kategoriler
  getKategoriler: () => api.get('/kategoriler'),

  // Siparişler
  getSiparisler: () => api.get('/siparisler'),
  createSiparis: (data) => api.post('/siparisler', data),
  getSiparis: (id) => api.get(`/siparisler/${id}`),
};
```

## 7. Ödev

1. Vue projesi oluşturun ve yukarıdaki yapıyı kurun
2. Ana sayfa, ürün listesi ve ürün detay sayfalarını oluşturun
3. Sepet işlevselliğini ekleyin
4. Kimlik doğrulama işlemlerini entegre edin
5. API ile iletişimi test edin

## 8. Yararlı Kaynaklar

- [Vue.js Resmi Dokümantasyonu](https://vuejs.org/)
- [Vue Router Dokümantasyonu](https://router.vuejs.org/)
- [Pinia Dokümantasyonu](https://pinia.vuejs.org/)
- [VueUse Kütüphanesi](https://vueuse.org/)
- [Axios Dokümantasyonu](https://axios-http.com/)

---

**Not:** Öğleden sonraki oturumda Quasar Framework kullanarak daha gelişmiş bir arayüz oluşturacağız ve API entegrasyonunu tamamlayacağız.
