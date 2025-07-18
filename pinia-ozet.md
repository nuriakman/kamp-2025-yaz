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
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)
app.mount('#app')
```

## Core Concepts (Temel Kavramlar)

### Defining a Store (Bir Store Tanımlama)

Bir "store", `defineStore()` fonksiyonu ile tanımlanır. Her store'un uygulama genelinde benzersiz bir ID'si olmalıdır.

```javascript
// stores/counter.js
import { defineStore } from 'pinia'

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
      this.count++
    },
  },
})
```

### State (Durum)

- `state`, store'un temel verisini tutan bir fonksiyondur ve bir nesne döndürmelidir.
- Bir bileşen içinden state'e doğrudan erişilebilir ve değiştirilebilir.

```vue
<script setup>
import { useCounterStore } from '@/stores/counter'

const counter = useCounterStore()

// State'e erişim
console.log(counter.count)

// State'i değiştirme
counter.count++
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
    console.log(`[${store.$id}]: ${mutation.type}`, mutation.payload)
  })

  // store'a yeni bir özellik ekle
  return { secret: 'the-secret-key' }
}
```

## Stores outside of components (Bileşen Dışında Store Kullanımı)

Bazen bir store'u bir Vue bileşeni dışında (örneğin bir yönlendirme (router) guard'ı içinde) kullanmak gerekebilir. Bunu yaparken dikkat edilmesi gereken, Pinia örneğinin henüz aktif olmayabileceğidir.

Çözüm, store'u kullanacağınız fonksiyonun *içinde* çağırmaktır.

```javascript
// router.js
import { useUserStore } from './stores/user'

router.beforeEach((to) => {
  // Fonksiyon içinde çağırarak store'un aktif olduğundan emin ol
  const userStore = useUserStore()

  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    return '/login'
  }
})
```
