# Gün 6 - Öğleden Sonra Oturumu: İleri Düzey State Yönetimi ve Performans Optimizasyonu

## 1. İleri Düzey Pinia Kullanımı

### 1.1 Store'lar Arası İletişim

`src/stores/sepet.js`:

```javascript
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar';
import { useAuthStore } from './auth';
import { useUrunStore } from './urun';
import { useSiparisStore } from './siparis';

export const useSepetStore = defineStore('sepet', () => {
  const $q = useQuasar();
  const authStore = useAuthStore();
  const urunStore = useUrunStore();
  const siparisStore = useSiparisStore();

  const urunler = ref([]);
  const kuponKodu = ref('');
  const kuponIndirimi = ref(0);
  const kargoUcreti = ref(0);
  const odemeYontemi = ref('kredi_karti');
  const adres = ref(null);

  // Getters
  const toplamUrunSayisi = computed(() => {
    return urunler.value.reduce((toplam, urun) => toplam + urun.miktar, 0);
  });

  const araToplam = computed(() => {
    return urunler.value.reduce((toplam, urun) => {
      return toplam + urun.fiyat * urun.miktar;
    }, 0);
  });

  const toplamIndirim = computed(() => {
    return (
      urunler.value.reduce((toplam, urun) => {
        return toplam + (urun.originalFiyat - urun.fiyat) * urun.miktar;
      }, 0) + kuponIndirimi.value
    );
  });

  const toplamTutar = computed(() => {
    return Math.max(
      0,
      araToplam.value - kuponIndirimi.value + kargoUcreti.value,
    );
  });

  const sepetBosMu = computed(() => urunler.value.length === 0);

  // Actions
  function sepeteEkle(urun, miktar = 1) {
    if (!authStore.isAuthenticated) {
      $q.dialog({
        title: 'Giriş Yapın',
        message: 'Sepete ürün eklemek için giriş yapmalısınız.',
        cancel: true,
        persistent: true,
        ok: {
          label: 'Giriş Yap',
          color: 'primary',
          flat: false,
        },
        cancel: {
          label: 'Vazgeç',
          color: 'grey',
          flat: true,
        },
      }).onOk(() => {
        router.push({
          name: 'giris',
          query: { redirect: router.currentRoute.value.fullPath },
        });
      });
      return;
    }

    const index = urunler.value.findIndex((item) => item.id === urun.id);

    if (index !== -1) {
      // Ürün zaten sepette, miktarı güncelle
      urunler.value[index].miktar += miktar;

      // Stok kontrolü
      if (urunler.value[index].miktar > urunler.value[index].stok) {
        urunler.value[index].miktar = urunler.value[index].stok;
        $q.notify({
          type: 'warning',
          message: 'Maksimum stok adedine ulaşıldı',
          position: 'top-right',
        });
      }
    } else {
      // Yeni ürün ekle
      urunler.value.push({
        id: urun.id,
        urun_adi: urun.urun_adi,
        fiyat: urun.indirimli_fiyat || urun.birim_fiyat,
        originalFiyat: urun.birim_fiyat,
        resim: urun.resim_yolu,
        stok: urun.stok_miktari,
        miktar: Math.min(miktar, urun.stok_miktari),
        kdv_orani: urun.kdv_orani || 18,
        urun_kodu: urun.urun_kodu,
      });
    }

    // Yerel depolamaya kaydet
    localStorage.setItem('sepet', JSON.stringify(urunler.value));

    // Bildirim göster
    $q.notify({
      type: 'positive',
      message: `${urun.urun_adi} sepete eklendi`,
      icon: 'shopping_cart',
      position: 'top-right',
    });
  }

  function sepettenCikar(urunId) {
    const index = urunler.value.findIndex((item) => item.id === urunId);
    if (index !== -1) {
      const urunAdi = urunler.value[index].urun_adi;
      urunler.value.splice(index, 1);

      // Yerel depolamayı güncelle
      localStorage.setItem('sepet', JSON.stringify(urunler.value));

      $q.notify({
        type: 'warning',
        message: `${urunAdi} sepetinizden çıkarıldı`,
        position: 'top-right',
      });
    }
  }

  function miktarGuncelle(urunId, yeniMiktar) {
    const urun = urunler.value.find((item) => item.id === urunId);
    if (urun) {
      // Miktarı güncelle (minimum 1, maksimum stok adedi)
      urun.miktar = Math.max(1, Math.min(yeniMiktar, urun.stok));

      // Yerel depolamayı güncelle
      localStorage.setItem('sepet', JSON.stringify(urunler.value));
    }
  }

  function sepetiBosalt() {
    urunler.value = [];
    kuponKodu.value = '';
    kuponIndirimi.value = 0;
    localStorage.removeItem('sepet');
  }

  async function kuponUygula(kupon) {
    try {
      // API'den kupon doğrulaması yapılacak
      const response = await api.post('/kupon/dogrula', { kupon });

      kuponKodu.value = kupon;
      kuponIndirimi.value = response.indirim_tutari;

      $q.notify({
        type: 'positive',
        message: 'Kupon kodu başarıyla uygulandı',
        position: 'top-right',
      });

      return true;
    } catch (error) {
      console.error('Kupon uygulanırken hata oluştu:', error);

      $q.notify({
        type: 'negative',
        message: error.response?.data?.message || 'Geçersiz kupon kodu',
        position: 'top-right',
      });

      return false;
    }
  }

  async function odemeYap(odemeBilgileri) {
    try {
      // Sipariş verilerini hazırla
      const siparisVerileri = {
        urunler: urunler.value.map((urun) => ({
          urun_id: urun.id,
          miktar: urun.miktar,
          birim_fiyat: urun.fiyat,
          kdv_orani: urun.kdv_orani,
        })),
        kupon_kodu: kuponKodu.value || null,
        kargo_ucreti: kargoUcreti.value,
        toplam_tutar: toplamTutar.value,
        odeme_yontemi: odemeYontemi.value,
        adres: adres.value,
        ...odemeBilgileri,
      };

      // Sipariş oluştur
      const siparis = await siparisStore.siparisOlustur(siparisVerileri);

      // Ödeme işlemini başlat
      const odemeSonucu = await siparisStore.odemeYap(siparis.id, {
        odeme_yontemi: odemeYontemi.value,
        ...odemeBilgileri,
      });

      // Başarılı ödeme sonrası sepeti temizle
      sepetiBosalt();

      return {
        success: true,
        siparis,
        odeme: odemeSonucu,
      };
    } catch (error) {
      console.error('Ödeme işlemi sırasında hata oluştu:', error);

      $q.notify({
        type: 'negative',
        message:
          error.response?.data?.message ||
          'Ödeme işlemi sırasında bir hata oluştu',
        position: 'top',
      });

      return {
        success: false,
        error: error.response?.data || error.message,
      };
    }
  }

  // LocalStorage'dan sepeti yükle
  function sepetiYukle() {
    const kayitliSepet = localStorage.getItem('sepet');
    if (kayitliSepet) {
      urunler.value = JSON.parse(kayitliSepet);

      // Ürün bilgilerini güncelle
      urunler.value = urunler.value
        .map((urun) => {
          const guncelUrun =
            urunStore.urunler.find((u) => u.id === urun.id) || urun;
          return {
            ...urun,
            fiyat: guncelUrun.indirimli_fiyat || guncelUrun.birim_fiyat,
            originalFiyat: guncelUrun.birim_fiyat,
            stok: guncelUrun.stok_miktari,
            miktar: Math.min(
              urun.miktar,
              guncelUrun.stok_miktari || urun.miktar,
            ),
          };
        })
        .filter((urun) => urun.stok > 0); // Stokta olmayan ürünleri çıkar

      // Güncellenmiş sepeti kaydet
      localStorage.setItem('sepet', JSON.stringify(urunler.value));
    }
  }

  return {
    // State
    urunler,
    kuponKodu,
    kuponIndirimi,
    kargoUcreti,
    odemeYontemi,
    adres,

    // Getters
    toplamUrunSayisi,
    araToplam,
    toplamIndirim,
    toplamTutar,
    sepetBosMu,

    // Actions
    sepeteEkle,
    sepettenCikar,
    miktarGuncelle,
    sepetiBosalt,
    kuponUygula,
    odemeYap,
    sepetiYukle,
  };
});
```

