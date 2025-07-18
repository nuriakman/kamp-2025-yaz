# Vue.js Temelleri: Kapsamlı Özet

Bu doküman, [Vue.js resmi dokümantasyonunun](https://vuejs.org/guide/introduction.html) "Introduction", "Essentials" ve "Components In-Depth" bölümlerinin tamamını kapsayan detaylı bir özettir.

## Bölüm 1: Giriş (Introduction)

### Vue.js Nedir?

Vue, kullanıcı arayüzleri (UI) oluşturmak için kullanılan, standart HTML, CSS ve JavaScript üzerine inşa edilmiş bir JavaScript framework'üdür. İki temel özelliği vardır:

- **Declarative Rendering:** JavaScript durumuna (state) dayalı olarak HTML çıktısını bildirimsel olarak tanımlar.
- **Reactivity:** JavaScript durumundaki değişiklikleri otomatik olarak izler ve DOM'u verimli bir şekilde günceller.

### Progressive Framework (Aşamalı Çerçeve)

Vue, projenizin ihtiyacına göre aşamalı olarak benimsenebilir. Basit bir HTML sayfasını geliştirmekten, tam teşekküllü Tek Sayfa Uygulamaları (SPA) oluşturmaya kadar geniş bir yelpazede kullanılabilir.

### API Stilleri: Options API vs. Composition API

- **Options API:** `data`, `methods` gibi seçeneklerle bileşen mantığını düzenler. `this` anahtar kelimesi merkezlidir ve yeni başlayanlar için daha sezgiseldir.
- **Composition API:** `import` edilen fonksiyonlarla (`ref`, `reactive`) mantık oluşturulur. `<script setup>` ile kullanılır, daha esnek ve büyük projelerde mantığı organize etmek için güçlüdür.

---

## Bölüm 2: Temeller (Essentials)

### Bir Vue Uygulaması Oluşturma

- Her Vue uygulaması `createApp` fonksiyonu ile başlar.
- Bir **kök bileşen (root component)** alır ve `.mount()` metodu ile bir DOM elementine bağlanır.

```javascript
import { createApp } from 'vue'
import App from './App.vue'

createApp(App).mount('#app')
```

### Template Syntax (Şablon Sözdizimi)

- **Text Interpolation:** `{{ }}` (Mustache syntax) ile veriyi metin olarak render eder.
- **Raw HTML:** `v-html` direktifi ile gerçek HTML render eder (XSS riskine dikkat!).
- **Attribute Bindings:** `v-bind:` veya kısaca `:` ile HTML elementlerinin özelliklerini (attribute) dinamik olarak bağlar. Örn: `<img :src="imageUrl">`
- **JavaScript Expressions:** Bağlamalar (bindings) içinde tek satırlık JavaScript ifadeleri kullanılabilir.
- **Direktifler:** `v-` önekine sahip özel attribute'lardır (`v-if`, `v-for`, `v-on`, `v-bind`, `v-model`).

### Reactivity Fundamentals (Reaktivitenin Temelleri)

- **`ref()`:** Tek bir değeri (primitive type) reaktif hale getirmek için kullanılır. Değerine `.value` ile erişilir.
- **`reactive()`:** Sadece nesneleri, dizileri ve diğer koleksiyon türlerini reaktif hale getirir.

### Computed Properties (Hesaplanmış Özellikler)

- Şablon içinde karmaşık mantık yerine `computed` kullanılır.
- Bağımlı olduğu reaktif veriler değiştiğinde yeniden hesaplanır ve sonuç önbelleğe (cache) alınır. Bu sayede performans artışı sağlar.

### Class ve Style Bindings

- `:class` ile bir elemente dinamik olarak CSS class'ları eklenir. (Örn: `:class="{ active: isActive }"`)
- `:style` ile bir elemente dinamik olarak inline stiller verilir. (Örn: `:style="{ color: activeColor, fontSize: size + 'px' }"`)

### Conditional Rendering (Koşullu Render)

- **`v-if`, `v-else`, `v-else-if`:** Bir bloğu koşula bağlı olarak DOM'a ekler veya DOM'dan kaldırır.
- **`v-show`:** Elementi her zaman DOM'da tutar, sadece CSS `display` özelliğini değiştirerek gizler/gösterir. Sık sık değiştirilen elemanlar için daha performanslıdır.

### List Rendering (Liste Render)

- **`v-for`:** Bir dizi veya nesne üzerinden iterasyon yaparak elementleri render eder. (Örn: `<li v-for="item in items">`)
- **`key`:** `v-for` ile birlikte kullanılması zorunludur. Vue'nun her bir düğümü izlemesine ve mevcut elemanları yeniden kullanıp sıralamasına yardımcı olur. Benzersiz bir değer olmalıdır.

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

- Bir bileşen üzerinde `v-model` kullanılarak iki yönlü veri bağlama sağlanabilir. Bileşen, `modelValue` adında bir `prop` kabul etmeli ve değeri güncellediğinde `update:modelValue` olayını yaymalıdır.

### Fallthrough Attributes

- Bir bileşene aktarılan ancak `props` veya `emits` olarak deklare edilmeyen attribute'lardır. Varsayılan olarak, bu attribute'lar bileşenin kök elementine uygulanır.
- `inheritAttrs: false` ile bu davranış devre dışı bırakılabilir ve `$attrs` ile bu attribute'lara erişilebilir.

### Slots

- **Named Slots:** Birden fazla içerik yerleştirme noktası sağlamak için isimlendirilmiş slotlar kullanılır. `<slot name="header"></slot>`
- **Scoped Slots:** Slot içeriğinin, alt bileşenden veri almasını sağlar. Bu, slot içeriğini daha yetenekli hale getirir.

### Provide / Inject

- Derinlemesine iç içe geçmiş bileşenler arasında veri aktarımı için kullanılır. Bir üst bileşen veri `provide` eder, herhangi bir alt bileşen bu veriyi `inject` edebilir. Bu, "prop drilling" (prop'ları katman katman aşağı geçirme) sorununu çözer.

### Async Components (Asenkron Bileşenler)

- `defineAsyncComponent` ile tanımlanır. Sadece ihtiyaç duyulduğunda yüklenirler (lazy loading). Bu, uygulamanın başlangıç yükleme süresini (initial load time) azaltmaya yardımcı olur.
