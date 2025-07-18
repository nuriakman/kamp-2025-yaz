# Gün 5 - Öğleden Sonra Oturumu: Quasar Framework ile İleri Düzey Uygulama Geliştirme

## 1. Quasar Framework'e Giriş

### 1.1 Quasar Nedir?

- Vue.js tabanlı tam kapsamlı bir framework
- Responsive arayüzler için hazır bileşenler
- PWA, SSR, Electron, Capacitor gibi platformlara destek
- Material Design ve diğer tasarım sistemleri ile uyumlu

### 1.2 Avantajları

- Hızlı geliştirme süreci
- Tek kod tabanı ile birden fazla platform
- Performans optimizasyonları
- Zengin bileşen kütüphanesi
- İyi dokümantasyon ve topluluk desteği

## 2. Quasar Projesi Oluşturma

### 2.1 Kurulum ve Proje Oluşturma

```bash
# Quasar CLI kurulumu
npm install -g @quasar/cli

# Yeni proje oluşturma
quasar create e-ticaret-quasar

# Gerekli paketlerin kurulumu
cd e-ticaret-quasar
npm install axios @quasar/extras @quasar/vite-plugin @quasar/icongenie
```

### 2.2 Proje Yapısı

```
e-ticaret-quasar/
├── public/              # Statik dosyalar
├── src/
│   ├── assets/          # Asset dosyaları (resimler, fontlar vb.)
│   ├── boot/            # Uygulama başlangıç scriptleri
│   ├── components/      # Yeniden kullanılabilir bileşenler
│   ├── composables/     # Composition API fonksiyonları
│   ├── css/             # Global CSS dosyaları
│   ├── layouts/         # Uygulama şablonları
│   ├── pages/           # Sayfa bileşenleri
│   ├── router/          # Rota yapılandırması
│   ├── stores/          # Pinia store'ları
│   ├── App.vue          # Ana uygulama bileşeni
│   └── boot/            # Uygulama başlangıç scriptleri
└── quasar.config.js     # Quasar yapılandırma dosyası
```

## 3. Temel Bileşenler ve Layout

### 3.1 Ana Layout Oluşturma

`src/layouts/MainLayout.vue`:

