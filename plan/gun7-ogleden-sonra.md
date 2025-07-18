# Gün 7 - Öğleden Sonra Oturumu: Quasar ile REST API ve CRUD SPA

## 1. Axios ile REST API Bağlantısı

### 1.1 Axios Kurulumu

```bash
npm install axios
```

### 1.2 API Servisi Oluşturma

`src/services/api.js`:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Laravel API adresiniz
  timeout: 5000,
});

export default api;
```

## 2. Pinia ile State Yönetimi

`src/stores/urun.js`:

```javascript
import { defineStore } from 'pinia';
import api from '@/services/api';

export const useUrunStore = defineStore('urun', {
  state: () => ({
    urunler: [],
    loading: false,
    hata: null,
  }),
  actions: {
    async urunleriYukle() {
      this.loading = true;
      try {
        const res = await api.get('/urunler');
        this.urunler = res.data;
      } catch (e) {
        this.hata = e.message;
      } finally {
        this.loading = false;
      }
    },
    async urunEkle(yeniUrun) {
      try {
        const res = await api.post('/urunler', yeniUrun);
        this.urunler.push(res.data);
      } catch (e) {
        this.hata = e.message;
      }
    },
    async urunGuncelle(id, guncelUrun) {
      try {
        const res = await api.put(`/urunler/${id}`, guncelUrun);
        const index = this.urunler.findIndex((u) => u.id === id);
        if (index !== -1) this.urunler[index] = res.data;
      } catch (e) {
        this.hata = e.message;
      }
    },
    async urunSil(id) {
      try {
        await api.delete(`/urunler/${id}`);
        this.urunler = this.urunler.filter((u) => u.id !== id);
      } catch (e) {
        this.hata = e.message;
      }
    },
  },
});
```

## 3. Router ile Sayfa Geçişleri

`src/router/routes.js`:

```javascript
export default [
  {
    path: '/',
    component: () => import('pages/UrunListesi.vue'),
  },
  {
    path: '/urun/:id',
    component: () => import('pages/UrunDetay.vue'),
    props: true,
  },
];
```

## 4. CRUD Operasyonları: Tam Entegre SPA

### 4.1 Ürün Ekleme, Güncelleme ve Silme

`src/pages/UrunListesi.vue` (devamı):

```vue
<template>
  <q-page class="q-pa-md">
    <q-table
      :rows="urunler"
      :columns="kolonlar"
      row-key="id"
      :loading="loading"
      @row-click="detayGoster"
    />
    <q-btn
      label="Yeni Ürün Ekle"
      color="primary"
      @click="dialogAc = true"
      class="q-mt-md"
    />
    <urun-dialog v-model="dialogAc" @kaydet="yeniUrunEkle" />
    <q-dialog v-model="silDialogAc">
      <q-card>
        <q-card-section>Ürünü silmek istediğinize emin misiniz?</q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Vazgeç" v-close-popup />
          <q-btn color="negative" label="Sil" @click="urunuSil" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useUrunStore } from '@/stores/urun';
import UrunDialog from '@/components/UrunDialog.vue';

const urunStore = useUrunStore();
const urunler = ref([]);
const loading = ref(false);
const dialogAc = ref(false);
const silDialogAc = ref(false);
const seciliUrunId = ref(null);

const kolonlar = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'isim', label: 'Ürün Adı', field: 'isim', align: 'left' },
  { name: 'fiyat', label: 'Fiyat', field: 'fiyat', align: 'right' },
  { name: 'islemler', label: 'İşlemler', field: 'islemler', align: 'center' },
];

onMounted(async () => {
  loading.value = true;
  await urunStore.urunleriYukle();
  urunler.value = urunStore.urunler;
  loading.value = false;
});

function detayGoster(row) {
  // Ürün detaylarını gösteren dialog açılabilir
}

function yeniUrunEkle(yeniUrun) {
  urunStore.urunEkle(yeniUrun);
  urunler.value = urunStore.urunler;
}

function silUrun(row) {
  seciliUrunId.value = row.id;
  silDialogAc.value = true;
}

async function urunuSil() {
  await urunStore.urunSil(seciliUrunId.value);
  urunler.value = urunStore.urunler;
  silDialogAc.value = false;
}
</script>
```

---

## 5. Ödev

1. CRUD işlemlerini tamamlayın: ürün ekleme, güncelleme, silme ve listeleme.
2. API bağlantı hatalarını yakalayın ve kullanıcıya bildirim gösterin.
3. Uygulamanızı Quasar'ın farklı temalarıyla deneyin.

## 6. Yararlı Kaynaklar

- [Quasar Framework](https://quasar.dev/)
- [Pinia](https://pinia.vuejs.org/)
- [Axios](https://axios-http.com/)
- [Vue Router](https://router.vuejs.org/)

---

Bir sonraki derste proje geliştirme ve sunum hazırlığı yapılacaktır.
