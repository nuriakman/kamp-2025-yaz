# Laravel ile Kullanılabilecek API Yaklaşımları

Aşağıda Laravel ekosisteminde yaygın API türleri, ne zaman tercih edileceği ve önerilen paketlerle kısa özetler yer alır.

## 1) REST (JSON)

- Ne zaman: Standart CRUD, geniş istemci uyumluluğu.
- Laravel dosyaları: `routes/api.php`, `app/Http/Controllers/*`, `app/Http/Resources/*`
- Kimlik doğrulama:
  - Sanctum: SPA/mobil için hafif token ve cookie tabanlı.
  - Passport: OAuth2 akışları.
  - JWT: php-open-source-saver/jwt-auth (projenizde kurulu olabilir).
- Paketler:
  - Auth: `laravel/sanctum`, `laravel/passport`, `php-open-source-saver/jwt-auth`
  - Dokümantasyon: `darkaonline/l5-swagger` (Swagger/OpenAPI)
- Artıları: Basit, olgun ekosistem.
- Eksileri: Over/under-fetching yaşanabilir.

## 2) GraphQL

- Ne zaman: İstemci her ekranda farklı alanlara ihtiyaç duyuyor, tek endpoint ile esnek sorgu.
- Paket: `nuwave/lighthouse` (schema-first, Laravel ile en popüler).
- Kurulum özeti: `/graphql` endpoint, `graphql/schema.graphql`, resolver’lar policy/middleware ile korunur.
- Artıları: Over-fetching azalır, tip güvenli şema.
- Eksileri: Önbellek ve rate limit tasarımı ekstra efor.

## 3) gRPC

- Ne zaman: Mikroservisler arası yüksek performanslı, tip güvenli iletişim.
- Kullanım: PHP için gRPC client/server mümkündür; tarayıcı için genelde `grpc-web` + Envoy proxy gerekir.
- Paket/araçlar: `grpc/grpc` (PHP extension), `spiral/roadrunner` (performans), `envoyproxy/envoy` (grpc-web).
- Artıları: Hızlı, streaming, düşük gecikme.
- Eksileri: PHP tarafında server deneyimi REST’e göre daha karmaşık.

## 4) WebSockets / SSE (Gerçek Zamanlı)

- Ne zaman: Chat, bildirim, canlı paneller.
- WebSockets:
  - Paket: `beyondcode/laravel-websockets` (Pusher uyumlu, kendi sunucun).
  - İstemci: Laravel Echo + Pusher protokolü.
- SSE:
  - Basit tek yönlü akış; `Response::stream()` veya yardımcı paketlerle.
- Artıları: Düşük gecikme, push.
- Eksileri: Ölçeklendirme ve bağlantı yönetimi ek tasarım ister.

## 5) SOAP

- Ne zaman: Kurumsal/legacy sistemlerle WSDL sözleşmesi.
- Paket: `artisaninweb/laravel-soap` veya PHP `ext-soap`.
- Artıları: Standartlaşmış güvenlik/işlem (WS-\*)
- Eksileri: XML yükü, karmaşıklık.

## 6) JSON-RPC / RPC

- Ne zaman: “Metot çağrısı” modeli isteyen basit servisler.
- Paketler: Genel PHP JSON-RPC paketleri Laravel ile entegre edilebilir.
- Artıları: Hafif, basit.
- Eksileri: Keşif/dokümantasyon ek efor.

## 7) OData

- Ne zaman: URL’de standartlaştırılmış sorgu semantiği (filter, select, expand).
- Not: PHP/Laravel tarafında sınırlı olgun paket; genelde REST + kriter parametreleri ile benzetim yapılır.

## 8) Event-Driven / Mesajlaşma (Webhooks, MQ)

- Webhooks: Ödeme sağlayıcıları vb. için `routes/api.php` altında imzalı endpoint’ler.
- MQ/Streaming: Kafka/RabbitMQ/MQTT ile asenkron; Laravel Queue’lar (Redis, SQS) sık kullanılır.
- Paketler: `vladimir-yuldashev/laravel-queue-rabbitmq`, MQTT istemcileri, Kafka için PHP client’ları.
- Artıları: Gevşek bağlı, ölçeklenebilir.
- Eksileri: Mimari karmaşıklık.

---

## Hızlı Seçim Önerisi

- Basit/klasik CRUD + geniş istemci: REST (+ Sanctum/Passport/JWT)
- Mobil/çoklu ekran, alan bazlı veri: GraphQL (Lighthouse)
- Mikroservis arası yüksek performans: gRPC (+ Envoy)
- Gerçek zamanlı push: WebSockets (Laravel Echo + beyondcode)
- Legacy/kurumsal: SOAP
- Olay tabanlı entegrasyon: Webhooks + Queue
