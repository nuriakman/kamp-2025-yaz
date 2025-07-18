# Gün 6 - Sabah Oturumu: İleri Düzey Vue.js ve API Entegrasyonu

## 1. Composition API ve Composables

### 1.1 Composition API'nin Temelleri

- `setup()` fonksiyonu ve `<script setup>` sözdizimi
- Reactive state yönetimi: `ref()`, `reactive()`, `computed()`
- Lifecycle hooks: `onMounted()`, `onUpdated()`, `onUnmounted()`
- Watchers: `watch()` ve `watchEffect()`
- Provide/Inject ile veri paylaşımı

### 1.2 Custom Composable'lar

`src/composables/useApi.js`:

```javascript
import { ref } from 'vue';

export function useApi(endpoint) {
  const data = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchData = async (params = {}) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch(
        `/api/${endpoint}?${new URLSearchParams(params)}`,
      );

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      data.value = await response.json();
      return data.value;
    } catch (err) {
      console.error('API Error:', err);
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const postData = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch(`/api/${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      return result;
    } catch (err) {
      console.error('API Error:', err);
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    data,
    loading,
    error,
    fetchData,
    postData,
  };
}
```

### 1.3 Kullanım Örneği

`src/views/UrunListesi.vue`:

```vue
<template>
  <div class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="col">
        <h4 class="text-h4 q-my-none">Ürünler</h4>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Yeni Ürün"
          :to="{ name: 'urun-ekle' }"
          v-if="authStore.isAdmin"
        />
      </div>
    </div>

    <!-- Filtreleme ve Sıralama -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-4">
        <q-input
          v-model="filters.search"
          outlined
          dense
          placeholder="Ürün ara..."
          clearable
          @update:model-value="fetchProducts"
        >
          <template v-slot:prepend>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>

      <div class="col-12 col-md-3">
        <q-select
          v-model="filters.category"
          :options="categories"
          option-label="name"
          option-value="id"
          emit-value
          map-options
          outlined
          dense
          label="Kategori"
          clearable
          @update:model-value="fetchProducts"
        />
      </div>

      <div class="col-12 col-md-3">
        <q-select
          v-model="filters.sortBy"
          :options="sortOptions"
          option-label="label"
          option-value="value"
          emit-value
          map-options
          outlined
          dense
          label="Sırala"
          @update:model-value="fetchProducts"
        />
      </div>

      <div class="col-12 col-md-2">
        <q-checkbox
          v-model="filters.inStockOnly"
          label="Sadece Stokta Olanlar"
          @update:model-value="fetchProducts"
        />
      </div>
    </div>

    <!-- Yükleniyor durumu -->
    <div v-if="loading" class="text-center q-pa-lg">
      <q-spinner color="primary" size="3em" />
      <p>Ürünler yükleniyor...</p>
    </div>

    <!-- Hata durumu -->
    <q-banner v-else-if="error" class="bg-negative text-white q-mb-md">
      {{ error }}
      <template v-slot:action>
        <q-btn flat color="white" label="Tekrar Dene" @click="fetchProducts" />
      </template>
    </q-banner>

    <!-- Ürün listesi -->
    <div v-else class="row q-col-gutter-md">
      <div
        v-for="product in products"
        :key="product.id"
        class="col-12 col-sm-6 col-md-4 col-lg-3"
      >
        <q-card class="product-card">
          <!-- Ürün resmi -->
          <q-img
            :src="getProductImage(product.image)"
            :alt="product.name"
            :ratio="1"
            class="product-image"
          >
            <!-- İndirim etiketi -->
            <div v-if="product.discount" class="discount-tag">
              %{{ product.discount }}
            </div>

            <!-- Stok yok etiketi -->
            <div v-if="product.stock === 0" class="out-of-stock">Tükendi</div>

            <!-- Hızlı işlem butonları -->
            <div class="absolute-bottom">
              <div class="row justify-center q-gutter-sm q-pa-sm">
                <q-btn
                  round
                  color="white"
                  text-color="primary"
                  icon="shopping_cart"
                  size="sm"
                  :disable="product.stock === 0"
                  @click.stop="addToCart(product)"
                />
                <q-btn
                  round
                  color="white"
                  text-color="red"
                  icon="favorite_border"
                  size="sm"
                />
                <q-btn
                  round
                  color="white"
                  text-color="dark"
                  icon="visibility"
                  size="sm"
                  :to="{ name: 'urun-detay', params: { id: product.id } }"
                />
              </div>
            </div>
          </q-img>

          <!-- Ürün bilgileri -->
          <q-card-section>
            <div class="text-subtitle1 text-weight-bold q-mb-xs">
              {{ product.name }}
            </div>

            <div class="row items-center q-mb-sm">
              <div class="col">
                <div class="text-h6 text-primary">
                  {{ formatPrice(product.price) }}
                  <span
                    v-if="product.originalPrice"
                    class="text-caption text-strike text-grey-7 q-ml-sm"
                  >
                    {{ formatPrice(product.originalPrice) }}
                  </span>
                </div>
              </div>
              <div class="col-auto">
                <q-badge
                  :color="product.stock > 0 ? 'positive' : 'negative'"
                  class="q-pa-sm"
                >
                  {{ product.stock > 0 ? 'Stokta Var' : 'Stokta Yok' }}
                </q-badge>
              </div>
            </div>

            <div class="text-caption text-grey-8 q-mb-sm">
              {{ product.shortDescription || 'Açıklama bulunmamaktadır.' }}
            </div>

            <div class="row items-center">
              <div class="col">
                <q-rating
                  v-model="product.rating"
                  size="1.5em"
                  color="amber-7"
                  readonly
                />
                <span class="text-caption text-grey-7 q-ml-sm">
                  ({{ product.reviewCount }} değerlendirme)
                </span>
              </div>
              <div class="col-auto">
                <q-btn
                  v-if="authStore.isAdmin"
                  flat
                  round
                  dense
                  color="grey-7"
                  icon="edit"
                  size="sm"
                  :to="{ name: 'urun-duzenle', params: { id: product.id } }"
                  @click.stop
                />
                <q-btn
                  v-if="authStore.isAdmin"
                  flat
                  round
                  dense
                  color="negative"
                  icon="delete"
                  size="sm"
                  @click.stop="confirmDelete(product)"
                />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Sayfalama -->
    <div v-if="!loading && !error" class="row justify-center q-mt-lg">
      <q-pagination
        v-model="pagination.page"
        :max="pagination.lastPage"
        :max-pages="5"
        :boundary-numbers="false"
        direction-links
        @update:model-value="handlePageChange"
      />
    </div>
  </div>

  <!-- Silme Onay Diyaloğu -->
  <q-dialog v-model="deleteDialog.show" persistent>
    <q-card>
      <q-card-section class="row items-center">
        <q-avatar
          icon="warning"
          color="warning"
          text-color="white"
          class="q-mr-sm"
        />
        <span class="q-ml-sm"
          >Bu ürünü silmek istediğinizden emin misiniz? Bu işlem geri
          alınamaz.</span
        >
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="İptal" color="primary" v-close-popup />
        <q-btn
          flat
          label="Sil"
          color="negative"
          @click="deleteProduct"
          :loading="deleteDialog.loading"
          v-close-popup
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';
import { useApi } from 'src/composables/useApi';
import { useAuthStore } from 'src/stores/auth';

