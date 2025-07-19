# Vue.js Temelleri: Kapsamlı Özet

Bu doküman, [Vue.js resmi dokümantasyonunun](https://vuejs.org/guide/introduction.html) "Introduction", "Essentials" ve "Components In-Depth" bölümlerinin tamamını kapsayan detaylı bir özettir.

## İçindekiler

### Bölüm 1: Giriş (Introduction)
- [Vue.js Nedir?](#vuejs-nedir)
- [Progressive Framework (Aşamalı Çerçeve)](#progressive-framework-aşamalı-çerçeve)
- [API Stilleri: Options API vs. Composition API](#api-stilleri-options-api-vs-composition-api)

### Bölüm 2: Temeller (Essentials)
- [Bir Vue Uygulaması Oluşturma](#bir-vue-uygulaması-oluşturma)
- [Template Syntax (Şablon Sözdizimi)](#template-syntax-şablon-sözdizimi)
- [Reactivity Fundamentals (Reaktivitenin Temelleri)](#reactivity-fundamentals-reaktivitenin-temelleri)
- [Computed Properties (Hesaplanmış Özellikler)](#computed-properties-hesaplanmış-özellikler)
- [Class ve Style Bindings](#class-ve-style-bindings)
- [Conditional Rendering (Koşullu Render)](#conditional-rendering-koşullu-render)
- [List Rendering (Liste Render)](#list-rendering-liste-render)
- [Event Handling (Olay Yönetimi)](#event-handling-olay-yönetimi)
- [Form Input Bindings (Form Girdi Bağlama)](#form-input-bindings-form-girdi-bağlama)
- [Watchers (Gözlemciler)](#watchers-gözlemciler)
- [Template Refs](#template-refs)
- [Component Basics (Bileşen Temelleri)](#component-basics-bileşen-temelleri)

### Bölüm 3: Derinlemesine Bileşenler (Components In-Depth)
- [Registration (Kayıt)](#registration-kayıt)
- [Props](#props)
- [Events](#events)
- [Component `v-model`](#component-v-model)
- [Fallthrough Attributes](#fallthrough-attributes)
- [Slots](#slots)
- [Provide / Inject](#provide--inject)
- [Async Components](#async-components-asenkron-bileşenler)

---


## Bölüm 1: Giriş (Introduction)

### Vue.js Nedir?

Vue, kullanıcı arayüzleri (UI) oluşturmak için kullanılan, standart HTML, CSS ve JavaScript üzerine inşa edilmiş bir JavaScript framework'üdür. İki temel özelliği vardır:

- **Declarative Rendering:** JavaScript durumuna (state) dayalı olarak HTML çıktısını bildirimsel olarak tanımlar.
- **Reactivity:** JavaScript durumundaki değişiklikleri otomatik olarak izler ve DOM'u verimli bir şekilde günceller.

#### Örnek Kullanım

```vue
<template>
  <div>
    <h1>{{ message }}</h1>
    <button @click="reverseMessage">Mesajı Ters Çevir</button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref('Merhaba Vue!');

function reverseMessage() {
  message.value = message.value.split('').reverse().join('');
}
</script>
```

Bu örnekte, bir butona tıklandığında mesajın ters çevrildiğini görebilirsiniz. Vue, `message` değişkenindeki değişikliği otomatik olarak algılar ve arayüzü günceller.

### Progressive Framework (Aşamalı Çerçeve)

Vue, projenizin ihtiyacına göre aşamalı olarak benimsenebilir. Basit bir HTML sayfasını geliştirmekten, tam teşekküllü Tek Sayfa Uygulamaları (SPA) oluşturmaya kadar geniş bir yelpazede kullanılabilir.

#### Kullanım Senaryoları

1. **CDN ile Hızlı Başlangıç**

   ```html
   <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

   <div id="app">{{ message }}</div>

   <script>
     const { createApp } = Vue;

     createApp({
       data() {
         return {
           message: 'Merhaba Vue!',
         };
       },
     }).mount('#app');
   </script>
   ```

2. **Vite ile Modern Proje**

   ```bash
   # Yeni bir Vue projesi oluştur
   npm create vue@latest

   # Geliştirme sunucusunu başlat
   npm run dev
   ```

3. **Bileşen Tabanlı Geliştirme**

   ```vue
   <!-- components/Button.vue -->
   <template>
     <button class="btn">
       <slot></slot>
     </button>
   </template>

   <style scoped>
   .btn {
     padding: 8px 16px;
     background-color: #42b983;
     color: white;
     border: none;
     border-radius: 4px;
     cursor: pointer;
   }
   </style>
   ```

4. **Vue Router ile SPA**

   ```javascript
   // router/index.js
   import { createRouter, createWebHistory } from 'vue-router';
   import Home from '../views/Home.vue';
   import About from '../views/About.vue';

   const routes = [
     { path: '/', component: Home },
     { path: '/about', component: About },
   ];

   const router = createRouter({
     history: createWebHistory(),
     routes,
   });

   export default router;
   ```

5. **Pinia ile Durum Yönetimi**

   ```javascript
   // stores/counter.js
   import { defineStore } from 'pinia';

   export const useCounterStore = defineStore('counter', {
     state: () => ({
       count: 0,
     }),
     actions: {
       increment() {
         this.count++;
       },
     },
     getters: {
       doubleCount: (state) => state.count * 2,
     },
   });
   ```

6. **Composition API ile Gelişmiş Bileşen**

   ```vue
   <script setup>
   import { ref, computed, onMounted } from 'vue';
   import { useCounterStore } from './stores/counter';

   const counter = useCounterStore();
   const message = ref('Merhaba Vue 3!');

   const reversedMessage = computed(() =>
     message.value.split('').reverse().join(''),
   );

   onMounted(() => {
     console.log('Bileşen oluşturuldu');
   });
   </script>

   <template>
     <div>
       <p>Sayı: {{ counter.count }}</p>
       <button @click="counter.increment">Artır</button>
       <p>Mesaj: {{ message }}</p>
       <p>Ters çevrilmiş: {{ reversedMessage }}</p>
     </div>
   </template>
   ```

7. **Composables ile Mantık Yeniden Kullanımı**

   ```javascript
   // composables/useMouse.js
   import { ref, onMounted, onUnmounted } from 'vue';

   export function useMouse() {
     const x = ref(0);
     const y = ref(0);

     function update(event) {
       x.value = event.pageX;
       y.value = event.pageY;
     }

     onMounted(() => window.addEventListener('mousemove', update));
     onUnmounted(() => window.removeEventListener('mousemove', update));

     return { x, y };
   }

   // Kullanımı:
   // const { x, y } = useMouse();
   ```

8. **Teleport ile DOM Yerleşimi**

   ```vue
   <template>
     <button @click="showModal = true">Modal Aç</button>

     <Teleport to="body">
       <div v-if="showModal" class="modal">
         <p>Bu bir modal penceresidir</p>
         <button @click="showModal = false">Kapat</button>
       </div>
     </Teleport>
   </template>

   <script setup>
   import { ref } from 'vue';

   const showModal = ref(false);
   </script>

   <style scoped>
   .modal {
     position: fixed;
     z-index: 999;
     top: 50%;
     left: 50%;
     transform: translate(-50%, -50%);
     background: white;
     padding: 20px;
     border-radius: 8px;
     box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
   }
   </style>
   ```

### API Stilleri: Options API vs. Composition API

Vue 3'te iki farklı API stili mevcuttur:

#### 1. Options API

```vue
<template>
  <div>
    <p>Sayı: {{ count }}</p>
    <button @click="increment">Artır</button>
  </div>
</template>

<script>
export default {
  // Bileşen verileri
  data() {
    return {
      count: 0,
    };
  },
  // Bileşen metotları
  methods: {
    increment() {
      this.count++;
    },
  },
  // Yaşam döngüsü kancaları
  mounted() {
    console.log('Bileşen oluşturuldu');
  },
  // Hesaplanmış özellikler
  computed: {
    doubleCount() {
      return this.count * 2;
    },
  },
};
</script>
```

#### 2. Composition API

```vue
<template>
  <div>
    <p>Sayı: {{ count }}</p>
    <p>İki katı: {{ doubleCount }}</p>
    <button @click="increment">Artır</button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// Reaktif durum
const count = ref(0);

// Hesaplanmış özellik
const doubleCount = computed(() => count.value * 2);

// Metot
function increment() {
  count.value++;
}

// Yaşam döngüsü kancası
onMounted(() => {
  console.log('Bileşen oluşturuldu');
});
</script>
```

#### Karşılaştırma

| Özellik            | Options API                  | Composition API              |
| ------------------ | ---------------------------- | ---------------------------- |
| Öğrenme Eğrisi     | Daha kolay                   | Biraz daha dik               |
| Kod Organizasyonu  | Seçeneklere göre gruplanır   | Mantığa göre gruplanır       |
| Tekrar Kullanım    | Mixinler                     | Composables                  |
| TypeScript Desteği | Sınırlı                      | Mükemmel                     |
| Karmaşıklık        | Küçük-Orta projeler için iyi | Büyük projeler için daha iyi |

#### Ne Zaman Hangi API Kullanılmalı?

- **Options API** kullanın:

  - Vue'ya yeni başlıyorsanız
  - Küçük-orta ölçekli projelerde
  - Daha basit bileşenler için

- **Composition API** kullanın:
  - Büyük ölçekli uygulamalarda
  - TypeScript ile çalışırken
  - Mantığı yeniden kullanılabilir hale getirmek istediğinizde
  - Daha iyi kod organizasyonu gerektiğinde

#### Örnek: Aynı Bileşen İki Stilde

**Options API ile:**

```vue
<template>
  <div>
    <input v-model="searchQuery" placeholder="Ara..." />
    <ul>
      <li v-for="item in filteredItems" :key="item.id">
        {{ item.name }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: '',
      items: [
        { id: 1, name: 'Elma' },
        { id: 2, name: 'Armut' },
        { id: 3, name: 'Muz' },
      ],
    };
  },
  computed: {
    filteredItems() {
      return this.items.filter((item) =>
        item.name.toLowerCase().includes(this.searchQuery.toLowerCase()),
      );
    },
  },
};
</script>
```

**Composition API ile:**

```vue
<template>
  <div>
    <input v-model="searchQuery" placeholder="Ara..." />
    <ul>
      <li v-for="item in filteredItems" :key="item.id">
        {{ item.name }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const items = ref([
  { id: 1, name: 'Elma' },
  { id: 2, name: 'Armut' },
  { id: 3, name: 'Muz' },
]);

const filteredItems = computed(() =>
  items.value.filter((item) =>
    item.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
  ),
);
</script>
```

Her iki API de aynı sonucu üretir, ancak Composition API daha modüler ve yeniden kullanılabilir bir yapı sunar.

---

## Bölüm 2: Temeller (Essentials)

### Bir Vue Uygulaması Oluşturma

Her Vue uygulaması `createApp` fonksiyonu ile başlar. Bu fonksiyon, uygulamanın kök bileşenini (root component) alır ve `.mount()` metodu ile bir DOM elementine bağlanır.

#### Temel Uygulama Örneği

```javascript
// main.js
import { createApp } from 'vue';
import App from './App.vue';

// Uygulama örneği oluştur
const app = createApp({
  // Uygulama seçenekleri
  data() {
    return {
      title: 'Vue Uygulamam',
    };
  },
});

// Global bileşen kaydı
app.component('MyComponent', {
  template: '<div>Merhaba Dünya!</div>',
});

// Global özellik ekleme
app.config.globalProperties.$apiUrl = 'https://api.ornek.com';

// Uygulamayı DOM'a bağla
app.mount('#app');
```

#### Çoklu Uygulama Örneği

Aynı sayfada birden fazla Vue uygulaması çalıştırabilirsiniz:

```html
<!-- index.html -->
<div id="app1">
  {{ message1 }}
  <button @click="changeMessage">Değiştir</button>
</div>

<div id="app2">
  {{ message2 }}
  <button @click="reverseMessage">Ters Çevir</button>
</div>

<script type="module">
  import { createApp } from 'vue';

  // İlk uygulama
  createApp({
    data() {
      return {
        message1: 'İlk uygulama',
      };
    },
    methods: {
      changeMessage() {
        this.message1 = 'Mesaj değişti!';
      },
    },
  }).mount('#app1');

  // İkinci uygulama
  createApp({
    data() {
      return {
        message2: 'İkinci uygulama',
      };
    },
    methods: {
      reverseMessage() {
        this.message2 = this.message2.split('').reverse().join('');
      },
    },
  }).mount('#app2');
</script>
```

#### Uygulama Yapılandırması

```javascript
import { createApp } from 'vue';
import App from './App.vue';

const app = createApp(App);

// Hata işleme
app.config.errorHandler = (err, vm, info) => {
  console.error('Vue Hatası:', err);
  console.log('Bileşen:', vm);
  console.log('Bilgi:', info);
};

// Uyarıları devre dışı bırak
app.config.warnHandler = (msg, vm, trace) => {
  // Özel uyarı işleme
  console.warn('Uyarı:', msg);
};

// Özel özellikler
app.config.globalProperties.$formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR');
};

// Uygulamayı başlat
app.mount('#app');
```

#### Uygulama Örneği Kullanımı

```vue
<template>
  <div>
    <h1>{{ title }}</h1>
    <p>Bugün: {{ $formatDate(new Date()) }}</p>
    <button @click="fetchData">Veri Getir</button>
    <div v-if="loading">Yükleniyor...</div>
    <div v-else-if="error">{{ error }}</div>
    <ul v-else>
      <li v-for="item in items" :key="item.id">
        {{ item.name }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      title: 'Vue Uygulamam',
      items: [],
      loading: false,
      error: null,
    };
  },
  methods: {
    async fetchData() {
      this.loading = true;
      this.error = null;

      try {
        const response = await fetch(`${this.$apiUrl}/items`);
        if (!response.ok) {
          throw new Error('Veri yüklenirken hata oluştu');
        }
        this.items = await response.json();
      } catch (err) {
        this.error = err.message;
        console.error('Hata:', err);
      } finally {
        this.loading = false;
      }
    },
  },
  created() {
    // Bileşen oluşturulduğunda verileri yükle
    this.fetchData();
  },
};
</script>
```

Bu örnekler, Vue uygulaması oluşturmanın temellerini ve yaygın kullanım senaryolarını göstermektedir. Uygulama yapılandırması, hata işleme ve global özellikler gibi gelişmiş konuları da kapsar.

### Template Syntax (Şablon Sözdizimi)

Vue.js şablon sözdizimi, HTML tabanlıdır ve verileri DOM'a bağlamak için özel sözdizimlerini kullanır. İşte temel özellikleri ve örnekler:

#### 1. Metin İçeriği (Text Interpolation)

```vue
<template>
  <div>
    <!-- Temel metin bağlama -->
    <p>Merhaba, {{ name }}!</p>

    <!-- JavaScript ifadeleri -->
    <p>Toplam: {{ count + 1 }}</p>
    <p>Tam isim: {{ firstName + ' ' + lastName }}</p>
    <p>Mesaj: {{ message.split('').reverse().join('') }}</p>

    <!-- Üçlü operatör -->
    <p>Durum: {{ isActive ? 'Aktif' : 'Pasif' }}</p>

    <!-- Metot çağrıları -->
    <p>Tarih: {{ formatDate(date) }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const name = ref('Vue.js');
const count = ref(10);
const firstName = ref('Ahmet');
const lastName = ref('Yılmaz');
const message = ref('Merhaba Dünya');
const isActive = ref(true);
const date = new Date();

function formatDate(date) {
  return new Date(date).toLocaleDateString('tr-TR');
}
</script>
```

#### 2. Ham HTML (Raw HTML)

```vue
<template>
  <div>
    <!-- Normal metin olarak render edilir -->
    <p>HTML: {{ rawHtml }}</p>

    <!-- Gerçek HTML olarak render edilir -->
    <p>HTML: <span v-html="rawHtml"></span></p>

    <!-- Güvenlik Uyarısı: Kullanıcı girdisiyle kullanmayın! -->
    <div>
      <label>HTML Girin: </label>
      <input v-model="userHtml" placeholder="<strong>Kalın</strong> yazı" />
      <div v-html="userHtml"></div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const rawHtml = ref('<span style="color: red">Kırmızı yazı</span>');
const userHtml = ref('');
</script>
```

#### 3. Özellik Bağlama (Attribute Bindings)

```vue
<template>
  <div>
    <!-- Dinamik özellik bağlama -->
    <div :id="dynamicId">Dinamik ID</div>

    <!-- Boolean özellikler -->
    <button :disabled="isButtonDisabled">Gönder</button>

    <!-- Dinamik sınıf bağlama -->
    <div :class="{ active: isActive, 'text-danger': hasError }">
      Sınıf bağlama örneği
    </div>

    <!-- Dinamik stil bağlama -->
    <div :style="{ color: activeColor, fontSize: fontSize + 'px' }">
      Stil bağlama örneği
    </div>

    <!-- Çoklu stil nesnesi -->
    <div :style="[baseStyles, overridingStyles]">Çoklu stil örneği</div>

    <!-- Dinamik bileşen -->
    <component :is="currentComponent"></component>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const dynamicId = ref('app-container');
const isButtonDisabled = ref(false);
const isActive = ref(true);
const hasError = ref(false);
const activeColor = ref('blue');
const fontSize = ref(16);
const currentComponent = ref('div');

const baseStyles = {
  padding: '10px',
  margin: '10px 0',
  border: '1px solid #ddd',
};

const overridingStyles = {
  color: 'white',
  backgroundColor: '#42b983',
  borderRadius: '4px',
};
</script>
```

#### 4. JavaScript İfadeleri

```vue
<template>
  <div>
    <!-- Matematiksel işlemler -->
    <p>Toplam: {{ 5 + 5 }}</p>

    <!-- Koşullu ifadeler -->
    <p>{{ isLoggedIn ? 'Hoş geldiniz' : 'Lütfen giriş yapın' }}</p>

    <!-- Dizi işlemleri -->
    <p>İlk öğe: {{ numbers[0] }}</p>
    <p>Filtrelenmiş: {{ numbers.filter((n) => n > 2) }}</p>

    <!-- Nesne işlemleri -->
    <p>Kullanıcı: {{ user.name }} ({{ user.age }})</p>

    <!-- Tarih işlemleri -->
    <p>Bugün: {{ new Date().toLocaleDateString('tr-TR') }}</p>

    <!-- Dikkat: Karmaşık mantık için computed property kullanın -->
    <p>Tam isim: {{ `${user.firstName} ${user.lastName}`.toUpperCase() }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isLoggedIn = ref(true);
const numbers = ref([1, 2, 3, 4, 5]);
const user = ref({
  name: 'Ahmet',
  age: 30,
  firstName: 'Ahmet',
  lastName: 'Yılmaz',
});
</script>
```

#### 5. Özel Direktifler

```vue
<template>
  <div>
    <!-- Koşullu Render -->
    <p v-if="seen">Şimdi beni görebilirsin</p>

    <!-- Döngüler -->
    <ul>
      <li v-for="(item, index) in items" :key="item.id">
        {{ index + 1 }}. {{ item.text }}
      </li>
    </ul>

    <!-- Olay Dinleyicileri -->
    <button @click="greet">Selamla</button>

    <!-- Çift Yönlü Veri Bağlama -->
    <input v-model="message" placeholder="Bir şeyler yazın" />
    <p>Yazdıklarınız: {{ message }}</p>

    <!-- Dinamik Özellik -->
    <a :href="url" :title="tooltip" :disabled="isDisabled">Bağlantı</a>

    <!-- Özel Direktif -->
    <p v-highlight="'yellow'">Bu metin vurgulanacak</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const seen = ref(true);
const items = ref([
  { id: 1, text: 'Birinci öğe' },
  { id: 2, text: 'İkinci öğe' },
  { id: 3, text: 'Üçüncü öğe' },
]);
const message = ref('');
const url = ref('https://vuejs.org');
const tooltip = ref('Vue.js web sitesine gider');
const isDisabled = ref(false);

function greet() {
  alert('Merhaba Vue!');
}

// Özel direktif tanımı
const vHighlight = {
  mounted(el, binding) {
    el.style.backgroundColor = binding.value;
  },
  updated(el, binding) {
    el.style.backgroundColor = binding.value;
  },
};
</script>
```

#### 6. Dinamik Argümanlar

```vue
<template>
  <div>
    <!-- Dinamik özellik adı -->
    <div :[attributeName]="attributeValue">Dinamik özellik</div>

    <!-- Dinamik olay adı -->
    <button @[eventName]="handleEvent">Tıkla</button>

    <!-- Dinamik slot adı -->
    <template v-slot:[dynamicSlotName]> Dinamik slot içeriği </template>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const attributeName = ref('title');
const attributeValue = ref('Bu bir başlık');
const eventName = ref('click');
const dynamicSlotName = ref('header');

function handleEvent() {
  console.log('Olay tetiklendi!');
}
</script>
```

Bu örnekler, Vue.js'in şablon sözdiziminin temel özelliklerini kapsar. Bu yapıları kullanarak etkileşimli ve dinamik kullanıcı arayüzleri oluşturabilirsiniz.

### Reactivity Fundamentals (Reaktivitenin Temelleri)

Vue'un reaktivite sistemi, verilerdeki değişiklikleri otomatik olarak takip eder ve bu değişikliklere göre kullanıcı arayüzünü günceller. İki temel reaktif fonksiyon vardır: `ref` ve `reactive`.

#### 1. `ref()` Kullanımı

`ref()`, temel veri tipleri (string, number, boolean, vb.) için kullanılır. İçindeki değere `.value` özelliği ile erişilir.

```vue
<template>
  <div>
    <h1>Sayı: {{ count }}</h1>
    <button @click="increment">Artır</button>

    <h2>Kullanıcı Bilgileri</h2>
    <input v-model="user.name" placeholder="Ad" />
    <input v-model="user.age" type="number" placeholder="Yaş" />
    <p>Merhaba, {{ user.name }} ({{ user.age }})</p>

    <h2>Görev Listesi</h2>
    <input
      v-model="newTodo"
      @keyup.enter="addTodo"
      placeholder="Yeni görev ekle"
    />
    <ul>
      <li v-for="(todo, index) in todos" :key="index">
        {{ todo }}
        <button @click="removeTodo(index)">Sil</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Temel veri tipleri için ref
const count = ref(0);
const newTodo = ref('');

// Nesneler için ref
const user = ref({
  name: 'Ahmet',
  age: 30,
});

// Diziler için ref
const todos = ref(['Alışveriş yap', 'Kod yaz', 'Spor yap']);

// Metotlar
function increment() {
  count.value++;
}

function addTodo() {
  if (newTodo.value.trim()) {
    todos.value.push(newTodo.value);
    newTodo.value = '';
  }
}

function removeTodo(index) {
  todos.value.splice(index, 1);
}

// Refs özellikleri
console.log(count.value); // 0
count.value = 1; // Değer güncelleme

// Ref'i bir nesneyle değiştirme
user.value = { name: 'Mehmet', age: 25 };

// Ref içindeki nesnenin özelliklerini değiştirme
user.value.name = 'Ayşe';
</script>
```

#### 2. `reactive()` Kullanımı

`reactive()`, nesneler, diziler, Map, Set gibi koleksiyonlar için kullanılır. Doğrudan erişim sağlar, `.value` gerekmez.

```vue
<template>
  <div>
    <h1>Kullanıcı Bilgileri</h1>
    <form @submit.prevent="saveUser">
      <div>
        <label>Ad:</label>
        <input v-model="user.firstName" />
      </div>
      <div>
        <label>Soyad:</label>
        <input v-model="user.lastName" />
      </div>
      <div>
        <label>E-posta:</label>
        <input v-model="user.email" type="email" />
      </div>
      <div>
        <label>Rol:</label>
        <select v-model="user.role">
          <option value="user">Kullanıcı</option>
          <option value="admin">Yönetici</option>
          <option value="editor">Editör</option>
        </select>
      </div>
      <button type="submit">Kaydet</button>
    </form>

    <h2>Tercihler</h2>
    <div v-for="(value, key) in preferences" :key="key">
      <label>
        <input type="checkbox" v-model="preferences[key]" />
        {{ key }}
      </label>
    </div>

    <h2>Sepet ({{ cart.size }})</h2>
    <div v-for="[id, item] in cart" :key="id">
      {{ item.name }} - {{ item.quantity }} adet
      <button @click="updateQuantity(id, item.quantity + 1)">+</button>
      <button @click="updateQuantity(id, item.quantity - 1)">-</button>
      <button @click="removeFromCart(id)">Kaldır</button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';

// Kullanıcı nesnesi
const user = reactive({
  firstName: 'Ahmet',
  lastName: 'Yılmaz',
  email: 'ahmet@example.com',
  role: 'user',
  preferences: {
    newsletter: true,
    notifications: false,
  },
});

// Tercihler
const preferences = reactive({
  darkMode: false,
  notifications: true,
  newsletter: true,
});

// Alışveriş sepeti (Map kullanımı)
const cart = reactive(
  new Map([
    [1, { id: 1, name: 'Ürün 1', quantity: 2, price: 99.99 }],
    [2, { id: 2, name: 'Ürün 2', quantity: 1, price: 149.99 }],
  ]),
);

// Sepet işlemleri
function addToCart(product) {
  if (cart.has(product.id)) {
    const item = cart.get(product.id);
    item.quantity++;
  } else {
    cart.set(product.id, { ...product, quantity: 1 });
  }
}

function updateQuantity(productId, newQuantity) {
  if (newQuantity <= 0) {
    cart.delete(productId);
  } else {
    const item = cart.get(productId);
    if (item) {
      item.quantity = newQuantity;
    }
  }
}

function removeFromCart(productId) {
  cart.delete(productId);
}

// Kullanıcıyı kaydet
function saveUser() {
  console.log('Kullanıcı kaydediliyor:', user);
  // API çağrısı yapılabilir
}

// Reactive özellikleri
console.log(user.firstName); // Doğrudan erişim
user.firstName = 'Mehmet'; // Doğrudan atama

// Dizi işlemleri
const todos = reactive(['Alışveriş', 'Temizlik', 'Spor']);
todos.push('Kitap oku'); // Reaktif olarak çalışır
</script>
```

#### 3. `ref()` vs `reactive()` Karşılaştırması

| Özellik              | `ref()`                      | `reactive()`                            |
| -------------------- | ---------------------------- | --------------------------------------- |
| Kullanım             | `const count = ref(0)`       | `const state = reactive({ count: 0 })`  |
| Değer Erişimi        | `.value` gerekir             | Doğrudan erişim                         |
| Yeniden Atama        | Tamamen değiştirilebilir     | Referans korunmalıdır                   |
| Template'te Kullanım | Otomatik unwrap edilir       | Doğrudan kullanılır                     |
| İç İçe Nesneler      | `.value` ile erişilir        | Doğrudan erişim                         |
| Watch İzleme         | Tüm değişiklikleri yakalar   | Sadece özellik değişikliklerini yakalar |
| Kullanım Alanı       | Temel tipler, basit durumlar | Karmaşık nesneler, form durumları       |

#### 4. Reaktivite Örnekleri

```vue
<template>
  <div>
    <h2>Reaktivite Örnekleri</h2>

    <div>
      <h3>1. Temel Reaktivite</h3>
      <p>Sayı: {{ counter }}</p>
      <button @click="counter++">Artır</button>

      <h3>2. Dizi İşlemleri</h3>
      <input v-model="newItem" @keyup.enter="addItem" />
      <ul>
        <li v-for="(item, index) in items" :key="index">
          {{ item }}
          <button @click="removeItem(index)">Sil</button>
        </li>
      </ul>

      <h3>3. Derin Reaktivite</h3>
      <div>
        <p>Kullanıcı: {{ user.name }} ({{ user.age }})</p>
        <button @click="user.age++">Yaşını Artır</button>
        <button @click="user = { name: 'Yeni Kullanıcı', age: 18 }">
          Kullanıcıyı Değiştir
        </button>
      </div>

      <h3>4. Refs in Reactive</h3>
      <p>Ad: {{ formData.name }}</p>
      <p>Soyad: {{ formData.surname }}</p>
      <p>Tam Ad: {{ fullName }}</p>

      <h3>5. Reactivity Transform (Deneysel)</h3>
      <p>Dakika: {{ minutes }}</p>
      <p>Saniye: {{ seconds }}</p>
      <button @click="increment">Artır</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, toRefs } from 'vue';

// 1. Temel reaktivite
const counter = ref(0);

// 2. Dizi işlemleri
const items = ref(['Elma', 'Armut', 'Muz']);
const newItem = ref('');

function addItem() {
  if (newItem.value.trim()) {
    items.value.push(newItem.value);
    newItem.value = '';
  }
}

function removeItem(index) {
  items.value.splice(index, 1);
}

// 3. Derin reaktivite
const user = reactive({
  name: 'Ahmet',
  age: 30,
  address: {
    city: 'İstanbul',
    country: 'Türkiye',
  },
});

// 4. Refs in Reactive
const firstName = ref('Ahmet');
const lastName = ref('Yılmaz');

const formData = reactive({
  name: firstName,
  surname: lastName,
});

const fullName = computed(() => {
  return `${formData.name} ${formData.surname}`;
});

// 5. Reactivity Transform (Deneysel)
// Bu özellik Vue 3.2+ ile deneysel olarak mevcuttur
// Önizleme: https://vuejs.org/guide/extras/reactivity-transform.html
// Bu özelliği kullanmak için @vue/compiler-sfc eklentisi gerekir
let minutes = $ref(0);
let seconds = $ref(0);

function increment() {
  seconds++;
  if (seconds >= 60) {
    minutes++;
    seconds = 0;
  }
}

// 6. Watch Kullanımı
watch(counter, (newValue, oldValue) => {
  console.log(`Sayaç değişti: ${oldValue} -> ${newValue}`);
});

watch(
  () => user.age,
  (newAge, oldAge) => {
    console.log(`Yaş değişti: ${oldAge} -> ${newAge}`);
  },
);

// 7. Deep Watching
watch(
  () => user,
  (newUser) => {
    console.log('Kullanıcı bilgileri değişti:', newUser);
  },
  { deep: true },
);

// 8. Multiple Sources
watch([counter, () => user.age], ([newCount, newAge], [oldCount, oldAge]) => {
  console.log(`Sayı: ${oldCount} -> ${newCount}, Yaş: ${oldAge} -> ${newAge}`);
});

// 9. toRefs ile Reactivity
const { name, age } = toRefs(user);
// Artık name ve age reaktif referanslardır
</script>
```

Bu örnekler, Vue'un reaktivite sisteminin temel kavramlarını ve nasıl kullanılacağını göstermektedir. `ref` ve `reactive` arasındaki farkları anlamak, Vue uygulamalarında daha etkili durum yönetimi yapmanıza yardımcı olacaktır.

### Computed Properties (Hesaplanmış Özellikler)

- Şablon içinde karmaşık mantık yerine `computed` kullanılır.
- Bağımlı olduğu reaktif veriler değiştiğinde yeniden hesaplanır ve sonuç önbelleğe (cache) alınır. Bu sayede performans artışı sağlar.

### Class ve Style Bindings

Vue'da class ve style binding'leri, HTML öğelerinin görünümünü dinamik olarak değiştirmek için kullanılır. İşte detaylı örnekler:

#### 1. Class Binding

```vue
<template>
  <div>
    <!-- Dinamik class ekleme -->
    <div :class="{ active: isActive, 'text-danger': hasError }">
      Dinamik Class Örneği
    </div>

    <!-- Dizi sözdizimi -->
    <div :class="[isActive ? 'active' : '', errorClass]">
      Dizi Sözdizimi
    </div>

    <!-- Bileşenlerle kullanım -->
    <my-component :class="{ active: isActive }"></my-component>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isActive = ref(true);
const hasError = ref(false);
const errorClass = 'text-danger';
</script>

<style>
.active {
  font-weight: bold;
  color: #42b983;
}
.text-danger {
  color: #ff5252;
}
</style>
```

#### 2. Style Binding

```vue
<template>
  <div>
    <!-- Dinamik stil -->
    <div :style="{ color: activeColor, fontSize: fontSize + 'px' }">
      Dinamik Stil
    </div>

    <!-- Çoklu stil nesnesi -->
    <div :style="[baseStyles, overridingStyles]">
      Çoklu Stil
    </div>

    <!-- Otomatik ön ek ekleme -->
    <div :style="{ display: ['-webkit-box', '-ms-flexbox', 'flex'] }">
      Vendor Prefix Örneği
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const activeColor = ref('red');
const fontSize = ref(16);

const baseStyles = reactive({
  padding: '10px',
  margin: '5px',
  border: '1px solid #ddd'
});

const overridingStyles = reactive({
  color: 'blue',
  fontSize: '18px'
});
</script>
```

#### 3. Gelişmiş Kullanım Örnekleri

```vue
<template>
  <div>
    <!-- Koşullu stil sınıfları -->
    <button
      :class="[
        'btn',
        {
          'btn-primary': type === 'primary',
          'btn-danger': type === 'danger',
          'btn-disabled': isDisabled
        },
        isLarge ? 'btn-lg' : ''
      ]"
      :disabled="isDisabled"
    >
      {{ buttonText }}
    </button>

    <!-- Dinamik stil hesaplamaları -->
    <div 
      :style="{
        transform: `scale(${scale}) rotate(${rotation}deg)`,
        opacity: isVisible ? 1 : 0,
        transition: 'all 0.3s ease'
      }"
      class="box"
    >
      Animasyonlu Kutu
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const type = ref('primary');
const isDisabled = ref(false);
const isLarge = ref(true);
const scale = ref(1);
const rotation = ref(0);
const isVisible = ref(true);

const buttonText = computed(() => {
  if (isDisabled.value) return 'Devre Dışı';
  return type.value === 'primary' ? 'Birincil Buton' : 'Tehlikeli İşlem';
});

// Animasyon için döngü
setInterval(() => {
  scale.value = 1 + Math.sin(Date.now() / 500) * 0.1;
  rotation.value = (rotation.value + 1) % 360;
}, 16);
</script>

<style scoped>
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #42b983;
  color: white;
}

.btn-danger {
  background-color: #ff5252;
  color: white;
}

.btn-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-lg {
  padding: 12px 24px;
  font-size: 16px;
}

.box {
  width: 100px;
  height: 100px;
  background-color: #42b983;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
  border-radius: 8px;
}
</style>
```

Bu örnekler, Vue'da class ve style binding'lerinin nasıl kullanılabileceğini göstermektedir. Dinamik sınıflar ve stiller oluşturarak, kullanıcı etkileşimlerine ve uygulama durumuna göre görünümü değiştirebilirsiniz.

- `:class` ile bir elemente dinamik olarak CSS class'ları eklenir. (Örn: `:class="{ active: isActive }"`)
- `:style` ile bir elemente dinamik olarak inline stiller verilir. (Örn: `:style="{ color: activeColor, fontSize: size + 'px' }"`)

### Conditional Rendering (Koşullu Render)

Vue'da koşullu render etme, belirli koşullara göre DOM öğelerini göstermek veya gizlemek için kullanılır. İşte detaylı örnekler:

#### 1. Temel Kullanım

```vue
<template>
  <div>
    <!-- v-if temel kullanımı -->
    <div v-if="isLoggedIn">
      Hoş geldiniz, {{ user.name }}!
    </div>
    <div v-else>
      Lütfen giriş yapın.
    </div>

    <!-- v-else-if ile çoklu koşul -->
    <div v-if="score >= 90">Mükemmel!</div>
    <div v-else-if="score >= 70">İyi iş!</div>
    <div v-else-if="score >= 50">Orta</div>
    <div v-else>Daha çok çalışmalısınız.</div>

    <!-- v-show kullanımı -->
    <div v-show="isActive">Bu element v-show ile gösteriliyor</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isLoggedIn = ref(true);
const user = ref({ name: 'Ahmet Yılmaz' });
const score = ref(85);
const isActive = ref(true);
</script>
```

#### 2. `v-if` vs `v-show` Karşılaştırması

```vue
<template>
  <div>
    <div class="demo-container">
      <div>
        <h3>v-if Kullanımı</h3>
        <button @click="showIf = !showIf">
          {{ showIf ? 'Gizle' : 'Göster' }}
        </button>
        <p v-if="showIf" class="demo-box">
          Bu içerik v-if ile kontrol ediliyor. DOM'dan tamamen kaldırılır.
        </p>
      </div>

      <div>
        <h3>v-show Kullanımı</h3>
        <button @click="showShow = !showShow">
          {{ showShow ? 'Gizle' : 'Göster' }}
        </button>
        <p v-show="showShow" class="demo-box">
          Bu içerik v-show ile kontrol ediliyor. Sadece CSS ile gizlenir.
        </p>
      </div>
    </div>

    <div class="performance-note">
      <h4>Performans Notu:</h4>
      <p><strong>v-if</strong> gerçek DOM manipülasyonu yapar ve daha maliyetlidir.</p>
      <p><strong>v-show</strong> sadece CSS ile çalışır ve daha hafiftir.</p>
      <p>✅ Sık değişen durumlar için <strong>v-show</strong> kullanın.</p>
      <p>✅ Nadiren değişen durumlar için <strong>v-if</strong> kullanın.</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const showIf = ref(true);
const showShow = ref(true);
</script>

<style scoped>
.demo-container {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
}

.demo-box {
  padding: 1rem;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 0.5rem;
}

.performance-note {
  background-color: #e3f2fd;
  padding: 1rem;
  border-radius: 4px;
  border-right: 4px solid #2196f3;
}

button {
  padding: 0.5rem 1rem;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #3aa876;
}
</style>
```

#### 3. Gelişmiş Kullanım Örnekleri

```vue
<template>
  <div>
    <!-- Dinamik bileşen yükleme -->
    <component :is="currentComponent"></component>
    
    <!-- v-if ile template etiketi kullanımı -->
    <template v-if="user.role === 'admin'">
      <h3>Yönetici Paneli</h3>
      <admin-dashboard />
      <user-list />
    </template>
    
    <!-- v-if ile birlikte v-for kullanımı (önerilmez, dikkatli kullanın) -->
    <ul>
      <template v-for="item in items" :key="item.id">
        <li v-if="!item.hidden">
          {{ item.name }} - {{ item.price }} TL
        </li>
      </template>
    </ul>
    
    <!-- Dinamik bileşenler ve keep-alive -->
    <keep-alive>
      <component :is="activeTab"></component>
    </keep-alive>
  </div>
</template>

<script setup>
import { ref, shallowRef } from 'vue';
import AdminDashboard from './AdminDashboard.vue';
import UserList from './UserList.vue';
import UserProfile from './UserProfile.vue';
import Settings from './Settings.vue';

const currentComponent = ref('user-profile');
const activeTab = ref('profile');

const user = ref({
  name: 'Ahmet Yılmaz',
  role: 'admin',
  isActive: true
});

const items = ref([
  { id: 1, name: 'Ürün 1', price: 100, hidden: false },
  { id: 2, name: 'Ürün 2', price: 150, hidden: true },
  { id: 3, name: 'Ürün 3', price: 200, hidden: false }
]);

// Dinamik bileşenler
const tabs = {
  profile: shallowRef(UserProfile),
  settings: shallowRef(Settings)
};
</script>
```

#### 4. Performans İpuçları

1. **`v-if` ve `v-for'u birlikte kullanmaktan kaçının**:
   ```vue
   <!-- Kötü Kullanım -->
   <ul>
     <li v-for="item in items" v-if="!item.hidden" :key="item.id">
       {{ item.name }}
     </li>
   </ul>
   
   <!-- İyi Kullanım -->
   <ul>
     <template v-for="item in items" :key="item.id">
       <li v-if="!item.hidden">
         {{ item.name }}
       </li>
     </template>
   </ul>
   ```

2. **Ağır hesaplamaları `v-if` içinde yapmayın**:
   ```vue
   <!-- Kötü Kullanım -->
   <div v-if="filteredItems().length > 0">
     <!-- ... -->
   </div>
   
   <!-- İyi Kullanım -->
   <div v-if="hasFilteredItems">
     <!-- ... -->
   </div>
   
   <script setup>
   const filteredItems = computed(() => {
     // Ağır hesaplama
     return items.value.filter(/* ... */);
   });
   
   const hasFilteredItems = computed(() => filteredItems.value.length > 0);
   </script>
   ```

3. **`v-show` ile birlikte `v-if` kullanmayın**:
   ```vue
   <!-- Gereksiz Kullanım -->
   <div v-show="isVisible" v-if="isActive">
     <!-- ... -->
   </div>
   
   <!-- Daha İyi -->
   <div v-show="isVisible && isActive">
     <!-- ... -->
   </div>
   ```

Bu örnekler, Vue'da liste render etmenin temel ve gelişmiş kullanımlarını göstermektedir. Uygulamanızın ihtiyaçlarına göre bu örnekleri özelleştirebilirsiniz.

### Event Handling (Olay Yönetimi)

- **`v-on:` veya kısaca `@`:** DOM olaylarını dinlemek için kullanılır. (Örn: `@click="handler"`)
- **Event Modifiers:** Olay davranışını değiştiren son eklerdir. (Örn: `@click.prevent`, `@submit.stop`, `@keyup.enter`)

### Form Input Bindings (Form Girdi Bağlama)

- **`v-model`:** Form elemanları (`<input>`, `<textarea>`, `<select>`) ile state arasında iki yönlü veri bağlamayı (two-way binding) sağlar.
- **Modifiers:** `.lazy` (change olayı sonrası günceller), `.number` (sayıya çevirir), `.trim` (boşlukları temizler).

### Watchers (Gözlemciler)

- `watch` veya `watchEffect` ile bir veri kaynağındaki değişiklikleri izleyip, değiştiğinde bir yan etki (asenkron işlem, API çağrısı vb.) çalıştırmak için kullanılır.

### Template Refs

- `ref` attribute'u ile bir DOM elementine veya alt bileşen örneğine doğrudan erişim sağlanır.

### Component Basics (Bileşen Temelleri)

- **Props:** Üst bileşenden alt bileşene veri aktarmanın yoludur. Veri akışı tek yönlüdür (üstten alta).
- **Events:** Alt bileşen, üst bileşene bir şeylerin olduğunu bildirmek için `$emit` ile olay (event) yayar.
- **Slots:** Üst bileşenin, alt bileşenin şablonuna içerik (HTML) göndermesini sağlar. Bu, bileşenleri daha esnek ve yeniden kullanılabilir hale getirir.
-

#### 1. Props (Özellikler)

```vue
<!-- ParentComponent.vue -->
<template>
  <div>
    <h2>Ebeveyn Bileşen</h2>
    <child-component
      :title="message"
      :user="user"
      :is-visible="isVisible"
      @update:title="updateTitle"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ChildComponent from './ChildComponent.vue';

const message = ref('Merhaba Dünya');
const isVisible = ref(true);
const user = ref({
  id: 1,
  name: 'Ahmet',
  email: 'ahmet@example.com',
});

function updateTitle(newTitle) {
  message.value = newTitle;
}
</script>

<!-- ChildComponent.vue -->
<template>
  <div v-if="isVisible" class="child">
    <h3>{{ title }}</h3>
    <p>Kullanıcı: {{ user.name }} ({{ user.email }})</p>
    <button @click="updateTitle('Yeni Başlık')">Başlığı Değiştir</button>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
    default: 'Varsayılan Başlık',
    validator: (value) => value.length > 0,
  },
  user: {
    type: Object,
    required: true,
    default: () => ({
      id: 0,
      name: 'Misafir',
      email: '',
    }),
  },
  isVisible: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['update:title']);

function updateTitle(newTitle) {
  emit('update:title', newTitle);
}
</script>

<style scoped>
.child {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 1rem;
}
</style>
```

#### 2. Events (Olaylar)

```vue
<!-- EventExample.vue -->
<template>
  <div>
    <h3>Olay Örneği</h3>
    <button @click="increment">Artır ({{ count }})</button>
    <button @click="decrement">Azalt</button>

    <!-- Özel Olaylar -->
    <custom-button
      @custom-click="handleCustomClick"
      @custom-submit="handleCustomSubmit"
    />

    <!-- Olay Modifier'ları -->
    <div
      @click.stop="handleClick"
      @keyup.enter="handleEnter"
      @submit.prevent="handleSubmit"
    >
      <button type="button" @click.middle="handleMiddleClick">
        Orta Tıkla
      </button>
      <input type="text" @keyup.space="handleSpace" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import CustomButton from './CustomButton.vue';

const count = ref(0);

function increment() {
  count.value++;
  // Özel bir olay yayınlama
  emit('count-updated', count.value);
}

function decrement() {
  if (count.value > 0) {
    count.value--;
    emit('count-updated', count.value);
  }
}

function handleCustomClick(data) {
  console.log('Özel tıklama:', data);
}

function handleCustomSubmit(formData) {
  console.log('Form gönderildi:', formData);
}

// Diğer olay işleyicileri
function handleClick() {
  console.log('Tıklandı');
}

function handleEnter() {
  console.log('Enter tuşuna basıldı');
}

function handleSubmit() {
  console.log('Form gönderildi');
}

function handleMiddleClick() {
  console.log('Orta düğmeye tıklandı');
}

function handleSpace() {
  console.log('Boşluk tuşuna basıldı');
}
</script>
```

#### 3. Slots (Yuvalar)

```vue
<!-- CardComponent.vue -->
<template>
  <div class="card">
    <!-- Varsayılan slot -->
    <div class="card-header">
      <slot name="header">
        <!-- Varsayılan başlık -->
        <h3>Varsayılan Başlık</h3>
      </slot>
    </div>

    <!-- Ana içerik -->
    <div class="card-body">
      <slot></slot>
    </div>

    <!-- Alt bilgi -->
    <div class="card-footer">
      <slot name="footer">
        <button @click="$emit('close')">Kapat</button>
      </slot>
    </div>
  </div>
</template>

<script setup>
// Props tanımlamaları
const props = defineProps({
  title: String,
  showFooter: {
    type: Boolean,
    default: true,
  },
});

// Olayları tanımlama
const emit = defineEmits(['close']);
</script>

<style scoped>
.card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  margin: 1rem 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
}

.card-footer {
  border-top: 1px solid #eee;
  padding-top: 0.5rem;
  margin-top: 1rem;
  text-align: right;
}
</style>

<!-- Kullanımı -->
<template>
  <card-component title="Kullanıcı Profili">
    <!-- İsimli slot kullanımı -->
    <template #header>
      <h3>Kullanıcı Bilgileri</h3>
      <p>Son Giriş: {{ lastLogin }}</p>
    </template>

    <!-- Varsayılan slot içeriği -->
    <p>Ad: {{ user.name }}</p>
    <p>E-posta: {{ user.email }}</p>

    <!-- İsimli slot kullanımı -->
    <template #footer>
      <button @click="saveUser">Kaydet</button>
      <button @click="$emit('close')">Kapat</button>
    </template>
  </card-component>
</template>

<script setup>
import { ref } from 'vue';
import CardComponent from './CardComponent.vue';

const user = ref({
  name: 'Ahmet Yılmaz',
  email: 'ahmet@example.com',
});

const lastLogin = ref(new Date().toLocaleString());

function saveUser() {
  console.log('Kullanıcı kaydedildi:', user.value);
}
</script>
```

### Lifecycle Hooks (Yaşam Döngüsü Kancaları)

Bir bileşenin oluşturulması, DOM'a eklenmesi, güncellenmesi ve yok edilmesi gibi farklı aşamalarında özel kod çalıştırmayı sağlayan fonksiyonlardır. (Örn: `onMounted`, `onUpdated`, `onUnmounted`)

---

## Bölüm 3: Derinlemesine Bileşenler (Components In-Depth)

### Registration (Kayıt)

- **Global:** `app.component()` ile kaydedilir ve uygulamanın her yerinde kullanılabilir.
- **Local:** Bir bileşenin `components` seçeneğinde veya `<script setup>` içinde `import` edilerek kaydedilir. Sadece o bileşen içinde kullanılabilir. Genellikle tercih edilen yöntemdir.

### Props

- `props` tanımlanırken tip (`type`), varsayılan değer (`default`) ve zorunluluk (`required`) gibi doğrulamalar yapılabilir.
- Veri akışı daima **tek yönlüdür**. Alt bileşen, bir `prop`'u doğrudan değiştirmemelidir.

### Events

- `emits` seçeneği ile bir bileşenin hangi olayları yayacağı deklare edilebilir ve doğrulanabilir.

### Component `v-model`

#### 1. Temel Kullanım

```vue
<!-- CustomInput.vue -->
<template>
  <div class="custom-input">
    <label v-if="label">{{ label }}</label>
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      v-bind="$attrs"
    />
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  }
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
.custom-input {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.error {
  color: #f44336;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style>

<!-- Kullanımı -->
<template>
  <div>
    <h2>Kullanıcı Kaydı</h2>
    
    <custom-input
      v-model="user.name"
      label="Ad Soyad"
      placeholder="Adınızı giriniz"
      :error="errors.name"
      @blur="validateField('name')"
    />
    
    <custom-input
      v-model="user.email"
      type="email"
      label="E-posta"
      placeholder="E-posta adresinizi giriniz"
      :error="errors.email"
      @blur="validateField('email')"
    />
    
    <custom-input
      v-model="user.password"
      type="password"
      label="Şifre"
      placeholder="Şifrenizi giriniz"
      :error="errors.password"
      @blur="validateField('password')"
    />
    
    <button @click="submitForm">Kaydet</button>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import CustomInput from './CustomInput.vue';

const user = reactive({
  name: '',
  email: '',
  password: ''
});

const errors = reactive({
  name: '',
  email: '',
  password: ''
});

function validateField(field) {
  // Basit doğrulama örneği
  if (!user[field]) {
    errors[field] = 'Bu alan zorunludur';
  } else if (field === 'email' && !isValidEmail(user.email)) {
    errors.email = 'Geçerli bir e-posta adresi giriniz';
  } else if (field === 'password' && user.password.length < 6) {
    errors.password = 'Şifre en az 6 karakter olmalıdır';
  } else {
    errors[field] = '';
  }
}

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function submitForm() {
  // Tüm alanları doğrula
  Object.keys(user).forEach(field => validateField(field));
  
  // Hata kontrolü
  const hasErrors = Object.values(errors).some(error => error !== '');
  
  if (!hasErrors) {
    console.log('Form gönderildi:', user);
    // API çağrısı yapılabilir
  } else {
    console.log('Lütfen formdaki hataları düzeltin');
  }
}
</script>
```

#### 2. Çoklu v-model Kullanımı (Vue 3+)

```vue
<!-- UserForm.vue -->
<template>
  <div class="user-form">
    <div class="form-group">
      <label>Ad:</label>
      <input 
        :value="firstName" 
        @input="$emit('update:firstName', $event.target.value)"
      />
    </div>
    
    <div class="form-group">
      <label>Soyad:</label>
      <input 
        :value="lastName" 
        @input="$emit('update:lastName', $event.target.value)"
      />
    </div>
    
    <div class="form-group">
      <label>Yaş:</label>
      <input 
        type="number" 
        :value="age" 
        @input="$emit('update:age', parseInt($event.target.value) || 0)"
      />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  firstName: String,
  lastName: String,
  age: Number
});

defineEmits(['update:firstName', 'update:lastName', 'update:age']);
</script>

<style scoped>
.user-form {
  max-width: 400px;
  margin: 0 auto;
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}
</style>

<!-- Kullanımı -->
<template>
  <div>
    <h2>Kullanıcı Bilgileri</h2>
    
    <user-form
      v-model:firstName="user.firstName"
      v-model:lastName="user.lastName"
      v-model:age="user.age"
    />
    
    <div class="preview">
      <h3>Önizleme:</h3>
      <p>Ad: {{ user.firstName }}</p>
      <p>Soyad: {{ user.lastName }}</p>
      <p>Yaş: {{ user.age }}</p>
      <p>Tam İsim: {{ fullName }}</p>
    </div>
    
    <button @click="saveUser">Kullanıcıyı Kaydet</button>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import UserForm from './UserForm.vue';

const user = reactive({
  firstName: 'Ahmet',
  lastName: 'Yılmaz',
  age: 30
});

const fullName = computed(() => {
  return `${user.firstName} ${user.lastName}`.trim();
});

function saveUser() {
  console.log('Kullanıcı kaydedildi:', {
    ...user,
    fullName: fullName.value
  });
  
  // API çağrısı yapılabilir
  // await api.saveUser(user);
}
</script>

<style scoped>
.preview {
  margin: 2rem 0;
  padding: 1rem;
  background-color: #f5f5f5;
  border-radius: 4px;
}

button {
  padding: 0.5rem 1rem;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

button:hover {
  background-color: #3aa876;
}
</style>
```

#### 3. Özel v-model Modelleri

```vue
<!-- CustomCheckbox.vue -->
<template>
  <label class="custom-checkbox">
    <input
      type="checkbox"
      :checked="modelValue"
      @change="$emit('update:modelValue', $event.target.checked)"
      v-bind="$attrs"
    />
    <span class="checkmark"></span>
    <span class="label"><slot></slot></span>
  </label>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
.custom-checkbox {
  display: inline-flex;
  align-items: center;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  user-select: none;
}

/* Gizle varsayılan checkbox */
.custom-checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Özel checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 4px;
  transition: all 0.3s;
}

/* Hover durumunda arkaplan rengi */
.custom-checkbox:hover input ~ .checkmark {
  background-color: #ccc;
}

/* Checkbox işaretli olduğunda arkaplan rengi */
.custom-checkbox input:checked ~ .checkmark {
  background-color: #42b983;
}

/* Checkmark (gizli) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Checkmark göster */
.custom-checkbox input:checked ~ .checkmark:after {
  display: block;
}

/* Checkmark stili */
.custom-checkbox .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  transform: rotate(45deg);
}

.label {
  margin-left: 8px;
}
</style>

<!-- Kullanımı -->
<template>
  <div class="settings">
    <h2>Ayarlar</h2>
    
    <div class="setting-item">
      <custom-checkbox v-model="settings.darkMode">
        Karanlık Tema
      </custom-checkbox>
    </div>
    
    <div class="setting-item">
      <custom-checkbox v-model="settings.notifications">
        Bildirimleri Etkinleştir
      </custom-checkbox>
    </div>
    
    <div class="setting-item">
      <custom-checkbox v-model="settings.analytics">
        Analiz Topla
      </custom-checkbox>
    </div>
    
    <button @click="saveSettings">Ayarları Kaydet</button>
    
    <div v-if="showSaved" class="saved-message">
      Ayarlar kaydedildi!
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import CustomCheckbox from './CustomCheckbox.vue';

const settings = reactive({
  darkMode: false,
  notifications: true,
  analytics: false
});

const showSaved = ref(false);

function saveSettings() {
  console.log('Ayarlar kaydedildi:', settings);
  
  // Ayarları localStorage'a kaydet
  localStorage.setItem('appSettings', JSON.stringify(settings));
  
  // Kaydedildi mesajını göster
  showSaved.value = true;
  
  // 3 saniye sonra mesajı gizle
  setTimeout(() => {
    showSaved.value = false;
  }, 3000);
}

// Sayfa yüklendiğinde kayıtlı ayarları yükle
function loadSettings() {
  const savedSettings = localStorage.getItem('appSettings');
  if (savedSettings) {
    Object.assign(settings, JSON.parse(savedSettings));
  }
}

// Bileşen oluşturulduğunda ayarları yükle
loadSettings();
</script>

<style scoped>
.settings {
  max-width: 500px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.setting-item {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background-color: #f9f9f9;
  border-radius: 6px;
  transition: background-color 0.3s;
}

.setting-item:hover {
  background-color: #f0f0f0;
}

button {
  display: block;
  width: 100%;
  padding: 0.75rem;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1.5rem;
}

button:hover {
  background-color: #3aa876;
}

.saved-message {
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: #d4edda;
  color: #155724;
  border-radius: 4px;
  text-align: center;
  animation: fadeOut 3s forwards;
  animation-delay: 1s;
}

@keyframes fadeOut {
  from { opacity: 1; }
  to { opacity: 0; }
}
</style>

### Fallthrough Attributes

Varsayılan olarak, bir bileşene iletilen ancak `props` veya `emits` olarak tanımlanmamış özellikler (attributes), bileşenin kök elementine otomatik olarak uygulanır. Bu davranışı kontrol etmek için `inheritAttrs: false` kullanılabilir ve `$attrs` kullanılarak özellikler manuel olarak başka bir öğeye uygulanabilir.

#### 1. Temel Kullanım

```vue
<!-- BaseButton.vue -->
<template>
  <button class="btn" v-bind="$attrs">
    <slot></slot>
  </button>
</template>

<script setup>
// inheritAttrs: false yapılmadığı için tüm özellikler otomatik olarak kök elemente uygulanır
</script>

<style scoped>
.btn {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #f5f5f5;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s;
}

.btn:hover {
  background-color: #e0e0e0;
}
</style>

<!-- Kullanımı -->
<template>
  <div>
    <h2>Düğmeler</h2>
    
    <base-button 
      class="primary" 
      @click="sayHello"
      title="Merhaba Düğmesi"
      data-testid="hello-button"
    >
      Tıkla Bana
    </base-button>
    
    <base-button 
      type="submit" 
      :disabled="isLoading"
      @mouseover="showTooltip = true"
      @mouseleave="showTooltip = false"
    >
      <span v-if="isLoading">Yükleniyor...</span>
      <span v-else>Gönder</span>
    </base-button>
    
    <div v-if="showTooltip" class="tooltip">
      Bu düğmeye tıklayarak formu gönderebilirsiniz
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import BaseButton from './BaseButton.vue';

const isLoading = ref(false);
const showTooltip = ref(false);

function sayHello() {
  alert('Merhaba! Düğmeye tıklandı!');
}
</script>

<style scoped>
.tooltip {
  margin-top: 1rem;
  padding: 0.5rem;
  background-color: #333;
  color: white;
  border-radius: 4px;
  font-size: 0.875rem;
  max-width: 200px;
}

.primary {
  background-color: #42b983;
  color: white;
  border-color: #3aa876;
}

.primary:hover {
  background-color: #3aa876;
}
</style>
```

#### 2. `inheritAttrs: false` ile Özelleştirilmiş Davranış

```vue
<!-- CustomInput.vue -->
<template>
  <div class="form-group">
    <label v-if="label" :for="id">{{ label }}</label>
    <input
      :id="id"
      :value="modelValue"
      v-bind="{
        ...$attrs,
        class: `form-control ${$attrs.class || ''}`,
        onInput: $event => $emit('update:modelValue', $event.target.value)
      }"
    />
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    error: {
      type: String,
      default: ''
    },
    id: {
      type: String,
      required: true
    }
  },
  emits: ['update:modelValue'],
  setup(props, { attrs }) {
    // $attrs içeriğini inceleyebiliriz
    console.log('CustomInput attrs:', attrs);
    
    return {};
  }
};
</script>

<style scoped>
.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
  border-color: #42b983;
  box-shadow: 0 0 0 2px rgba(66, 185, 131, 0.2);
  outline: none;
}

.error-message {
  color: #f44336;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style>

<!-- Kullanımı -->
<template>
  <div class="form-container">
    <h2>Kullanıcı Bilgileri</h2>
    
    <form @submit.prevent="handleSubmit">
      <custom-input
        v-model="formData.username"
        id="username"
        label="Kullanıcı Adı"
        placeholder="Kullanıcı adınızı giriniz"
        :error="errors.username"
        autocomplete="username"
        required
        @focus="clearError('username')"
      />
      
      <custom-input
        v-model="formData.email"
        id="email"
        type="email"
        label="E-posta Adresi"
        placeholder="ornek@email.com"
        :error="errors.email"
        autocomplete="email"
        required
        @focus="clearError('email')"
        class="email-input"
      />
      
      <custom-input
        v-model="formData.phone"
        id="phone"
        type="tel"
        label="Telefon Numarası"
        placeholder="(5__) ___ __ __"
        :error="errors.phone"
        pattern="[0-9]{10}"
        @focus="clearError('phone')"
      />
      
      <button type="submit" class="submit-btn">
        Kaydet
      </button>
    </form>
    
    <div v-if="isSubmitted" class="success-message">
      Form başarıyla gönderildi!
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import CustomInput from './CustomInput.vue';

const formData = reactive({
  username: '',
  email: '',
  phone: ''
});

const errors = reactive({
  username: '',
  email: '',
  phone: ''
});

const isSubmitted = ref(false);

function validateForm() {
  let isValid = true;
  
  // Kullanıcı adı doğrulama
  if (!formData.username.trim()) {
    errors.username = 'Kullanıcı adı zorunludur';
    isValid = false;
  } else if (formData.username.length < 3) {
    errors.username = 'Kullanıcı adı en az 3 karakter olmalıdır';
    isValid = false;
  }
  
  // E-posta doğrulama
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!formData.email) {
    errors.email = 'E-posta adresi zorunludur';
    isValid = false;
  } else if (!emailRegex.test(formData.email)) {
    errors.email = 'Geçerli bir e-posta adresi giriniz';
    isValid = false;
  }
  
  // Telefon doğrulama (opsiyonel)
  if (formData.phone && !/^\d{10}$/.test(formData.phone)) {
    errors.phone = 'Geçerli bir telefon numarası giriniz (10 hane)';
    isValid = false;
  }
  
  return isValid;
}

function clearError(field) {
  if (errors[field]) {
    errors[field] = '';
  }
}

function handleSubmit() {
  if (validateForm()) {
    console.log('Form verileri:', formData);
    // API çağrısı yapılabilir
    // await api.saveUser(formData);
    
    // Başarılı gösterimi
    isSubmitted.value = true;
    
    // Formu sıfırla
    Object.keys(formData).forEach(key => {
      formData[key] = '';
    });
    
    // 3 saniye sonra başarı mesajını gizle
    setTimeout(() => {
      isSubmitted.value = false;
    }, 3000);
  } else {
    console.log('Form doğrulama başarısız');
  }
}
</script>

<style scoped>
.form-container {
  max-width: 500px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #2c3e50;
  text-align: center;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.submit-btn {
  padding: 0.75rem 1.5rem;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.submit-btn:hover {
  background-color: #3aa876;
}

.submit-btn:active {
  transform: translateY(1px);
}

.success-message {
  margin-top: 1.5rem;
  padding: 0.75rem;
  background-color: #d4edda;
  color: #155724;
  border-radius: 4px;
  text-align: center;
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Özel stil örneği - v-bind ile iletilen class ile birleşecek */
.email-input {
  border-left: 3px solid #42b983;
  padding-left: 0.75rem;
}
</style>

### Slots

- **Named Slots:** Birden fazla içerik yerleştirme noktası sağlamak için isimlendirilmiş slotlar kullanılır. `<slot name="header"></slot>`
- **Scoped Slots:** Slot içeriğinin, alt bileşenden veri almasını sağlar. Bu, slot içeriğini daha yetenekli hale getirir.

#### 1. Temel Slot Kullanımı

```vue
<!-- Card.vue -->
<template>
  <div class="card">
    <div v-if="$slots.header" class="card-header">
      <slot name="header"></slot>
    </div>
    
    <div class="card-body">
      <slot></slot> <!-- Varsayılan slot -->
    </div>
    
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
// Bileşen mantığı buraya
</script>

<style scoped>
.card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
  background-color: #fff;
}

.card-header {
  padding: 1rem 1.25rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
  font-weight: 600;
  font-size: 1.1rem;
}

.card-body {
  padding: 1.25rem;
}

.card-footer {
  padding: 0.75rem 1.25rem;
  background-color: #f8f9fa;
  border-top: 1px solid #e0e0e0;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}
</style>

<!-- Kullanımı -->
<template>
  <div class="page">
    <h1>Blog Yazıları</h1>
    
    <card>
      <template #header>
        <h2>Vue.js ile Modern Web Geliştirme</h2>
        <div class="meta">Yayın Tarihi: 15 Haziran 2023</div>
      </template>
      
      <p>Vue.js, kullanıcı arayüzleri oluşturmak için popüler bir JavaScript framework'üdür. Bu yazıda Vue 3'ün en son özelliklerini inceleyeceğiz.</p>
      <p>Composition API, Teleport, Fragments ve diğer yenilikler hakkında bilgi edineceksiniz.</p>
      
      <template #footer>
        <button class="btn btn-outline">Devamını Oku</button>
        <button class="btn btn-primary">Beğen</button>
      </template>
    </card>
    
    <card>
      <template #header>
        <h2>Vue 3 ve TypeScript</h2>
        <div class="meta">Yayın Tarihi: 10 Haziran 2023</div>
      </template>
      
      <p>TypeScript, büyük ölçekli uygulamalar geliştirirken kod kalitesini artırmak için harika bir araçtır. Vue 3, TypeScript desteği ile birlikte gelir.</p>
      
      <template #footer>
        <button class="btn btn-outline">Devamını Oku</button>
        <button class="btn btn-primary">Beğen</button>
      </template>
    </card>
  </div>
</template>

<script setup>
import Card from './Card.vue';
</script>

<style scoped>
.page {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: #2c3e50;
  margin-bottom: 2rem;
  text-align: center;
}

h2 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.meta {
  font-size: 0.875rem;
  color: #666;
  font-style: italic;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.btn-outline {
  border: 1px solid #42b983;
  background: transparent;
  color: #42b983;
}

.btn-outline:hover {
  background-color: rgba(66, 185, 131, 0.1);
}

.btn-primary {
  background-color: #42b983;
  color: white;
  border: 1px solid #3aa876;
}

.btn-primary:hover {
  background-color: #3aa876;
}
</style>

#### 2. Scoped Slots ile Veri İletimi

```vue
<!-- DataTable.vue -->
<template>
  <div class="data-table">
    <table>
      <thead>
        <tr>
          <th v-for="column in columns" :key="column.key">
            {{ column.label }}
          </th>
          <th v-if="$slots.actions">İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="item.id || index">
          <td v-for="column in columns" :key="column.key">
            <!-- Varsayılan slot, veriyi doğrudan gösterir -->
            <slot 
              :name="`cell-${column.key}`" 
              :value="item[column.key]"
              :item="item"
              :column="column"
            >
              {{ item[column.key] }}
            </slot>
          </td>
          
          <!-- İşlemler sütunu için slot -->
          <td v-if="$slots.actions" class="actions">
            <slot name="actions" :item="item" :index="index"></slot>
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Boş durum için slot -->
    <div v-if="items.length === 0" class="empty-state">
      <slot name="empty">
        <p>Gösterilecek veri bulunamadı.</p>
      </slot>
    </div>
    
    <!-- Sayfalama için slot -->
    <div v-if="$slots.pagination" class="pagination">
      <slot name="pagination"></slot>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  items: {
    type: Array,
    required: true,
    default: () => []
  },
  columns: {
    type: Array,
    required: true,
    validator: (columns) => {
      return columns.every(col => col.key && col.label);
    }
  }
});
</script>

<style scoped>
.data-table {
  width: 100%;
  overflow-x: auto;
  margin: 1rem 0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  background-color: #fff;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 0.75rem 1rem;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

tr:hover {
  background-color: #f5f5f5;
}

.empty-state {
  padding: 2rem;
  text-align: center;
  color: #666;
  font-style: italic;
}

.actions {
  white-space: nowrap;
  text-align: right;
}

.pagination {
  padding: 1rem;
  display: flex;
  justify-content: center;
  border-top: 1px solid #e0e0e0;
}
</style>

<!-- Kullanımı -->
<template>
  <div class="user-management">
    <h1>Kullanıcı Yönetimi</h1>
    
    <data-table
      :items="users"
      :columns="columns"
    >
      <!-- Özel hücre formatlama -->
      <template #cell-avatar="{ value }">
        <img 
          :src="value || 'https://via.placeholder.com/40'" 
          alt="Profil Resmi" 
          class="avatar"
        />
      </template>
      
      <template #cell-status="{ value }">
        <span :class="['status', value.toLowerCase()]">
          {{ statusLabels[value] || value }}
        </span>
      </template>
      
      <template #cell-createdAt="{ value }">
        {{ formatDate(value) }}
      </template>
      
      <!-- İşlemler sütunu -->
      <template #actions="{ item }">
        <button 
          class="btn-icon" 
          @click="editUser(item)"
          title="Düzenle"
        >
          <span class="material-icons">edit</span>
        </button>
        <button 
          class="btn-icon danger" 
          @click="confirmDelete(item)"
          title="Sil"
        >
          <span class="material-icons">delete</span>
        </button>
      </template>
      
      <!-- Boş durum için özel içerik -->
      <template #empty>
        <div class="empty-content">
          <div class="empty-icon">📊</div>
          <h3>Kullanıcı bulunamadı</h3>
          <p>Henüz hiç kullanıcı eklenmemiş. Yeni bir kullanıcı eklemek için aşağıdaki butona tıklayın.</p>
          <button class="btn btn-primary" @click="addNewUser">
            Yeni Kullanıcı Ekle
          </button>
        </div>
      </template>
      
      <!-- Sayfalama -->
      <template #pagination>
        <div class="pagination-controls">
          <button 
            class="btn btn-outline" 
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Önceki
          </button>
          <span class="page-info">
            Sayfa {{ currentPage }} / {{ totalPages }}
          </span>
          <button 
            class="btn btn-outline"
            :disabled="currentPage >= totalPages"
            @click="currentPage++"
          >
            Sonraki
          </button>
        </div>
      </template>
    </data-table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import DataTable from './DataTable.vue';

// Örnek veri
const users = ref([
  {
    id: 1,
    name: 'Ahmet Yılmaz',
    email: 'ahmet@example.com',
    role: 'admin',
    status: 'active',
    createdAt: '2023-01-15T10:30:00Z',
    avatar: 'https://i.pravatar.cc/150?img=1'
  },
  {
    id: 2,
    name: 'Ayşe Kaya',
    email: 'ayse@example.com',
    role: 'editor',
    status: 'pending',
    createdAt: '2023-02-20T14:45:00Z',
    avatar: 'https://i.pravatar.cc/150?img=2'
  },
  {
    id: 3,
    name: 'Mehmet Demir',
    email: 'mehmet@example.com',
    role: 'user',
    status: 'inactive',
    createdAt: '2023-03-10T09:15:00Z',
    avatar: 'https://i.pravatar.cc/150?img=3'
  },
  {
    id: 4,
    name: 'Zeynep Şahin',
    email: 'zeynep@example.com',
    role: 'user',
    status: 'active',
    createdAt: '2023-04-05T16:20:00Z',
    avatar: 'https://i.pravatar.cc/150?img=4'
  }
]);

const columns = [
  { key: 'avatar', label: '' },
  { key: 'name', label: 'Ad Soyad' },
  { key: 'email', label: 'E-posta' },
  { key: 'role', label: 'Rol' },
  { key: 'status', label: 'Durum' },
  { key: 'createdAt', label: 'Kayıt Tarihi' }
];

const statusLabels = {
  active: 'Aktif',
  inactive: 'Pasif',
  pending: 'Onay Bekliyor',
  suspended: 'Askıya Alındı'
};

const currentPage = ref(1);
const itemsPerPage = 10;
const totalPages = computed(() => Math.ceil(users.value.length / itemsPerPage));

function formatDate(dateString) {
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateString).toLocaleDateString('tr-TR', options);
}

function editUser(user) {
  console.log('Düzenle:', user);
  // Düzenleme mantığı buraya gelecek
}

function confirmDelete(user) {
  if (confirm(`${user.name} adlı kullanıcıyı silmek istediğinize emin misiniz?`)) {
    deleteUser(user.id);
  }
}

function deleteUser(userId) {
  users.value = users.value.filter(user => user.id !== userId);
  console.log('Kullanıcı silindi:', userId);
}

function addNewUser() {
  console.log('Yeni kullanıcı ekleme formu açılıyor...');
  // Yeni kullanıcı ekleme mantığı buraya gelecek
}
</script>

<style scoped>
.user-management {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: #2c3e50;
  margin-bottom: 2rem;
  text-align: center;
}

.btn-icon {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-icon:hover {
  background-color: rgba(0, 0, 0, 0.05);
  color: #2c3e50;
}

.btn-icon.danger {
  color: #f44336;
}

.btn-icon.danger:hover {
  background-color: rgba(244, 67, 54, 0.1);
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}

.status {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status.active {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.status.inactive {
  background-color: #ffebee;
  color: #c62828;
}

.status.pending {
  background-color: #fff8e1;
  color: #f57f17;
}

.empty-content {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.7;
}

.empty-content h3 {
  margin: 0.5rem 0;
  color: #2c3e50;
}

.empty-content p {
  color: #666;
  margin-bottom: 1.5rem;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.page-info {
  font-size: 0.9rem;
  color: #666;
  min-width: 100px;
  text-align: center;
}

/* Material Icons için stil */
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
.material-icons {
  font-size: 1.25rem;
  vertical-align: middle;
}
</style>

### Provide / Inject

#### 1. Temel Kullanım

```vue
<!-- App.vue (Üst Bileşen) -->
<template>
  <div class="app">
    <h1>Kullanıcı Paneli</h1>
    <user-dashboard />
  </div>
</template>

<script setup>
import { ref, provide } from 'vue';
import UserDashboard from './components/UserDashboard.vue';

// Kullanıcı verisi
const currentUser = ref({
  id: 1,
  name: 'Ahmet Yılmaz',
  email: 'ahmet@example.com',
  role: 'admin',
  preferences: {
    theme: 'dark',
    language: 'tr',
    notifications: true
  },
  permissions: ['dashboard:view', 'users:manage', 'settings:edit']
});

// Kullanıcı giriş durumu
const isAuthenticated = ref(true);

// Kullanıcı güncelleme fonksiyonu
function updateUser(updates) {
  currentUser.value = { ...currentUser.value, ...updates };
  console.log('Kullanıcı güncellendi:', currentUser.value);
}

// Tema değiştirme fonksiyonu
function toggleTheme() {
  const newTheme = currentUser.value.preferences.theme === 'dark' ? 'light' : 'dark';
  updateUser({
    preferences: {
      ...currentUser.value.preferences,
      theme: newTheme
    }
  });
  document.documentElement.setAttribute('data-theme', newTheme);
}

// Uygulama başlatıldığında temayı ayarla
document.documentElement.setAttribute('data-theme', currentUser.value.preferences.theme);

// Alt bileşenlere sağlanacak değerler
provide('currentUser', {
  user: currentUser,
  isAuthenticated,
  updateUser,
  toggleTheme
});

// İzin kontrolü için bir fonksiyon
function hasPermission(permission) {
  return currentUser.value.permissions.includes(permission);
}

provide('hasPermission', hasPermission);
</script>

<style>
:root {
  --bg-color: #ffffff;
  --text-color: #2c3e50;
  --primary-color: #42b983;
  --border-color: #e0e0e0;
}

[data-theme="dark"] {
  --bg-color: #1a1a1a;
  --text-color: #f5f5f5;
  --primary-color: #4fc08d;
  --border-color: #333;
}

body {
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
  background-color: var(--bg-color);
  color: var(--text-color);
  transition: background-color 0.3s, color 0.3s;
}

.app {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: var(--primary-color);
  text-align: center;
  margin-bottom: 2rem;
}

.dashboard {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
  margin-top: 2rem;
}

.sidebar {
  background-color: var(--bg-color);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1.5rem;
  height: fit-content;
}

.main-content {
  background-color: var(--bg-color);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1.5rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-primary {
  background-color: var(--primary-color);
  color: white;
}

.btn-primary:hover {
  opacity: 0.9;
}

.card {
  background-color: var(--bg-color);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.card-title {
  margin-top: 0;
  color: var(--primary-color);
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
}
</style>
```

```vue
<!-- components/UserDashboard.vue -->
<template>
  <div class="dashboard">
    <user-sidebar />
    <div class="main-content">
      <user-profile />
      <user-settings v-if="hasPermission('settings:edit')" />
      <user-admin-panel v-if="hasPermission('admin:access')" />
    </div>
  </div>
</template>

<script setup>
import { inject } from 'vue';
import UserSidebar from './UserSidebar.vue';
import UserProfile from './UserProfile.vue';
import UserSettings from './UserSettings.vue';
import UserAdminPanel from './UserAdminPanel.vue';

const hasPermission = inject('hasPermission');
</script>
```

```vue
<!-- components/UserSidebar.vue -->
<template>
  <aside class="sidebar">
    <div class="user-info">
      <h3>{{ user.name }}</h3>
      <p>{{ user.email }}</p>
      <p>Rol: {{ user.role }}</p>
    </div>
    
    <nav class="nav-menu">
      <ul>
        <li><a href="#" class="nav-link">Profilim</a></li>
        <li><a href="#" class="nav-link">Ayarlar</a></li>
        <li v-if="hasPermission('users:manage')">
          <a href="#" class="nav-link">Kullanıcı Yönetimi</a>
        </li>
        <li>
          <button class="btn btn-primary" @click="toggleTheme">
            {{ user.preferences.theme === 'dark' ? 'Açık Tema' : 'Koyu Tema' }}
          </button>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script setup>
import { inject } from 'vue';

const { user, toggleTheme, hasPermission } = inject('currentUser');
</script>

<style scoped>
.user-info {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.user-info h3 {
  margin: 0 0 0.5rem 0;
  color: var(--primary-color);
}

.user-info p {
  margin: 0.25rem 0;
  font-size: 0.9rem;
  color: var(--text-color);
  opacity: 0.8;
}

.nav-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-menu li {
  margin-bottom: 0.75rem;
}

.nav-link {
  display: block;
  padding: 0.5rem 0;
  color: var(--text-color);
  text-decoration: none;
  transition: color 0.2s;
}

.nav-link:hover {
  color: var(--primary-color);
}

.btn {
  width: 100%;
  margin-top: 1rem;
}
</style>
```

```vue
<!-- components/UserProfile.vue -->
<template>
  <div class="card">
    <h2 class="card-title">Profil Bilgileri</h2>
    <div class="profile-info">
      <div class="info-row">
        <span class="label">Ad Soyad:</span>
        <span class="value">{{ user.name }}</span>
      </div>
      <div class="info-row">
        <span class="label">E-posta:</span>
        <span class="value">{{ user.email }}</span>
      </div>
      <div class="info-row">
        <span class="label">Rol:</span>
        <span class="value">{{ user.role }}</span>
      </div>
      <div class="info-row">
        <span class="label">Tema Tercihi:</span>
        <span class="value">{{ user.preferences.theme === 'dark' ? 'Koyu Tema' : 'Açık Tema' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject } from 'vue';

const { user } = inject('currentUser');
</script>

<style scoped>
.profile-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.info-row {
  display: flex;
  flex-direction: column;
  margin-bottom: 0.75rem;
}

.label {
  font-weight: 500;
  color: var(--text-color);
  opacity: 0.8;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.value {
  color: var(--text-color);
  font-size: 1rem;
}

@media (max-width: 768px) {
  .profile-info {
    grid-template-columns: 1fr;
  }
}
</style>
```

#### 2. Reactive Provide/Inject

Bu örnek, tema yönetimi için özelleştirilmiş bir sağlayıcı bileşeni gösterir. Bu yapı, uygulamanın herhangi bir yerinden tema değişikliklerine tepki verebilen bileşenler oluşturmayı sağlar.

```vue
<!-- ThemeProvider.vue -->
<template>
  <div class="theme-provider">
    <slot></slot>
    
    <!-- Tema değişim butonu (opsiyonel) -->
    <button 
      class="theme-toggle"
      @click="toggleTheme"
      :title="`${theme === 'dark' ? 'Açık' : 'Koyu'} temaya geç`"
    >
      {{ theme === 'dark' ? '☀️' : '🌙' }}
    </button>
  </div>
</template>

<script setup>
import { ref, provide, watch, onMounted } from 'vue';

// Varsayılan tema
const DEFAULT_THEME = 'light';
const THEME_KEY = 'app-theme';

// Tema durumu
const theme = ref(DEFAULT_THEME);

// Tema değişimini dinle
watch(theme, (newTheme) => {
  // HTML etiketine data-theme özelliği ekle
  document.documentElement.setAttribute('data-theme', newTheme);
  
  // LocalStorage'a kaydet
  localStorage.setItem(THEME_KEY, newTheme);
});

// Tema değiştirme fonksiyonu
function toggleTheme() {
  theme.value = theme.value === 'dark' ? 'light' : 'dark';
}

// Sistem temasını algıla
function detectSystemTheme() {
  return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches 
    ? 'dark' 
    : 'light';
}

// Sistem teması değiştiğinde güncelle
function setupSystemThemeListener() {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
  
  const handleChange = (e) => {
    // Sadece kullanıcı özel bir tema seçmediyse sistemi takip et
    const savedTheme = localStorage.getItem(THEME_KEY);
    if (!savedTheme) {
      theme.value = e.matches ? 'dark' : 'light';
    }
  };
  
  mediaQuery.addEventListener('change', handleChange);
  
  // Temizleme fonksiyonu
  return () => {
    mediaQuery.removeEventListener('change', handleChange);
  };
}

// Bileşen oluşturulduğunda
onMounted(() => {
  // Kayıtlı temayı yükle
  const savedTheme = localStorage.getItem(THEME_KEY);
  
  if (savedTheme) {
    theme.value = savedTheme;
  } else {
    // Sistem temasını kullan
    theme.value = detectSystemTheme();
  }
  
  // Sistem teması değişikliklerini dinle
  const cleanup = setupSystemThemeListener();
  
  // Temizleme fonksiyonunu döndür
  return cleanup;
});

// Alt bileşenlere tema bilgisini ve değiştirme fonksiyonunu sağla
provide('theme', {
  theme,
  toggleTheme
});
</script>

<style scoped>
.theme-provider {
  position: relative;
  min-height: 100vh;
}

.theme-toggle {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  border: none;
  background-color: var(--primary-color);
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  transition: transform 0.2s, background-color 0.3s;
}

.theme-toggle:hover {
  transform: scale(1.1);
}

.theme-toggle:active {
  transform: scale(0.95);
}
</style>
```

```vue
<!-- components/ThemeConsumer.vue -->
<template>
  <div class="theme-consumer">
    <h2>Mevcut Tema: {{ themeName }}</h2>
    <p>
      Bu bileşen, üst bileşenden sağlanan tema bilgisini kullanıyor.
      Aşağıdaki butonla temayı değiştirebilirsiniz.
    </p>
    
    <button @click="toggleTheme" class="theme-button">
      {{ theme === 'dark' ? '☀️ Açık Tema' : '🌙 Koyu Tema' }}
    </button>
    
    <div class="theme-preview">
      <div class="preview-box primary">
        <h3>Primary</h3>
        <p>Bu birincil renk örneğidir.</p>
      </div>
      <div class="preview-box secondary">
        <h3>Secondary</h3>
        <p>Bu ikincil renk örneğidir.</p>
      </div>
      <div class="preview-box accent">
        <h3>Accent</h3>
        <p>Bu vurgu rengi örneğidir.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject } from 'vue';

// Üst bileşenden tema bilgisini al
const { theme, toggleTheme } = inject('theme');

// Tema adını göstermek için hesaplanmış özellik
const themeName = computed(() => {
  return theme.value === 'dark' ? 'Koyu Tema' : 'Açık Tema';
});
</script>

<style scoped>
.theme-consumer {
  padding: 2rem;
  max-width: 800px;
  margin: 0 auto;
  background-color: var(--bg-color);
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s, color 0.3s;
}

h2 {
  color: var(--primary-color);
  margin-top: 0;
  border-bottom: 2px solid var(--border-color);
  padding-bottom: 0.5rem;
  margin-bottom: 1.5rem;
}

p {
  color: var(--text-color);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.theme-button {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0 auto 2rem;
  transition: transform 0.2s, opacity 0.2s;
}

.theme-button:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

.theme-button:active {
  transform: translateY(0);
}

.theme-preview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-top: 2rem;
}

.preview-box {
  padding: 1.5rem;
  border-radius: 6px;
  color: white;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.preview-box:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.preview-box h3 {
  margin-top: 0;
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.preview-box p {
  margin: 0;
  font-size: 0.9rem;
  opacity: 0.9;
  color: white;
}

.primary {
  background-color: var(--primary-color);
}

.secondary {
  background-color: var(--secondary-color, #6c757d);
}

.accent {
  background-color: var(--accent-color, #ffc107);
  color: #333;
}

.accent p {
  color: #333;
}
</style>
```

#### 3. Uygulama Başlatıcı ile Provide/Inject Kullanımı

Bu örnek, uygulama genelinde kullanılacak servislerin ve yapılandırmaların nasıl merkezi bir yerden sağlanacağını gösterir. Bu yapı, büyük ölçekli uygulamalarda kod organizasyonu ve yeniden kullanılabilirlik için idealdir.

```javascript
// src/app.js
export function createApp() {
  // 1. Uygulama yapılandırması
  const config = {
    appName: 'Vue 3 Uygulamam',
    version: '1.0.0',
    api: {
      baseUrl: import.meta.env.VITE_API_URL || '/api',
      endpoints: {
        users: '/users',
        posts: '/posts',
        // Diğer endpoint'ler...
      },
      // API çağrıları için varsayılan başlıklar
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    },
    features: {
      darkMode: true,
      notifications: true,
      analytics: false,
      // Diğer özellik bayrakları...
    },
    // Diğer yapılandırmalar...
  };

  // 2. HTTP İstemcisi
  const http = {
    async get(url, options = {}) {
      const response = await fetch(`${config.api.baseUrl}${url}`, {
        ...options,
        method: 'GET',
        headers: {
          ...config.api.headers,
          ...options.headers
        }
      });
      return this._handleResponse(response);
    },

    async post(url, data, options = {}) {
      const response = await fetch(`${config.api.baseUrl}${url}`, {
        ...options,
        method: 'POST',
        headers: {
          ...config.api.headers,
          ...options.headers
        },
        body: JSON.stringify(data)
      });
      return this._handleResponse(response);
    },

    // Diğer HTTP metodları (put, delete, patch vb.)...

    async _handleResponse(response) {
      if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(error.message || 'Bir hata oluştu');
      }
      return response.json();
    }
  };

  // 3. Kimlik Doğrulama Servisi
  const auth = {
    // Kullanıcı durumu
    user: null,
    isAuthenticated: false,
    token: localStorage.getItem('auth_token'),
    
    // Giriş işlemi
    async login(credentials) {
      try {
        const data = await http.post('/auth/login', credentials);
        this._setAuthData(data);
        return { success: true };
      } catch (error) {
        console.error('Giriş başarısız:', error);
        return { success: false, error: error.message };
      }
    },
    
    // Kayıt işlemi
    async register(userData) {
      try {
        const data = await http.post('/auth/register', userData);
        this._setAuthData(data);
        return { success: true };
      } catch (error) {
        console.error('Kayıt başarısız:', error);
        return { success: false, error: error.message };
      }
    },
    
    // Çıkış işlemi
    logout() {
      localStorage.removeItem('auth_token');
      this.user = null;
      this.isAuthenticated = false;
      this.token = null;
    },
    
    // Kullanıcı bilgilerini yükle
    async loadUser() {
      if (!this.token) return null;
      
      try {
        const user = await http.get('/auth/me', {
          headers: { 'Authorization': `Bearer ${this.token}` }
        });
        this.user = user;
        this.isAuthenticated = true;
        return user;
      } catch (error) {
        this.logout();
        return null;
      }
    },
    
    // Yetki kontrolü
    hasRole(role) {
      return this.user?.roles?.includes(role) || false;
    },
    
    // Özel metodlar
    _setAuthData(data) {
      this.token = data.token;
      this.user = data.user;
      this.isAuthenticated = true;
      localStorage.setItem('auth_token', this.token);
    }
  };

  // 4. Bildirim Servisi
  const notify = {
    methods: {
      success(message) {
        console.log(`✅ ${message}`);
        // Gerçek bir bildirim kütüphanesi entegrasyonu yapılabilir
        alert(`Başarılı: ${message}`);
      },
      
      error(message) {
        console.error(`❌ ${message}`);
        alert(`Hata: ${message}`);
      },
      
      info(message) {
        console.info(`ℹ️ ${message}`);
        alert(`Bilgi: ${message}`);
      },
      
      // Diğer bildirim türleri...
    },
    
    // Vue eklentisi olarak kurulum
    install(app) {
      app.config.globalProperties.$notify = this.methods;
    }
  };

  // 5. Özel Direktifler
  const directives = {
    // Otomatik odaklanma direktifi
    focus: {
      mounted(el) {
        el.focus();
      },
      updated(el) {
        el.focus();
      }
    },
    
    // Dışarı tıklama direktifi
    clickOutside: {
      beforeMount(el, binding) {
        el.clickOutsideEvent = (event) => {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value(event);
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      }
    },
    
    // Daha fazla özel direktif...
  };

  // 6. Uygulama Başlatıcı
  function bootstrapApp(app) {
    // Yapılandırmayı sağla
    app.provide('config', config);
    app.provide('http', http);
    app.provide('auth', auth);
    
    // Direktifleri kaydet
    Object.entries(directives).forEach(([name, directive]) => {
      app.directive(name, directive);
    });
    
    // Eklentileri yükle
    app.use(notify);
    
    // Uygulama başlatma işlemleri
    const initialize = async () => {
      // Kullanıcı oturumunu kontrol et
      if (auth.token) {
        await auth.loadUser();
      }
      
      // Uygulama genelinde kullanılacak global özellikler
      app.config.globalProperties.$appName = config.appName;
      app.config.globalProperties.$formatDate = (date) => {
        return new Date(date).toLocaleDateString('tr-TR');
      };
    };
    
    // Uygulamayı döndür
    return {
      app,
      initialize,
      config,
      http,
      auth,
      notify: notify.methods
    };
  }

  return {
    config,
    http,
    auth,
    notify: notify.methods,
    directives,
    bootstrapApp
  };
}
```

**Kullanım Örneği:**

```javascript
// main.js
import { createApp } from 'vue';
import App from './App.vue';
import { createPinia } from 'pinia';
import { createApp as createVueApp } from './app';

// Uygulama başlatıcıyı oluştur
const { bootstrapApp } = createVueApp();

// Vue uygulamasını oluştur
const app = createApp(App);

// Pinia'yı ekle
const pinia = createPinia();
app.use(pinia);

// Uygulamayı özelleştir ve başlat
const { initialize } = bootstrapApp(app);

// Uygulama başlatma işlemlerini gerçekleştir
initialize().then(() => {
  app.mount('#app');
});
```

**Bileşen İçinde Kullanım:**

```vue
<!-- UserProfile.vue -->
<template>
  <div class="user-profile">
    <div v-if="auth.isAuthenticated">
      <h2>Profil Bilgileri</h2>
      <p>Hoş geldiniz, {{ user.name }}!</p>
      <p>Üyelik Tarihi: {{ $formatDate(user.createdAt) }}</p>
      
      <button @click="updateProfile" :disabled="isSaving">
        {{ isSaving ? 'Kaydediliyor...' : 'Profili Güncelle' }}
      </button>
      
      <button @click="auth.logout" class="logout-btn">
        Çıkış Yap
      </button>
    </div>
    
    <div v-else>
      <p>Lütfen giriş yapın</p>
      <router-link to="/login">Giriş Yap</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

// Servisleri inject ile al
const http = inject('http');
const auth = inject('auth');
const notify = inject('notify');

const router = useRouter();
const isSaving = ref(false);

// Kullanıcı bilgilerini hesaplanmış özellik olarak al
const user = computed(() => auth.user);

// Profil güncelleme işlemi
async function updateProfile() {
  try {
    isSaving.value = true;
    await http.put(`/users/${user.value.id}`, user.value);
    notify.success('Profil başarıyla güncellendi');
  } catch (error) {
    notify.error('Profil güncellenirken bir hata oluştu');
    console.error('Profil güncelleme hatası:', error);
  } finally {
    isSaving.value = false;
  }
}

// Bileşen yüklendiğinde kullanıcı bilgilerini yükle
onMounted(async () => {
  if (!auth.isAuthenticated && auth.token) {
    await auth.loadUser();
  }
});
</script>

<style scoped>
.user-profile {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background-color: var(--bg-color);
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
  color: var(--primary-color);
  margin-top: 0;
}

button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background-color: var(--primary-color);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: opacity 0.2s;
}

button:hover:not(:disabled) {
  opacity: 0.9;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.logout-btn {
  margin-left: 1rem;
  background-color: #dc3545;
}
</style>
```

**Avantajlar:**

1. **Merkezi Yapılandırma**: Tüm uygulama ayarları tek bir yerden yönetilir.
2. **Yeniden Kullanılabilirlik**: Servisler ve yapılandırmalar tüm uygulamada kullanılabilir.
3. **Test Edilebilirlik**: Her bir servis bağımsız olarak test edilebilir.
4. **Ölçeklenebilirlik**: Yeni özellikler kolayca eklenebilir.
5. **Tutarlılık**: Tüm uygulama genelinde tutarlı bir API kullanımı sağlar.

Bu yapı, özellikle büyük ölçekli uygulamalarda kod organizasyonu ve bakımı kolaylaştırır. Ayrıca, uygulamanın farklı bölümlerinde aynı servislerin ve yapılandırmaların kullanılmasını sağlayarak tutarlılığı artırır.

### Async Components (Asenkron Bileşenler)

Asenkron bileşenler, uygulamanın başlangıç yükleme süresini (initial load time) azaltmak için kullanılan ve sadece ihtiyaç duyulduğunda yüklenen (lazy loading) bileşenlerdir. Bu özellik özellikle büyük ölçekli uygulamalarda performansı önemli ölçüde artırabilir.

#### 1. Temel Kullanım

```javascript
import { defineAsyncComponent } from 'vue';

// Basit kullanım
const AsyncComponent = defineAsyncComponent(() =>
  import('./components/HeavyComponent.vue')
);

export default {
  components: {
    'heavy-component': AsyncComponent
  }
}
```

#### 2. Yükleme ve Hata Durumları

```javascript
import { defineAsyncComponent } from 'vue';
import LoadingSpinner from './components/LoadingSpinner.vue';
import ErrorComponent from './components/ErrorComponent.vue';

const AsyncComponent = defineAsyncComponent({
  // Yüklenecek bileşen
  loader: () => import('./components/HeavyComponent.vue'),
  
  // Yüklenirken gösterilecek bileşen
  loadingComponent: LoadingSpinner,
  
  // Hata durumunda gösterilecek bileşen
  errorComponent: ErrorComponent,
  
  // Yükleme gecikmesi (ms)
  delay: 200,
  
  // Zaman aşımı süresi (ms)
  timeout: 3000,
  
  // Hata işleme fonksiyonu
  onError(error, retry, fail, attempts) {
    if (error.message.match(/fetch/) && attempts <= 3) {
      // 3 kez tekrar dene
      retry();
    } else {
      fail();
    }
  }
});
```

#### 3. Route Bazlı Kod Bölme (Route-Based Code Splitting)

```javascript
// router/index.js
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import(/* webpackChunkName: "home" */ '../views/Home.vue')
  },
  {
    path: '/about',
    name: 'About',
    // Route seviyesinde kod bölme
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    // Daha büyük bir bileşen için özel yükleme stratejisi
    component: () => ({
      component: import(/* webpackChunkName: "dashboard" */ '../views/Dashboard.vue'),
      // Yükleme sırasında gösterilecek bileşen
      loading: {
        render(h) {
          return h('div', { class: 'loading-screen' }, 'Yükleniyor...');
        }
      },
      // Hata durumunda gösterilecek bileşen
      error: {
        render(h) {
          return h('div', { class: 'error-screen' }, 'Bir hata oluştu!');
        }
      },
      // Yükleme gecikmesi (ms)
      delay: 200,
      // Zaman aşımı süresi (ms)
      timeout: 5000
    })
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

export default router;
```

#### 4. Dinamik İçe Aktarma ile Bileşen Yükleme

```vue
<template>
  <div>
    <h1>Dinamik Bileşen Yükleme</h1>
    <button @click="loadComponent">Bileşeni Yükle</button>
    
    <div v-if="loading">Yükleniyor...</div>
    <div v-else-if="error">Hata: {{ error.message }}</div>
    <component v-else-if="dynamicComponent" :is="dynamicComponent" />
  </div>
</template>

<script setup>
import { ref } from 'vue';

const dynamicComponent = ref(null);
const loading = ref(false);
const error = ref(null);

async function loadComponent() {
  try {
    loading.value = true;
    error.value = null;
    
    // Dinamik olarak bileşen yükleme
    const module = await import('./components/DynamicComponent.vue');
    dynamicComponent.value = module.default || module;
  } catch (err) {
    console.error('Bileşen yüklenirken hata oluştu:', err);
    error.value = err;
  } finally {
    loading.value = false;
  }
}
</script>
```

#### 5. Suspense ile Asenkron Bileşenler

Vue 3'te Suspense bileşeni, asenkron bileşenlerin yüklenmesini daha iyi yönetmek için kullanılır:

```vue
<!-- App.vue -->
<template>
  <div id="app">
    <Suspense>
      <!-- Varsayılan içerik -->
      <template #default>
        <AsyncComponent />
      </template>
      
      <!-- Yüklenirken gösterilecek içerik -->
      <template #fallback>
        <div class="loading">Yükleniyor...</div>
      </template>
    </Suspense>
  </div>
</template>

<script setup>
import { defineAsyncComponent } from 'vue';

const AsyncComponent = defineAsyncComponent(() => 
  import('./components/HeavyComponent.vue')
);
</script>

<style>
.loading {
  padding: 20px;
  background: #f3f4f6;
  border-radius: 4px;
  text-align: center;
  font-size: 1.2em;
  color: #666;
}
</style>
```

#### 6. Prefetch ve Preload ile Performans İyileştirmeleri

```javascript
// webpackChunkMagicComments kullanarak prefetch ve preload
const HeavyComponent = defineAsyncComponent({
  loader: () => import(
    /* webpackPrefetch: true */
    /* webpackPreload: true */
    /* webpackChunkName: "heavy-component" */
    './components/HeavyComponent.vue'
  ),
  loadingComponent: LoadingSpinner,
  delay: 200
});
```

#### 7. Bileşen Önbelleğe Alma (Caching)

```javascript
import { defineAsyncComponent, h } from 'vue';

// Basit bir önbellek mekanizması
const componentCache = new Map();

function asyncComponentWithCache(loader, componentName) {
  if (componentCache.has(componentName)) {
    return componentCache.get(componentName);
  }
  
  const component = defineAsyncComponent({
    loader: async () => {
      try {
        const module = await loader();
        componentCache.set(componentName, module.default || module);
        return module;
      } catch (error) {
        console.error(`Bileşen yüklenirken hata oluştu (${componentName}):`, error);
        throw error;
      }
    },
    loadingComponent: {
      render: () => h('div', 'Yükleniyor...')
    },
    errorComponent: {
      render: () => h('div', 'Bileşen yüklenirken bir hata oluştu')
    }
  });
  
  return component;
}

// Kullanımı
const CachedComponent = asyncComponentWithCache(
  () => import('./components/HeavyComponent.vue'),
  'HeavyComponent'
);
```

#### 8. Bileşen Yükleme Durumunu İzleme

```javascript
// useAsyncComponent.js
import { ref, onMounted } from 'vue';

export function useAsyncComponent(loader) {
  const component = ref(null);
  const isLoading = ref(false);
  const error = ref(null);
  
  const loadComponent = async () => {
    try {
      isLoading.value = true;
      error.value = null;
      
      const module = await loader();
      component.value = module.default || module;
    } catch (err) {
      console.error('Bileşen yüklenirken hata oluştu:', err);
      error.value = err;
    } finally {
      isLoading.value = false;
    }
  };
  
  // Bileşen oluşturulduğunda otomatik yükle
  onMounted(() => {
    loadComponent();
  });
  
  return {
    component,
    isLoading,
    error,
    retry: loadComponent
  };
}

// Kullanımı
// Component içinde:
// const { component: AsyncComp, isLoading, error, retry } = useAsyncComponent(
//   () => import('./HeavyComponent.vue')
// );
```

#### 9. Performans İçin En İyi Uygulamalar

1. **Route Seviyesinde Bölme**: Uygulamanızı farklı rotalara göre bölün.
2. **Bileşen Seviyesinde Bölme**: Büyük bileşenleri daha küçük, yönetilebilir parçalara bölün.
3. **Görünür Alan (Viewport) Kontrolü**: Kullanıcının görüş alanında olmayan bileşenleri geciktirin.
4. **Önbellek Kullanımı**: Sık kullanılan bileşenleri önbelleğe alın.
5. **Prefetch/Preload Stratejileri**: Kullanıcının muhtemelen ihtiyaç duyacağı kaynakları önceden yükleyin.
6. **Yükleme ve Hata Durumları**: Kullanıcı deneyimini iyileştirmek için uygun yükleme göstergeleri ve hata mesajları sağlayın.
7. **Performans İzleme**: Web Vitals gibi araçlarla performansı izleyin ve iyileştirin.

Bu teknikler, Vue uygulamanızın performansını önemli ölçüde artırabilir ve kullanıcı deneyimini iyileştirebilir.
