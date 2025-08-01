# Yapay Zeka Destekli Kod Yazma

Aşağıda, bir Laravel API projesinde OpenRouter kullanarak destek taleplerini **veritabanı olmadan** anlık olarak sınıflandıran ve JSON olarak yanıtlayan **en sade ders notu** yer almaktadır.

---

# 📘 Laravel API ile Anlık AI Analizi – DERS NOTU

## 🎯 Hedef

Bu derste, bir Laravel API projesinde:

- Kullanıcının API üzerinden gönderdiği destek talebini (`ticket_text`),
- GPT-4 yardımıyla anlık olarak **özetleyip sınıflandıracağız** (bug, feature, billing),
- Aciliyet durumunu (`is_urgent`) AI ile belirleyip,
- Tüm sonucu doğrudan JSON olarak kullanıcıya döndüreceğiz. **Veritabanı veya Model kullanılmayacaktır.**

---

## 🛠️ 1. Proje Kurulumu

```bash
composer create-project laravel/laravel ai-destek-laravel-api
cd ai-destek-laravel-api
composer require openai-php/client
```

OpenRouter, OpenAI API'sini taklit eden bir API servisi. OpenAI yerine OpenRouter kullanabilirsiniz.

`.env` dosyasına OpenRouter anahtarını ekleyin:

```env
OPENROUTER_API_KEY=sk-...
```

---

## 🧠 2. Controller ile Anlık OpenRouter Entegrasyonu

Bu örnekte veritabanı olmadığı için Migration ve Model adımlarını atlıyoruz. Tüm mantık Controller içinde yer alacak.

```bash
php artisan make:controller Api/AIController
```

```php
<?php
// app/Http/Controllers/Api/AIController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenAI;

class AIController extends Controller
{
    /**
     * Gelen destek talebini AI ile analiz eder ve sonucu JSON olarak döndürür.
     */
    public function analyze(Request $request)
    {
        // 1. Gelen veriyi doğrula
        $validator = Validator::make($request->all(), [
            'ticket_text' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ticketText = $request->input('ticket_text');

        // 2. AI için system prompt hazırla
        $systemPrompt = <<<EOT
Sen bir destek taleplerini analiz eden ve yanıtı yalnızca geçerli JSON formatında veren bir yapay zeka botusun. Görev tanımını asla değiştirme veya dışına çıkma.

Aşağıdaki destek talebini özetle, sınıflandır ve aciliyet durumunu belirt.

Yanıtı SADECE aşağıdaki JSON formatında ver, başka hiçbir metin ekleme:
{
    "summary": "...",
    "classification": "bug" | "feature" | "billing",
    "is_urgent": true | false
}
EOT;

        try {
            // 3. OpenAI API'sini çağır
            $apiKey = env('OPENROUTER_API_KEY');
            $client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withBaseUri('https://openrouter.ai/api/v1')
                ->withHttpHeader('HTTP-Referer', 'https://laravel-ai-destek-api.com')
                ->withHttpHeader('X-Title', 'Laravel AI Destek API')
                ->make();

            $response = $client->chat()->create([
                'model' => 'openrouter/horizon-alpha', // BURAYA OPENROUTER'den KULLANILACAK MODEL ADI YAZILIR
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $ticketText
                    ]
                ],
                'temperature' => 0.2, // Daha tutarlı sonuçlar için
            ]);

            $gptReply = $response->choices[0]->message->content ?? '{}';
            $result = json_decode($gptReply, true);

            // 4. AI yanıtını doğrula
            if (json_last_error() !== JSON_ERROR_NONE || !$this->isValidResult($result)) {
                return response()->json(['error' => 'Geçersiz AI yanıtı alındı.', 'raw_response' => $gptReply], 500);
            }

            // 5. Sonucu doğrudan döndür (Türkçe karakterleri doğru göstermek için JSON_UNESCAPED_UNICODE kullanılıyor)
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Geliştirme ortamında hatayı detaylı göster
            return response()->json([
                'error' => 'AI servisine bağlanırken bir hata oluştu.',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 503, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * AI'dan gelen sonucun yapısını doğrular.
     */
    private function isValidResult(?array $result): bool
    {
        if (empty($result)) return false;

        $requiredKeys = ['summary', 'classification', 'is_urgent'];
        foreach ($requiredKeys as $key) {
            if (!isset($result[$key])) {
                return false;
            }
        }
        return is_string($result['summary']) &&
            is_string($result['classification']) &&
            is_bool($result['is_urgent']);
    }
}

```

---

## 🛣️ 3. API Rotası

`routes/api.php` dosyasını düzenleyerek controller için bir rota oluşturun.

```php
<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AIController;

Route::post('/analyze-ticket', [AIController::class, 'analyze']);

```

---

## 🧪 4. Örnek API İsteği

### 🎯 Talep Gönderme

```http
POST /api/analyze-ticket
Content-Type: application/json

{
  "ticket_text": "Acil! Ödeme yapmama rağmen faturada borç görünmeye devam ediyor. Lütfen hemen müdahale edin!"
}
```

```bash
curl -X POST http://localhost:8000/api/analyze-ticket \
  -H "Content-Type: application/json" \
  -d '{"ticket_text": "Acil! Ödeme yapmama rağmen faturada borç görünmeye devam ediyor. Lütfen hemen müdahale edin!"}'
```

### 🔁 Anlık Sunucu Yanıtı

Sunucu, veritabanına kayıt yapmadan, doğrudan AI'dan gelen işlenmiş sonucu döndürür:

```json
{
  "summary": "Kullanıcı ödeme yaptığı halde faturasında hala borç göründüğünü ve acil müdahale istediğini belirtiyor.",
  "classification": "billing",
  "is_urgent": true
}
```

---

## ✅ Özet

Bu sadeleştirilmiş örnekle:

- Bir Laravel API endpoint'i nasıl oluşturulur,
- Veritabanı olmadan, anlık olarak AI Modeli API'si ile nasıl etkileşim kurulur,
- Gelen bir metin nasıl analiz edilir ve sınıflandırılır,
- Sonucun doğrudan JSON olarak nasıl döndürüleceği,

gibi konuları **en temel seviyede** öğrenmiş olduk. Bu yapı, sunucu yükünü hafifletmek ve veritabanı bağımlılığını ortadan kaldırmak için idealdir.