```vue
<template>
  <q-layout view="hHh lpR fFf">
    <!-- Header -->
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />

        <q-toolbar-title class="text-weight-bold">
          <q-avatar>
            <img src="logo.png" />
          </q-avatar>
          E-Ticaret Mağazası
        </q-toolbar-title>

        <q-space />

        <!-- Arama Çubuğu -->
        <q-input
          v-model="searchQuery"
          dense
          outlined
          rounded
          placeholder="Ürün ara..."
          class="q-mr-sm"
          style="min-width: 300px;"
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>

        <!-- Sepet Butonu -->
        <q-btn flat round dense icon="shopping_cart" class="q-mr-sm">
          <q-badge v-if="sepetStore.toplamUrunSayisi > 0" color="red" floating>
            {{ sepetStore.toplamUrunSayisi }}
          </q-badge>
          <q-menu>
            <q-card style="min-width: 300px">
              <q-card-section class="bg-primary text-white">
                <div class="text-h6">Sepetim</div>
              </q-card-section>

              <q-separator />

              <q-card-section
                v-if="sepetStore.urunler.length === 0"
                class="text-center q-pa-lg"
              >
                <q-icon name="shopping_cart_off" size="48px" color="grey-5" />
                <div class="text-grey-5 q-mt-sm">Sepetiniz boş</div>
              </q-card-section>

              <q-list v-else>
                <q-item
                  v-for="urun in sepetStore.urunler"
                  :key="urun.id"
                  class="q-pa-sm"
                >
                  <q-item-section avatar>
                    <q-avatar>
                      <img :src="getUrunResim(urun.resim)" />
                    </q-avatar>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>{{ urun.urun_adi }}</q-item-label>
                    <q-item-label caption>
                      {{ urun.adet }} x {{ formatPara(urun.fiyat) }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section side>
                    <q-btn
                      flat
                      round
                      dense
                      icon="remove"
                      size="sm"
                      @click="urunMiktarAzalt(urun.id, urun.adet - 1)"
                    />
                    <span class="q-mx-sm">{{ urun.adet }}</span>
                    <q-btn
                      flat
                      round
                      dense
                      icon="add"
                      size="sm"
                      @click="urunMiktarArtir(urun.id, urun.adet + 1)"
                    />
                  </q-item-section>

                  <q-item-section side>
                    <q-btn
                      flat
                      round
                      dense
                      icon="delete"
                      color="negative"
                      size="sm"
                      @click="sepettenCikar(urun.id)"
                    />
                  </q-item-section>
                </q-item>
              </q-list>

              <q-separator />

              <q-card-section class="row items-center">
                <div class="col text-h6">Toplam:</div>
                <div class="col-auto text-h6 text-primary">
                  {{ formatPara(sepetStore.toplamFiyat) }}
                </div>
              </q-card-section>

              <q-separator />

              <q-card-actions align="right" class="q-pa-md">
                <q-btn
                  flat
                  label="Sepeti Temizle"
                  color="negative"
                  @click="sepetiTemizle"
                  :disable="sepetStore.urunler.length === 0"
                />
                <q-space />
                <q-btn
                  label="Ödemeye Geç"
                  color="primary"
                  :to="{ name: 'odeme' }"
                  :disable="sepetStore.urunler.length === 0"
                />
              </q-card-actions>
            </q-card>
          </q-menu>
        </q-btn>

        <!-- Kullanıcı Menüsü -->
        <q-btn flat round dense>
          <q-avatar size="32px">
            <img :src="authStore.user?.avatar || 'default-avatar.png'" />
          </q-avatar>
          <q-menu>
            <q-list style="min-width: 150px">
              <q-item
                v-if="!authStore.isAuthenticated"
                clickable
                v-close-popup
                :to="{ name: 'giris' }"
              >
                <q-item-section>Giriş Yap</q-item-section>
              </q-item>
              <q-item
                v-if="!authStore.isAuthenticated"
                clickable
                v-close-popup
                :to="{ name: 'kayit' }"
              >
                <q-item-section>Kayıt Ol</q-item-section>
              </q-item>
              <q-item
                v-if="authStore.isAuthenticated"
                clickable
                v-close-popup
                :to="{ name: 'profil' }"
              >
                <q-item-section>Profilim</q-item-section>
              </q-item>
              <q-item
                v-if="authStore.isAuthenticated"
                clickable
                v-close-popup
                :to="{ name: 'siparislerim' }"
              >
                <q-item-section>Siparişlerim</q-item-section>
              </q-item>
              <q-separator v-if="authStore.isAuthenticated" />
              <q-item
                v-if="authStore.isAuthenticated"
                clickable
                v-close-popup
                @click="cikisYap"
              >
                <q-item-section>Çıkış Yap</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Sol Menü -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :width="250"
      :breakpoint="700"
      class="bg-grey-1"
    >
      <q-scroll-area class="fit">
        <q-list padding>
          <q-item-label header class="text-weight-bold text-grey-8">
            Menü
          </q-item-label>

          <q-item clickable v-ripple :to="{ name: 'anasayfa' }">
            <q-item-section avatar>
              <q-icon name="home" />
            </q-item-section>
            <q-item-section>Ana Sayfa</q-item-section>
          </q-item>

          <q-expansion-item
            expand-separator
            icon="category"
            label="Kategoriler"
            default-opened
          >
            <q-list>
              <q-item
                v-for="kategori in kategoriler"
                :key="kategori.id"
                clickable
                v-ripple
                :to="{ name: 'kategori', params: { id: kategori.id } }"
                class="q-pl-xl"
              >
                <q-item-section>{{ kategori.adi }}</q-item-section>
                <q-item-section side>
                  <q-badge color="primary" :label="kategori.urun_sayisi" />
                </q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>

          <q-separator class="q-my-md" />

          <q-item clickable v-ripple :to="{ name: 'indirimdekiler' }">
            <q-item-section avatar>
              <q-icon name="local_offer" color="negative" />
            </q-item-section>
            <q-item-section>İndirimdekiler</q-item-section>
            <q-item-section side>
              <q-badge color="negative" label="%50'ye varan" />
            </q-item-section>
          </q-item>

          <q-item clickable v-ripple :to="{ name: 'yeniUrunler' }">
            <q-item-section avatar>
              <q-icon name="fiber_new" color="primary" />
            </q-item-section>
            <q-item-section>Yeni Ürünler</q-item-section>
          </q-item>

          <q-separator class="q-my-md" />

          <q-item clickable v-ripple :to="{ name: 'hakkimizda' }">
            <q-item-section avatar>
              <q-icon name="info" />
            </q-item-section>
            <q-item-section>Hakkımızda</q-item-section>
          </q-item>

          <q-item clickable v-ripple :to="{ name: 'iletisim' }">
            <q-item-section avatar>
              <q-icon name="alternate_email" />
            </q-item-section>
            <q-item-section>İletişim</q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Sayfa İçeriği -->
    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition
          enter-active-class="animated fadeIn"
          leave-active-class="animated fadeOut"
          mode="out-in"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>

    <!-- Footer -->
    <q-footer elevated class="bg-grey-9 text-white">
      <q-toolbar>
        <q-toolbar-title class="text-center">
          <div class="row justify-center q-col-gutter-md">
            <div class="col-12 col-md-4 q-pa-md">
              <div class="text-h6 q-mb-md">Hızlı Erişim</div>
              <div class="column q-gutter-y-sm">
                <q-btn flat label="Hakkımızda" :to="{ name: 'hakkimizda' }" />
                <q-btn flat label="İletişim" :to="{ name: 'iletisim' }" />
                <q-btn
                  flat
                  label="Kargo ve İade"
                  :to="{ name: 'kargo-ve-iade' }"
                />
                <q-btn
                  flat
                  label="Gizlilik Politikası"
                  :to="{ name: 'gizlilik' }"
                />
              </div>
            </div>
            <div class="col-12 col-md-4 q-pa-md">
              <div class="text-h6 q-mb-md">Müşteri Hizmetleri</div>
              <div class="column q-gutter-y-sm">
                <div class="row items-center">
                  <q-icon name="phone" class="q-mr-sm" />
                  <div>0850 123 45 67</div>
                </div>
                <div class="row items-center">
                  <q-icon name="email" class="q-mr-sm" />
                  <div>info@eticaret.com</div>
                </div>
                <div class="row items-center">
                  <q-icon name="schedule" class="q-mr-sm" />
                  <div>Pazartesi - Cumartesi: 09:00 - 18:00</div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4 q-pa-md">
              <div class="text-h6 q-mb-md">Bizi Takip Edin</div>
              <div class="row q-gutter-sm">
                <q-btn round color="blue" icon="facebook" />
                <q-btn round color="light-blue" icon="twitter" />
                <q-btn round color="pink" icon="instagram" />
                <q-btn round color="red" icon="youtube" />
                <q-btn round color="blue-9" icon="linkedin" />
              </div>
              <div class="q-mt-md">
                <div class="text-subtitle2 q-mb-sm">E-Bülten'e Kaydolun</div>
                <q-input
                  v-model="email"
                  outlined
                  dense
                  placeholder="E-posta adresiniz"
                  class="bg-white"
                >
                  <template v-slot:append>
                    <q-btn
                      flat
                      round
                      icon="send"
                      color="primary"
                      @click="bulteneKaydol"
                    />
                  </template>
                </q-input>
              </div>
            </div>
          </div>
        </q-toolbar-title>
      </q-toolbar>
      <q-separator dark />
      <div class="row items-center justify-center q-py-sm">
        <div class="text-caption">
          © {{ new Date().getFullYear() }} Tüm hakları saklıdır.
        </div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import { useSepetStore } from 'stores/sepet';
import { useAuthStore } from 'stores/auth';
import { useKategoriStore } from 'stores/kategori';

const $q = useQuasar();
const router = useRouter();
const sepetStore = useSepetStore();
const authStore = useAuthStore();
const kategoriStore = useKategoriStore();

const leftDrawerOpen = ref(false);
const searchQuery = ref('');
const email = ref('');

const kategoriler = ref([
  { id: 1, adi: 'Elektronik', urun_sayisi: 156 },
  { id: 2, adi: 'Giyim', urun_sayisi: 89 },
  { id: 3, adi: 'Ev & Yaşam', urun_sayisi: 203 },
  { id: 4, adi: 'Kitap', urun_sayisi: 312 },
  { id: 5, adi: 'Spor & Outdoor', urun_sayisi: 67 },
]);

// Format helpers
const formatPara = (value) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
    minimumFractionDigits: 2,
  }).format(value);
};

const getUrunResim = (resim) => {
  return resim
    ? `http://localhost:8000/storage/${resim}`
    : 'placeholder-product.png';
};