## 2. Performans Optimizasyonları

### 2.1 Lazy Loading ve Code Splitting

`src/router/index.js`:

```javascript
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'AnaSayfa',
    component: () => import('pages/AnaSayfa.vue'),
  },
  {
    path: '/urunler',
    name: 'UrunListesi',
    component: () => import('pages/urun/UrunListesi.vue'),
  },
  {
    path: '/urun/:id',
    name: 'UrunDetay',
    component: () => import('pages/urun/UrunDetay.vue'),
    props: true,
  },
  {
    path: '/sepet',
    name: 'Sepet',
    component: () => import('pages/odeme/Sepet.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/odeme',
    name: 'Odeme',
    component: () => import('pages/odeme/Odeme.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/siparis/:id',
    name: 'SiparisDetay',
    component: () => import('pages/siparis/SiparisDetay.vue'),
    meta: { requiresAuth: true },
    props: true,
  },
  {
    path: '/profil',
    name: 'Profil',
    component: () => import('pages/kullanici/Profil.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: 'siparislerim',
        name: 'Siparislerim',
        component: () => import('pages/kullanici/Siparislerim.vue'),
      },
      {
        path: 'adreslerim',
        name: 'Adreslerim',
        component: () => import('pages/kullanici/Adreslerim.vue'),
      },
      {
        path: 'ayarlar',
        name: 'HesapAyarlari',
        component: () => import('pages/kullanici/HesapAyarlari.vue'),
      },
    ],
  },
  // Admin rotaları
  {
    path: '/yonetim',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'YonetimPaneli',
        component: () => import('pages/yonetim/Panel.vue'),
      },
      {
        path: 'urunler',
        name: 'YonetimUrunler',
        component: () => import('pages/yonetim/urun/Liste.vue'),
      },
      {
        path: 'urun/ekle',
        name: 'YonetimUrunEkle',
        component: () => import('pages/yonetim/urun/Form.vue'),
      },
      {
        path: 'urun/duzenle/:id',
        name: 'YonetimUrunDuzenle',
        component: () => import('pages/yonetim/urun/Form.vue'),
        props: true,
      },
      {
        path: 'siparisler',
        name: 'YonetimSiparisler',
        component: () => import('pages/yonetim/siparis/Liste.vue'),
      },
      {
        path: 'kullanicilar',
        name: 'YonetimKullanicilar',
        component: () => import('pages/yonetim/kullanici/Liste.vue'),
      },
      {
        path: 'istatistikler',
        name: 'YonetimIstatistikler',
        component: () => import('pages/yonetim/Istatistikler.vue'),
      },
    ],
  },
  // 404 sayfası
  {
    path: '/:pathMatch(.*)*',
    name: 'SayfaBulunamadi',
    component: () => import('pages/Hata404.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      };
    } else {
      return { top: 0, behavior: 'smooth' };
    }
  },
});

export default router;
```

