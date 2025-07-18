# Gün 7 - Sabah Oturumu: Quasar Framework ile SPA Geliştirme (Bölüm 1)

## 1. Node.js ve NPM Kurulumu

- [Node.js İndir](https://nodejs.org/)
- Kurulum sonrası terminalde sürüm kontrolü:

```bash
node -v
npm -v
```

## 2. Quasar Framework Kurulumu

- Quasar CLI'yı global olarak yükleyin:

```bash
npm install -g @quasar/cli
```

- Quasar CLI sürümünü kontrol edin:

```bash
quasar -v
```

## 3. Quasar Projesi Oluşturma

```bash
quasar create quasar-urun-yonetimi
cd quasar-urun-yonetimi
quasar dev
```

- Kurulum sırasında önerilen ayarları seçin (Vue 3, Composition API, Pinia, Router, Axios, ESLint, Prettier).

## 4. Proje Dosya Yapısı ve Temel Ayarlar

- `src/pages/` altında ana sayfa ve ürün listesi sayfası oluşturun.
- `src/components/` altında QTable, QDialog, QInput örnekleri için bileşenler oluşturun.
- `src/stores/` altında Pinia store dosyası oluşturun.

## 5. Quasar Temel Bileşenleri ile Arayüz Tasarımı

### 5.1 QTable ile Ürün Listesi

`src/pages/UrunListesi.vue`:

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

const kolonlar = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'isim', label: 'Ürün Adı', field: 'isim', align: 'left' },
  { name: 'fiyat', label: 'Fiyat', field: 'fiyat', align: 'right' },
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
</script>
```

### 5.2 QDialog ve QInput ile Ürün Ekleme

`src/components/UrunDialog.vue`:

```vue
<template>
  <q-dialog v-model="modelValue">
    <q-card>
      <q-card-section>
        <div class="text-h6">Yeni Ürün</div>
      </q-card-section>
      <q-card-section>
        <q-input v-model="isim" label="Ürün Adı" />
        <q-input v-model.number="fiyat" label="Fiyat" type="number" />
      </q-card-section>
      <q-card-actions align="right">
        <q-btn flat label="İptal" v-close-popup />
        <q-btn color="primary" label="Kaydet" @click="kaydet" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, defineEmits, defineProps, watch } from 'vue';

const props = defineProps({
  modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue', 'kaydet']);
const isim = ref('');
const fiyat = ref(0);

watch(
  () => props.modelValue,
  (val) => {
    if (!val) {
      isim.value = '';
      fiyat.value = 0;
    }
  },
);

function kaydet() {
  emit('kaydet', { isim: isim.value, fiyat: fiyat.value });
  emit('update:modelValue', false);
}
</script>
```

---

Devamı öğleden sonra oturumunda: Axios ile REST API bağlantısı, Pinia ile state yönetimi, router ile sayfa geçişleri ve CRUD işlemleri.