// Sepet işlemleri
const urunMiktarArtir = (urunId, yeniMiktar) => {
  sepetStore.urunMiktarGuncelle(urunId, yeniMiktar);
};

const urunMiktarAzalt = (urunId, yeniMiktar) => {
  if (yeniMiktar < 1) {
    sepettenCikar(urunId);
  } else {
    sepetStore.urunMiktarGuncelle(urunId, yeniMiktar);
  }
};

const sepettenCikar = (urunId) => {
  $q.dialog({
    title: 'Onay',
    message: 'Ürünü sepetinizden çıkarmak istediğinize emin misiniz?',
    cancel: true,
    persistent: true,
  }).onOk(() => {
    sepetStore.sepettenCikar(urunId);
    $q.notify({
      type: 'positive',
      message: 'Ürün sepetinizden çıkarıldı',
    });
  });
};

const sepetiTemizle = () => {
  $q.dialog({
    title: 'Sepeti Temizle',
    message: 'Sepetinizdeki tüm ürünleri kaldırmak istediğinize emin misiniz?',
    cancel: true,
    persistent: true,
  }).onOk(() => {
    sepetStore.sepetiBosalt();
    $q.notify({
      type: 'positive',
      message: 'Sepetiniz temizlendi',
    });
  });
};

// Kullanıcı işlemleri
const cikisYap = async () => {
  try {
    await authStore.cikisYap();
    router.push({ name: 'anasayfa' });
    $q.notify({
      type: 'positive',
      message: 'Başarıyla çıkış yapıldı',
    });
  } catch (error) {
    console.error('Çıkış yapılırken hata oluştu:', error);
    $q.notify({
      type: 'negative',
      message: 'Çıkış yapılırken bir hata oluştu',
    });
  }
};

// E-bülten kaydı
const bulteneKaydol = () => {
  if (!email.value) {
    $q.notify({
      type: 'warning',
      message: 'Lütfen geçerli bir e-posta adresi giriniz',
    });
    return;
  }

  // API çağrısı yapılabilir
  console.log('E-bültene kayıt olundu:', email.value);

  $q.notify({
    type: 'positive',
    message: 'E-bültenimize başarıyla kaydoldunuz!',
  });

  email.value = '';
};

// Uygulama yüklendiğinde
onMounted(() => {
  // Kategorileri yükle
  kategoriStore.kategorileriGetir();

  // Karanlık tema ayarı
  $q.dark.set('auto');
});
</script>

<style scoped>
.q-layout {
  background-color: #f5f5f5;
}

.q-header {
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
}

.q-drawer {
  box-shadow: 1px 0 5px rgba(0, 0, 0, 0.1);
}

.q-page-container {
  padding-top: 64px;
  min-height: 100vh;
}

@media (max-width: 600px) {
  .q-page-container {
    padding-top: 56px;
  }
}
</style>
```