const $q = useQuasar();
const router = useRouter();
const authStore = useAuthStore();

// API composable'ını kullan
const { data: productsData, loading, error, fetchData } = useApi('products');

// Filtreler
const filters = ref({
  search: '',
  category: null,
  sortBy: 'name_asc',
  inStockOnly: false,
});

// Sayfalama
const pagination = ref({
  page: 1,
  perPage: 12,
  total: 0,
  lastPage: 1,
});

// Silme diyaloğu
const deleteDialog = ref({
  show: false,
  productId: null,
  loading: false,
});

// Sıralama seçenekleri
const sortOptions = [
  { label: 'Ada Göre (A-Z)', value: 'name_asc' },
  { label: 'Ada Göre (Z-A)', value: 'name_desc' },
  { label: 'Fiyata Göre (Artan)', value: 'price_asc' },
  { label: 'Fiyata Göre (Azalan)', value: 'price_desc' },
  { label: 'Puana Göre', value: 'rating_desc' },
  { label: 'En Yeni', value: 'newest' },
  { label: 'En Çok Satan', value: 'bestseller' },
];

// Kategoriler (gerçek uygulamada API'den çekilecek)
const categories = ref([
  { id: 1, name: 'Elektronik' },
  { id: 2, name: 'Giyim' },
  { id: 3, name: 'Ev & Yaşam' },
  { id: 4, name: 'Kitap' },
  { id: 5, name: 'Spor & Outdoor' },
]);

