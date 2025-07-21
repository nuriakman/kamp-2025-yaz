# Pinia: Temel Kavramlar Özeti

Bu doküman, [Pinia resmi dokümantasyonunun](https://pinia.vuejs.org/) temel kavramlarını ve en sık kullanılan özelliklerini özetlemektedir.

## Introduction (Giriş)

### Pinia Nedir?

Pinia, Vue uygulamaları için bir durum yönetimi (state management) kütüphanesidir. Vue'nun resmi state management çözümü olan Vuex'in yerini alan, daha basit ve sezgisel bir alternatif olarak tasarlanmıştır. Temel özellikleri şunlardır:

- **Type-Safe:** TypeScript ile tam uyumludur ve otomatik tamamlama desteği sunar.
- **Basit ve Sezgisel API:** Az sayıda kavramla öğrenmesi ve kullanması kolaydır.
- **Geliştirici Araçları Desteği:** Vue Devtools ile entegre çalışarak state takibini kolaylaştırır.
- **Modüler ve Esnek:** Store'lar (mağazalar) modülerdir ve sadece ihtiyaç duyulduğunda içe aktarılır.

## Getting Started (Başlarken)

Pinia'yı bir Vue projesine eklemek için önce yüklenmesi ve ardından Vue uygulamasına bir eklenti (plugin) olarak tanıtılması gerekir.

**1. Kurulum:**

```bash
npm install pinia
# veya
yarn add pinia
```

**2. Vue Uygulamasına Ekleme:**

`main.js` veya `main.ts` dosyasında Pinia örneği oluşturulur ve `app.use()` ile uygulamaya dahil edilir.

```javascript
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.mount('#app');
```

## Core Concepts (Temel Kavramlar)

### Defining a Store (Bir Store Tanımlama)

Bir "store", `defineStore()` fonksiyonu ile tanımlanır. Her store'un uygulama genelinde benzersiz bir ID'si olmalıdır.

```javascript
// stores/counter.js
import { defineStore } from 'pinia';

// `defineStore` ilk argüman olarak benzersiz bir ID alır.
export const useCounterStore = defineStore('counter', {
  state: () => ({
    count: 0,
    name: 'Eduardo',
  }),
  getters: {
    doubleCount: (state) => state.count * 2,
  },
  actions: {
    increment() {
      this.count++;
    },
  },
});
```

### State (Durum)

- `state`, store'un temel verisini tutan bir fonksiyondur ve bir nesne döndürmelidir.
- Bir bileşen içinden state'e doğrudan erişilebilir ve değiştirilebilir.

```vue
<script setup>
import { useCounterStore } from '@/stores/counter';

const counter = useCounterStore();

// State'e erişim
console.log(counter.count);

// State'i değiştirme
counter.count++;
</script>
```

### Getters

- `getters`, state'e dayalı olarak hesaplanan değerlerdir. Vue'daki `computed` özelliklerine benzerler.
- Sonuçları önbelleğe alınır; yani bağımlı oldukları state değişmediği sürece yeniden hesaplanmazlar.

```javascript
// store tanımı içinde
getters: {
  // Normal fonksiyon
  doubleCount: (state) => state.count * 2,
  // Diğer getter'ları kullanan bir getter
  doubleCountPlusOne() {
    return this.doubleCount + 1
  },
}
```

### Actions

- `actions`, state'i değiştiren metotlardır. Vue'daki `methods` gibidirler.
- Asenkron olabilirler (`async/await` desteklerler).
- State'i doğrudan `this` üzerinden değiştirebilirler.

```javascript
// store tanımı içinde
actions: {
  increment() {
    this.count++
  },
  async fetchUserData() {
    const response = await fetch('/api/user')
    const data = await response.json()
    this.userData = data
  },
}
```

## Plugins (Eklentiler)

Pinia'nın işlevselliği eklentilerle genişletilebilir. Eklentiler, store'lara yeni özellikler eklemek, state değişikliklerini takip etmek (örneğin Local Storage'a kaydetmek) veya `actions` çağrılarını izlemek için kullanılır.

Bir eklenti, `pinia.use()` ile eklenir ve store örneğini argüman olarak alan bir fonksiyondur.

```javascript
// plugins/myPlugin.js
export function myPlugin({ store }) {
  store.$subscribe((mutation) => {
    // state değişikliklerini dinle
    console.log(`[${store.$id}]: ${mutation.type}`, mutation.payload);
  });

  // store'a yeni bir özellik ekle
  return { secret: 'the-secret-key' };
}
```

## Stores outside of components (Bileşen Dışında Store Kullanımı)

Bazen bir store'u bir Vue bileşeni dışında (örneğin bir yönlendirme (router) guard'ı içinde) kullanmak gerekebilir. Bunu yaparken dikkat edilmesi gereken, Pinia örneğinin henüz aktif olmayabileceğidir.

Çözüm, store'u kullanacağınız fonksiyonun _içinde_ çağırmaktır.

```javascript
// router.js
import { useUserStore } from './stores/user';

router.beforeEach((to) => {
  // Fonksiyon içinde çağırarak store'un aktif olduğundan emin ol
  const userStore = useUserStore();

  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    return '/login';
  }
});
```

## Axios ile CRUD İşlemleri Örneği (Option Stores İle)

Pinia store'unda axios kullanarak temel CRUD (Create, Read, Update, Delete) işlemlerini nasıl yapabileceğinize dair bir örnek:

```javascript
// stores/posts.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const usePostsStore = defineStore('posts', {
  state: () => ({
    posts: [],
    loading: false,
    error: null,
  }),

  getters: {
    // Tüm postları getir
    allPosts: (state) => state.posts,

    // ID'ye göre post getir
    getPostById: (state) => (id) => {
      return state.posts.find((post) => post.id === id);
    },
  },

  actions: {
    // Tüm postları getir (Read)
    async fetchPosts() {
      this.loading = true;
      try {
        const response = await axios.get(
          'https://jsonplaceholder.typicode.com/posts',
        );
        this.posts = response.data;
        this.error = null;
      } catch (error) {
        this.error = 'Gönderiler yüklenirken bir hata oluştu.';
        console.error('Hata:', error);
      } finally {
        this.loading = false;
      }
    },

    // Yeni post ekle (Create)
    async addPost(postData) {
      try {
        const response = await axios.post(
          'https://jsonplaceholder.typicode.com/posts',
          postData,
        );
        this.posts.unshift(response.data); // Yeni postu listenin başına ekle
        return response.data;
      } catch (error) {
        console.error('Hata:', error);
        throw error;
      }
    },

    // Post güncelle (Update)
    async updatePost(id, updatedData) {
      try {
        const response = await axios.put(
          `https://jsonplaceholder.typicode.com/posts/${id}`,
          updatedData,
        );
        const index = this.posts.findIndex((post) => post.id === id);
        if (index !== -1) {
          this.posts[index] = response.data;
        }
        return response.data;
      } catch (error) {
        console.error('Hata:', error);
        throw error;
      }
    },

    // Post sil (Delete)
    async deletePost(id) {
      try {
        await axios.delete(`https://jsonplaceholder.typicode.com/posts/${id}`);
        this.posts = this.posts.filter((post) => post.id !== id);
        return true;
      } catch (error) {
        console.error('Hata:', error);
        throw error;
      }
    },
  },
});
```

## Axios ile CRUD İşlemleri Örneği (Setup Stores İle)

Pinia store'unda axios kullanarak temel CRUD (Create, Read, Update, Delete) işlemlerini nasıl yapabileceğinize dair bir örnek:

```javascript
// stores/posts.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const usePostsStore = defineStore('posts', () => {
  // State
  const posts = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Getters
  const allPosts = computed(() => posts.value);
  const getPostById = computed(
    () => (id) => posts.value.find((post) => post.id === id),
  );

  // Actions
  const fetchPosts = async () => {
    loading.value = true;
    try {
      const response = await axios.get(
        'https://jsonplaceholder.typicode.com/posts',
      );
      posts.value = response.data;
      error.value = null;
    } catch (err) {
      error.value = 'Gönderiler yüklenirken bir hata oluştu.';
      console.error('Hata:', err);
    } finally {
      loading.value = false;
    }
  };

  const addPost = async (postData) => {
    try {
      const response = await axios.post(
        'https://jsonplaceholder.typicode.com/posts',
        postData,
      );
      posts.value.unshift(response.data);
      return response.data;
    } catch (err) {
      console.error('Hata:', err);
      throw err;
    }
  };

  const updatePost = async (id, updatedData) => {
    try {
      const response = await axios.put(
        `https://jsonplaceholder.typicode.com/posts/${id}`,
        updatedData,
      );
      const index = posts.value.findIndex((post) => post.id === id);
      if (index !== -1) {
        posts.value[index] = response.data;
      }
      return response.data;
    } catch (err) {
      console.error('Hata:', err);
      throw err;
    }
  };

  const deletePost = async (id) => {
    try {
      await axios.delete(`https://jsonplaceholder.typicode.com/posts/${id}`);
      posts.value = posts.value.filter((post) => post.id !== id);
      return true;
    } catch (err) {
      console.error('Hata:', err);
      throw err;
    }
  };

  return {
    // State
    posts,
    loading,
    error,

    // Getters
    allPosts,
    getPostById,

    // Actions
    fetchPosts,
    addPost,
    updatePost,
    deletePost,
  };
});
```

### Bileşen İçinde Kullanımı

```html
<template>
  <div>
    <h1>Gönderiler</h1>

    <!-- Yeni Gönderi Ekleme Formu -->
    <form @submit.prevent="addNewPost">
      <input v-model="newPost.title" placeholder="Başlık" required />
      <textarea v-model="newPost.body" placeholder="İçerik" required></textarea>
      <button type="submit">Gönderi Ekle</button>
    </form>

    <!-- Yükleme Durumu -->
    <div v-if="loading">Yükleniyor...</div>

    <!-- Hata Mesajı -->
    <div v-if="error" class="error">{{ error }}</div>

    <!-- Gönderi Listesi -->
    <div v-for="post in allPosts" :key="post.id" class="post">
      <h3>{{ post.title }}</h3>
      <p>{{ post.body }}</p>
      <button @click="editPost(post)">Düzenle</button>
      <button @click="deletePost(post.id)">Sil</button>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue';
  import { usePostsStore } from '@/stores/posts';
  import { storeToRefs } from 'pinia';

  const postsStore = usePostsStore();
  const newPost = ref({ title: '', body: '' });

  // Store'dan state'leri al
  const { posts: allPosts, loading, error } = storeToRefs(postsStore);

  // Bileşen yüklendiğinde gönderileri getir
  onMounted(() => {
    postsStore.fetchPosts();
  });

  // Yeni gönderi ekle
  const addNewPost = async () => {
    try {
      await postsStore.addPost({
        title: newPost.value.title,
        body: newPost.value.body,
        userId: 1,
      });
      newPost.value = { title: '', body: '' };
    } catch (error) {
      console.error('Gönderi eklenirken hata oluştu:', error);
    }
  };

  // Gönderi düzenle
  const editPost = (post) => {
    const newTitle = prompt('Başlığı düzenleyin:', post.title);
    if (newTitle === null) return; // Kullanıcı iptal ettiyse

    const newBody = prompt('İçeriği düzenleyin:', post.body);
    if (newBody === null) return; // Kullanıcı iptal ettiyse

    // Store'daki updatePost metodunu çağır
    postsStore.updatePost(post.id, {
      ...post,
      title: newTitle,
      body: newBody,
    });
  };

  // Gönderi sil
  const deletePost = async (id) => {
    if (confirm('Bu gönderiyi silmek istediğinize emin misiniz?')) {
      try {
        await postsStore.deletePost(id);
      } catch (error) {
        console.error('Gönderi silinirken hata oluştu:', error);
      }
    }
  };
</script>
```

Bu örnekte:

- `fetchPosts`: Tüm gönderileri API'den çeker ve store'a kaydeder
- `addPost`: Yeni bir gönderi ekler ve listeyi günceller
- `updatePost`: Mevcut bir gönderiyi günceller
- `deletePost`: Bir gönderiyi siler

Bu yapıyı kullanarak, uygulamanızın farklı bileşenlerinde bu store'u import edip aynı verilere erişebilir ve güncelleyebilirsiniz.
