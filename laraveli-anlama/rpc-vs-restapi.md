# Laravel API içinde RESTfull API veya RPC (Remote Procedure Call)?

**RPC (Remote Procedure Call)** ve **REST (Representational State Transfer)**, farklı ihtiyaçlara hitap eden iki farklı API mimarisidir. İşte detaylı bir karşılaştırma:

### **1. Temel Felsefe**

- **RPC**: Fonksiyon çağrılarını uzak bir sunucuda çalıştırmaya odaklanır.
  - Örnek: `getUser(123)`, `deleteOrder(456)` gibi direkt işlemler.
- **REST**: Kaynak (resource) tabanlıdır, HTTP metodlarıyla (GET, POST, PUT, DELETE) CRUD işlemleri yapar.
  - Örnek: `GET /users/123`, `DELETE /orders/456`.

### **2. Protokol ve Format**

- **RPC**:
  - Genelde **HTTP/1.1** veya **HTTP/2** üzerinden çalışır.
  - Veri formatı: **JSON-RPC**, **XML-RPC**, **gRPC** (binary veri için).
- **REST**:
  - **HTTP/1.1** standartlarını kullanır.
  - Veri formatı: Genellikle **JSON** veya **XML**.

### **3. Esneklik ve Standartlar**

- **RPC**:
  - Katı bir yapı sunar (özellikle gRPC gibi tipli sistemlerde).
  - İstemci/sunucu arasında sözleşme (contract) gerektirir (örneğin, **Protocol Buffers**).
- **REST**:
  - Daha esnek ve insan tarafından okunabilir (human-readable).
  - **HATEOAS** (Hypermedia as the Engine of Application State) gibi standartlarla kendini tanımlayabilir.

---

### **4. Performans**

- **RPC**:
  - Özellikle **gRPC** gibi binary protokoller **daha hızlı** ve düşük gecikmelidir (low latency).
  - **Çoklu istek (multiplexing)** destekler (HTTP/2 sayesinde).
- **REST**:
  - JSON/XML parsingi RPC'ye göre daha yavaştır.
  - HTTP/1.1'de **bağlantı açma/kapama** ek yük getirebilir.

---

### **5. Kullanım Senaryoları**

- **RPC Tercih Sebepleri**:
  - Mikroservisler arası **yüksek performans** iletişimi (gRPC).
  - Dahili (internal) sistemlerde **tipli (typed) iletişim**.
  - Gerçek zamanlı (real-time) uygulamalar.
- **REST Tercih Sebepleri**:
  - Public API'lar için **standart ve anlaşılır** yapı.
  - Web ve mobil uygulamalarda **geniş destek**.
  - Cache'lenebilir (önbelleklenebilir) istekler.

### **Sonuç**

- **RPC** (özellikle **gRPC**), **yüksek performans** ve **dahili mikroservis iletişimi** için idealdir.
- **REST**, **public API'lar** ve **geniş uyumluluk** gerektiren durumlarda daha uygundur.

Projenin ihtiyacına göre ikisini bir arada da kullanabilirsiniz (örneğin, dahili iletişimde gRPC + dış API'larda REST).

## Laravel içinde RPC (Remote Procedure Call)

**Laravel içinde RPC (Remote Procedure Call)** mantığıyla çalışan bir yapı kurmak için mutlaka bir kütüphane kullanmak **zorunda değilsin**. Laravel, kendi içinde HTTP üzerinden çalışacak şekilde yapılandırılmış olsa da, temel PHP özellikleri ve Laravel'in sunduğu olanaklarla basit veya gelişmiş RPC sistemleri oluşturabilirsin.

### **HTTP tabanlı RPC** (JSON-RPC, REST gibi)

Laravel zaten HTTP tabanlı bir framework olduğu için, klasik controller metodlarını birer "uzak prosedür" gibi çalıştırabilirsin. Örnek:

```http
POST /rpc/user/get
{
    "method": "getUser",
    "params": { "id": 42 }
}
```

Laravel'de bunu karşılayan bir controller yazarsın, gelen `method` alanına göre uygun işlemi yaparsın. Örnek:

```php
public function handleRpc(Request $request)
{
    $method = $request->input('method');
    $params = $request->input('params');

    return match ($method) {
        'getUser' => $this->getUser($params),
        'createUser' => $this->createUser($params),
        default => response()->json(['error' => 'Method not found'], 404),
    };
}
```

## **RPC için kütüphane kullanmak ne zaman mantıklı olur?**

Kütüphane kullanmak şu durumlarda faydalıdır:

- **JSON-RPC 2.0 gibi standartlara tam uyumluluk** istiyorsan
- **Method routing, hata yapısı, batch request** gibi şeyleri kendin elle kodlamak istemiyorsan
- **Client tarafında da (örneğin JS/TS) entegre olmasını** istiyorsan
- **gRPC gibi binary tabanlı RPC sistemleri kurmak istiyorsan**

Laravel ile uyumlu bazı RPC kütüphaneleri:

- [spatie/json-rpc](https://github.com/spatie/json-rpc): JSON-RPC 2.0 standardını destekleyen sade ve kullanışlı bir Laravel paketi.
- [google/protobuf + grpc/grpc-php](https://github.com/grpc/grpc): Laravel ile değil, genel PHP ile daha çok uyumlu. gRPC protokolü ile çalışmak istersen.

---

## Kütüphane kullanmadan Laravel içinde RPC tarzı bir yapı nasıl kurabilirim?

### Basit bir örnek:

```php
// route: POST /rpc

Route::post('/rpc', [RpcController::class, 'handle']);

class RpcController extends Controller
{
    public function handle(Request $request)
    {
        $method = $request->input('method');
        $params = $request->input('params', []);

        if (!method_exists($this, $method)) {
            return response()->json(['error' => 'Method not found'], 404);
        }

        return $this->$method(...array_values($params));
    }

    protected function getUser($id)
    {
        return User::findOrFail($id);
    }

    protected function sayHello($name)
    {
        return "Hello, $name";
    }
}
```

---

## ✅ Sonuç

- Laravel ile RPC tarzı yapı kurmak mümkündür.
- **Kütüphane kullanmak şart değildir**, ama **standartlara uyum** ve **daha karmaşık ihtiyaçlar** için önerilir.
- Laravel için en uygun olanı genellikle **JSON-RPC** protokolüdür.