### 2.2 Virtual Scrolling ile Büyük Listeleri Yönetme

`src/components/VirtualList.vue`:

```vue
<template>
  <div
    class="virtual-list"
    :style="{ height: `${height}px` }"
    @scroll.passive="handleScroll"
  >
    <div class="virtual-list__content" :style="{ height: `${totalHeight}px` }">
      <div
        v-for="item in visibleItems"
        :key="item.id"
        class="virtual-list__item"
        :style="{ transform: `translateY(${item.offset}px)` }"
      >
        <slot name="item" :item="item.data" :index="item.index"></slot>
      </div>
    </div>

    <div v-if="loading" class="virtual-list__loading">
      <q-spinner color="primary" size="3em" />
      <div class="q-mt-sm">Yükleniyor...</div>
    </div>

    <div v-if="!hasMore && items.length > 0" class="virtual-list__end">
      Tüm ürünler yüklendi
    </div>
  </div>
</template>

<script>
export default {
  name: 'VirtualList',

  props: {
    items: {
      type: Array,
      required: true,
    },
    itemHeight: {
      type: Number,
      default: 50,
    },
    height: {
      type: Number,
      default: 400,
    },
    buffer: {
      type: Number,
      default: 5,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    hasMore: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      scrollTop: 0,
      viewportHeight: 0,
    };
  },

  computed: {
    totalHeight() {
      return this.items.length * this.itemHeight;
    },

    visibleCount() {
      return Math.ceil(this.viewportHeight / this.itemHeight) + this.buffer * 2;
    },

    startIndex() {
      let start = Math.floor(this.scrollTop / this.itemHeight) - this.buffer;
      return Math.max(0, start);
    },

    endIndex() {
      let end = this.startIndex + this.visibleCount;
      return Math.min(this.items.length - 1, end);
    },

    visibleItems() {
      const items = [];

      for (let i = this.startIndex; i <= this.endIndex; i++) {
        items.push({
          id: this.items[i].id || i,
          data: this.items[i],
          index: i,
          offset: i * this.itemHeight,
        });
      }

      return items;
    },
  },

  mounted() {
    this.viewportHeight = this.height;
    window.addEventListener('resize', this.handleResize);
    this.$nextTick(this.handleResize);
  },

  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
  },

  methods: {
    handleScroll(event) {
      this.scrollTop = event.target.scrollTop;

      // Son elemanlara yaklaşıldığında daha fazla veri yükle
      const scrollPosition = event.target.scrollTop + event.target.offsetHeight;
      const scrollThreshold = this.totalHeight - 500; // 500px kala yükle

      if (scrollPosition >= scrollThreshold && this.hasMore && !this.loading) {
        this.$emit('load-more');
      }
    },

    handleResize() {
      // Viewport yüksekliğini güncelle
      this.viewportHeight = this.$el.offsetHeight;
    },

    scrollTo(index) {
      const scrollTop = index * this.itemHeight;
      this.$el.scrollTo({
        top: scrollTop,
        behavior: 'smooth',
      });
    },
  },
};
</script>

<style scoped>
.virtual-list {
  position: relative;
  overflow-y: auto;
  overflow-x: hidden;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  background-color: #fff;
}

.virtual-list__content {
  position: relative;
  width: 100%;
}

.virtual-list__item {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  box-sizing: border-box;
  will-change: transform;
}

.virtual-list__loading,
.virtual-list__end {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: #666;
  font-size: 14px;
}
</style>
```

