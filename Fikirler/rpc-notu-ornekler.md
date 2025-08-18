# JSON-RPC Örnekleri: addUser ve listUsers (Laravel)

Bu dosya, `Fikirler/rpc-notu.md` içindeki JSON-RPC yaklaşımına iki pratik örnek ekler: `User.addUser` ve `User.listUsers`.

## 1) Endpoint

- HTTP POST `/api/rpc`
- Header: `Accept: application/json`, (opsiyonel) `Authorization: Bearer <JWT>`

## 2) İstek/Yanıt Örnekleri

### 2.1 addUser

İstek:

```json
{
  "jsonrpc": "2.0",
  "id": "1",
  "method": "User.addUser",
  "params": {
    "name": "Ada Lovelace",
    "email": "ada@example.com",
    "password": "secret123"
  }
}
```

Yanıt (başarılı):

```json
{
  "jsonrpc": "2.0",
  "id": "1",
  "result": {
    "id": 101,
    "status": "created"
  }
}
```

Yanıt (validasyon hatası örneği):

```json
{
  "jsonrpc": "2.0",
  "id": "1",
  "error": {
    "code": -32001,
    "message": "Validation error",
    "data": {
      "email": ["taken"]
    }
  }
}
```

### 2.2 listUsers

İstek:

```json
{
  "jsonrpc": "2.0",
  "id": "2",
  "method": "User.listUsers",
  "params": {
    "page": 1,
    "perPage": 10
  }
}
```

Yanıt (başarılı):

```json
{
  "jsonrpc": "2.0",
  "id": "2",
  "result": {
    "data": [
      {
        "id": 1,
        "name": "John",
        "email": "john@example.com"
      }
    ],
    "pagination": {
      "page": 1,
      "perPage": 10,
      "total": 42
    }
  }
}
```

---

## 3) Controller Ekleme (Özet Kod)

```php
// routes/api.php
Route::post('/rpc', [\App\Http\Controllers\RpcController::class, 'handle']);
```

```php
<?php
// app/Http/Controllers/RpcController.php (ilgili bölümler)

namespace App\Http\Controllers;

use App\Models\User; // User modeli varsayıldı
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RpcController extends Controller
{
    private array $methods = [
        // mevcutlar...
        'User.resetPassword' => [self::class, 'resetPassword'],
        // yeni eklenenler
        'User.addUser' => [self::class, 'addUser'],
        'User.listUsers' => [self::class, 'listUsers'],
    ];

    public function handle(Request $request): Response
    {
        $payload = $request->json()->all();
        $id = $payload['id'] ?? null;
        $method = $payload['method'] ?? null;
        $params = $payload['params'] ?? [];

        if (!isset($this->methods[$method])) {
            return response()->json([
                'jsonrpc' => '2.0', 'id' => $id,
                'error' => ['code' => -32601, 'message' => 'Method not found'],
            ]);
        }

        try {
            $result = call_user_func($this->methods[$method], $params);
            return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result]);
        } catch (\Throwable $e) {
            return response()->json([
                'jsonrpc' => '2.0', 'id' => $id,
                'error' => ['code' => -32000, 'message' => 'Server error']
            ], 500);
        }
    }


    private static function addUser(array $params): array
    {
        $v = Validator::make($params, [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($v->fails()) {
            throw new \InvalidArgumentException(json_encode($v->errors()->toArray()));
        }

        $user = User::create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
        ]);

        return ['id' => $user->id, 'status' => 'created'];
    }

    private static function listUsers(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $perPage = (int)($params['perPage'] ?? 10);
        $perPage = max(1, min($perPage, 100));

        $paginator = User::query()->select(['id','name','email'])
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    private static function resetPassword(array $params): array
    {
        $v = Validator::make($params, [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($v->fails()) {
            throw new \InvalidArgumentException(json_encode($v->errors()->toArray()));
        }

        $status = Password::sendResetLink([
            'email' => $params['email'],
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return ['status' => 'email_sent'];
        }

        throw new \RuntimeException('Reset link could not be sent');
    }
}
```

### Hata Yakalama ve JSON-RPC Hata Kodları

`handle()` içinde validasyon kaynaklı hataları JSON-RPC `error` formatına dönüştürmek için try-catch genişletilebilir:

```php
try {
    $result = call_user_func($this->methods[$method], $params);
    return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result]);
} catch (\InvalidArgumentException $e) {
    return response()->json([
        'jsonrpc' => '2.0','id' => $id,
        'error' => ['code' => -32001, 'message' => 'Validation error', 'data' => json_decode($e->getMessage(), true)]
    ], 422);
} catch (\Throwable $e) {
    return response()->json([
        'jsonrpc' => '2.0','id' => $id,
        'error' => ['code' => -32000, 'message' => 'Server error']
    ], 500);
}
```

---

## 4) Güvenlik ve En İyi Uygulamalar

- JWT ile kimlik doğrulama (Authorization header) ve rate limiting.
- Metot adlarını whitelist ile sınırla; dinamik `call_user_func` çağrılarını dikkatle kullan.
- Versiyonlama: `v1.User.addUser`, `v1.User.listUsers` gibi önek.
- Girdi doğrulamada `FormRequest` kullanımı tercih edilebilir.

---

Bu dosyayı `Fikirler/rpc-notu.md` içinden “Örnekler” olarak bağlayabilirsin. İstersen bağlantıyı ben de ekleyebilirim.
