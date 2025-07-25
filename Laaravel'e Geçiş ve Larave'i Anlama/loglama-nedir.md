# Loglama Nedir?

## ✅ **LOGGING (Loglama) Nedir?**

Laravel’de loglama, uygulamada meydana gelen olayların (örneğin hata, uyarı, bilgilendirme gibi) kaydını tutmamıza olanak sağlar.

Bu kayıtlar sayesinde:

- Hataları tespit edebiliriz.
- Kullanıcı aktivitelerini izleyebiliriz.
- Performans sorunlarını görebiliriz.
- Uygulamanın geçmiş olaylarını analiz edebiliriz.

---

## 🧠 **Kısaca Teknik Tanım**

Laravel, arka planda [Monolog](https://github.com/Seldaek/monolog) kütüphanesini kullanır. Bu sistem sayesinde loglar:

- Dosyaya
- Slack’e
- Veritabanına
- Syslog’a
- ya da özel servislere gönderilebilir.

---

## 🔧 **Log Yapılandırması**

### Dosya:

```bash
config/logging.php
```

### .env içinde hangi log türü kullanılacağı:

```env
LOG_CHANNEL=stack
```

---

## ✍️ **Kod Örnekleri**

```php
use Illuminate\Support\Facades\Log;

Log::debug('Detaylı hata ayıklama mesajı');
Log::info('Kullanıcı giriş yaptı.', ['user_id' => 5]);
Log::warning('Yüksek bellek kullanımı algılandı');
Log::error('Ödeme sırasında hata oluştu');
Log::critical('Sunucu çökmesi!');
```

---

## 📁 **Log Dosyasının Yolu**

Varsayılan dosya:

```bash
storage/logs/laravel.log
```

---

## 🔂 **Log Seviyeleri Açıklaması**

| Seviye      | Açıklama                            |
| ----------- | ----------------------------------- |
| `debug`     | Hata ayıklama bilgileri             |
| `info`      | Genel bilgi mesajları               |
| `notice`    | Önemli ama acil olmayan bilgiler    |
| `warning`   | Uyarılar, küçük sorunlar            |
| `error`     | Hatalar (işlem tamamlanmadı)        |
| `critical`  | Uygulama çalışmasına engel sorunlar |
| `alert`     | Hemen müdahale edilmesi gerekenler  |
| `emergency` | Sistem tamamen kullanılamaz halde   |

---

## 🎛️ **Log Driver Türleri (log_channel)**

| Kanal    | Açıklama                                 |
| -------- | ---------------------------------------- |
| `single` | Tek bir log dosyası                      |
| `daily`  | Günlük log dosyası oluşturur             |
| `stack`  | Birden fazla driver'ı aynı anda kullanır |
| `slack`  | Slack kanalına log gönderir              |
| `syslog` | Sunucu sistem loglarına yazar            |

---

## 📦 **Gelişmiş Kullanım: Özel Kanal Oluşturma**

```php
// config/logging.php

'channels' => [
    'ozel_log' => [
        'driver' => 'single',
        'path' => storage_path('logs/ozel.log'),
        'level' => 'debug',
    ],
]
```

Kullanımı:

```php
Log::channel('ozel_log')->info('Özel log kaydı yapıldı.');
```

---

## ✅ Ne Zaman Log Kullanmalısın?

| Durum                          | Kullanılacak seviye  |
| ------------------------------ | -------------------- |
| Başarılı bir işlem             | `info`               |
| Beklenen ama normal dışı durum | `warning`            |
| Gerçek bir hata                | `error`              |
| Kritik sistem hatası           | `critical` / `alert` |

---

## 👨‍💻 Uygulamalı Mini Örnek

### Kullanıcının girişini logla:

```php
public function login(Request $request)
{
    if (Auth::attempt($request->only('email', 'password'))) {
        Log::info('Giriş başarılı', ['user_id' => Auth::id()]);
        return response()->json(['message' => 'Giriş başarılı']);
    }

    Log::warning('Giriş denemesi başarısız', ['email' => $request->email]);
    return response()->json(['message' => 'Hatalı giriş'], 401);
}
```