## 3. Önbellek Stratejileri

### 3.1 API Yanıtlarını Önbelleğe Alma

`src/composables/useCachedApi.js`:

```javascript
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar';

export function useCachedApi(endpoint, options = {}) {
  const $q = useQuasar();

  const data = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const lastFetched = ref(null);

  const cacheKey = `cache_${endpoint}`;
  const cacheExpiry = options.cacheExpiry || 5 * 60 * 1000; // Varsayılan 5 dakika

  const isStale = computed(() => {
    if (!lastFetched.value) return true;
    return Date.now() - lastFetched.value > cacheExpiry;
  });

  async function fetchData(params = {}, force = false) {
    // Önbellekte veri varsa ve zorlanmamışsa ve süresi dolmamışsa önbellekten dön
    if (!force && !isStale.value && data.value) {
      return data.value;
    }

    // Çevrimdışı modda ve önbellekte veri varsa
    if (!navigator.onLine) {
      const cached = getCachedData();
      if (cached) {
        data.value = cached;
        $q.notify({
          message:
            'Çevrimdışı moddasınız. Son kaydedilen veriler gösteriliyor.',
          color: 'warning',
          position: 'top',
          timeout: 3000,
        });
        return data.value;
      }
    }

    loading.value = true;
    error.value = null;

    try {
      const queryString = new URLSearchParams(params).toString();
      const url = `/api/${endpoint}${queryString ? `?${queryString}` : ''}`;

      const response = await fetch(url);

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();

      // Veriyi önbelleğe al
      data.value = result;
      lastFetched.value = Date.now();

      // Yerel depolamaya kaydet
      try {
        const cacheData = {
          data: result,
          timestamp: lastFetched.value,
          params,
        };
        localStorage.setItem(cacheKey, JSON.stringify(cacheData));
      } catch (e) {
        console.warn('Önbelleğe yazılırken hata oluştu:', e);
      }

      return result;
    } catch (err) {
      console.error('API Error:', err);
      error.value = err.message;

      // Çevrimiçi değilse ve önbellekte veri varsa göster
      if (!navigator.onLine) {
        const cached = getCachedData();
        if (cached) {
          data.value = cached;
          $q.notify({
            message: 'Ağ bağlantısı yok. Son kaydedilen veriler gösteriliyor.',
            color: 'warning',
            position: 'top',
            timeout: 3000,
          });
          return data.value;
        }
      }

      throw err;
    } finally {
      loading.value = false;
    }
  }

  function getCachedData() {
    try {
      const cached = localStorage.getItem(cacheKey);
      if (cached) {
        const { data, timestamp, params } = JSON.parse(cached);

        // Önbellek süresi dolmuş mu kontrol et
        if (Date.now() - timestamp > cacheExpiry) {
          return null;
        }

        return data;
      }
    } catch (e) {
      console.warn('Önbellekten okunurken hata oluştu:', e);
    }

    return null;
  }

  function invalidateCache() {
    localStorage.removeItem(cacheKey);
    lastFetched.value = null;
  }

  // İlk yüklemede önbellekten veri yükle
  const cachedData = getCachedData();
  if (cachedData) {
    data.value = cachedData;
    lastFetched.value = Date.now();
  }

  return {
    data,
    loading,
    error,
    fetchData,
    invalidateCache,
    isStale,
    lastFetched,
  };
}
```

## 4. Ödev

1. Sepet işlevselliğini tamamlayın:

   - Ürün ekleme/çıkarma işlemlerini optimize edin
   - Kupon uygulama işlevini ekleyin
   - Ödeme entegrasyonunu yapın

2. Performans optimizasyonları uygulayın:

   - Büyük listeler için virtual scrolling uygulayın
   - API isteklerini önbelleğe alın
   - Görüntüleri lazy loading ile yükleyin

3. PWA özellikleri ekleyin:
   - Çevrimdışı çalışma desteği
   - Push bildirimleri
   - Ana ekrana ekleme istemi

## 5. Yararlı Kaynaklar

- [Vue 3 Performance Guide](https://v3.vuejs.org/guide/optimizations.html)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Quasar Framework Documentation](https://quasar.dev/)
- [Web Vitals](https://web.dev/vitals/)
- [Workbox](https://developers.google.com/web/tools/workbox)

---

**Not:** Bir sonraki derste uygulamanızı test etmeyi ve dağıtmayı öğreneceğiz.