// Filtrelenmiş ve sıralanmış ürünler
const products = computed(() => {
  if (!productsData.value?.data) return [];

  return productsData.value.data.map((product) => ({
    ...product,
    // İndirim hesaplamaları
    originalPrice: product.discount > 0 ? product.price : null,
    price:
      product.discount > 0
        ? product.price * (1 - product.discount / 100)
        : product.price,
  }));
});

// Ürünleri getir
const fetchProducts = async () => {
  try {
    const params = {
      page: pagination.value.page,
      per_page: pagination.value.perPage,
      search: filters.value.search,
      category_id: filters.value.category,
      sort: filters.value.sortBy,
      in_stock: filters.value.inStockOnly,
    };

    await fetchData(params);

    // Sayfalama bilgilerini güncelle
    if (productsData.value?.meta) {
      pagination.value = {
        page: productsData.value.meta.current_page,
        perPage: productsData.value.meta.per_page,
        total: productsData.value.meta.total,
        lastPage: productsData.value.meta.last_page,
      };
    }
  } catch (err) {
    console.error('Ürünler yüklenirken hata oluştu:', err);
  }
};

// Sayfa değiştiğinde
const handlePageChange = (page) => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
  fetchProducts();
};

// Sepete ekle
const addToCart = (product) => {
  // Sepet işlemleri burada yapılacak
  $q.notify({
    type: 'positive',
    message: `${product.name} sepete eklendi`,
    icon: 'shopping_cart',
    position: 'top-right',
  });
};

// Silme onayı
const confirmDelete = (product) => {
  deleteDialog.value = {
    show: true,
    productId: product.id,
    loading: false,
  };
};

// Ürün sil
const deleteProduct = async () => {
  deleteDialog.value.loading = true;

  try {
    // API'ye silme isteği gönder
    await fetch(`/api/products/${deleteDialog.value.productId}`, {
      method: 'DELETE',
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
    });

    $q.notify({
      type: 'positive',
      message: 'Ürün başarıyla silindi',
      position: 'top-right',
    });

    // Listeyi yenile
    await fetchProducts();
  } catch (err) {
    console.error('Ürün silinirken hata oluştu:', err);
    $q.notify({
      type: 'negative',
      message: 'Ürün silinirken bir hata oluştu',
      position: 'top-right',
    });
  } finally {
    deleteDialog.value = {
      show: false,
      productId: null,
      loading: false,
    };
  }
};

// Yardımcı fonksiyonlar
const formatPrice = (price) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
    minimumFractionDigits: 2,
  }).format(price);
};

const getProductImage = (imagePath) => {
  return imagePath
    ? `http://localhost:8000/storage/${imagePath}`
    : 'https://via.placeholder.com/300x300?text=Resim+Yok';
};

// Bileşen yüklendiğinde ürünleri getir
onMounted(() => {
  fetchProducts();
});
</script>

<style scoped>
.product-card {
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.product-image {
  position: relative;
  overflow: hidden;
  background-color: #f5f5f5;
}

.discount-tag {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: #ff5252;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  z-index: 1;
}

.out-of-stock {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  z-index: 1;
}

.q-card__section {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}
</style>
```
