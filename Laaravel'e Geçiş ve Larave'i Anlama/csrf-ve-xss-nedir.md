# CSRF ve XSS Nedir?

Harika iki konu! Laravel'de güvenlikle ilgili bu iki konu çok önemli:

İkisini de **ayrı ayrı**, açık ve sade şekilde anlatayım:

---

## 🛡️ 1. CSRF (Cross-Site Request Forgery)

### ✅ Nedir?

Bir kullanıcının **haberi olmadan**, tarayıcısındaki oturum bilgileri (cookie, token) kullanılarak **yetkili bir işlem yapılmasını sağlamak** isteyen saldırıdır.

> Örneğin: Kullanıcı kendi banka hesabına giriş yaptı ve oturum açıkken başka bir sitedeki zararlı form üzerinden onun adına para transferi yapılmaya çalışılıyor.

---

### 🧰 Laravel'de CSRF Koruması Nasıl Çalışır?

Laravel, her `POST`, `PUT`, `PATCH`, `DELETE` isteğinde **gizli bir `CSRF token`** kontrolü yapar.

### 🔐 CSRF Token nasıl kullanılır?

Blade dosyasında formlar içinde otomatik eklenir:

```blade
<form method="POST" action="/guncelle">
    @csrf
    <input type="text" name="ad">
    <button type="submit">Gönder</button>
</form>
```

> `@csrf` Blade direktifi, form içine gizli bir `<input type="hidden" name="_token" value="...">` alanı ekler.

---

### 🛠️ API Kullanıyorsan?

API için genellikle **CSRF koruması kapalı olur** çünkü:

- API token'la (örneğin JWT) korunur.
- `web` middleware grubu değil, `api` grubu kullanılır.

---

## 🧪 CSRF Kontrolünü Manuel Doğrulama

Controller'da elle kontrol etmek istersen:

```php
if (Session::token() !== $request->_token) {
    abort(419); // Token mismatch
}
```

Ama genellikle Laravel middleware bunu otomatik yapar:
`VerifyCsrfToken` sınıfı (`App\Http\Middleware`) bu işi üstlenir.

---

## ☣️ 2. XSS (Cross-Site Scripting)

### ✅ Nedir?

Saldırgan, form alanlarına veya URL parametrelerine **JavaScript gibi zararlı kodlar** yazarak,
bu kodun başka kullanıcıların tarayıcısında çalışmasını sağlar.

> Örn: `<script>alert('HACK');</script>` gibi kodlar kullanıcıların oturum bilgilerini çalabilir.

---

### 🔐 Laravel'de XSS'e Karşı Koruma

Blade dosyalarında **`{{ }}` süslü parantezler** içerikleri **otomatik olarak `htmlspecialchars()` ile kaçışlar**.

#### Güvenli:

```blade
{{ $mesaj }}  // XSS'e karşı korumalıdır
```

#### Tehlikeli:

```blade
{!! $mesaj !!}  // HTML olduğu gibi yazılır! Güvenli değilse XSS olabilir.
```

---

### 🛠️ Form ve Girdi Kontrolü

- Formlardan gelen verileri `strip_tags()`, `htmlspecialchars()`, `Purifier::clean()` gibi yöntemlerle temizleyebilirsin.
- Laravel'de XSS temizleme için bazı paketler kullanılabilir:
  👉 `mews/purifier` gibi.

---

### 🛡️ XSS ve CSRF Arasındaki Fark

| Konu                  | CSRF                               | XSS                                           |
| --------------------- | ---------------------------------- | --------------------------------------------- |
| Amaç                  | Kullanıcının adına işlem yaptırmak | Zararlı kodu kullanıcıya çalıştırmak          |
| Saldırı Nereden Gelir | Dış siteden gönderilen istekle     | Kullanıcının tarayıcısında çalıştırılan kodla |
| Koruma                | CSRF token                         | Girdi filtreleme, otomatik kaçış              |

---

## ✅ Özet

### CSRF

- Laravel'de varsayılan olarak korunur (`@csrf`).
- Sadece `POST/PUT/DELETE` gibi isteklerde gerekir.
- API'de gerekmez (token-based auth varsa).

### XSS

- Blade `{{ }}` ile otomatik korunur.
- HTML çıktısı veriyorsan `{!! !!}` dikkatli kullan.
- Girdileri temizle (`strip_tags`, `purifier` vs).

---
