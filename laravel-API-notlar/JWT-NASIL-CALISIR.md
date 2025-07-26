# JWT (JSON Web Token) Nasıl Çalışır? - Kapsamlı Ders Notu

## 📚 İçindekiler

1. [JWT Nedir?](#jwt-nedir)
2. [Neden JWT'ye İhtiyacımız Var?](#neden-jwtye-ihtiyacımız-var)
3. [JWT'nin Avantajları](#jwtnin-avantajları)
4. [JWT'nin Dezavantajları](#jwtnin-dezavantajları)
5. [JWT Yapısı](#jwt-yapısı)
6. [JWT Çalışma Mantığı](#jwt-çalışma-mantığı)
7. [Laravel'de JWT Akışı](#laravelde-jwt-akışı)
8. [Güvenlik Konuları](#güvenlik-konuları)
9. [Best Practices](#best-practices)
10. [Alternatif Yöntemler](#alternatif-yöntemler)

## JWT Nedir?

**JWT (JSON Web Token)**, kullanıcı kimlik doğrulama ve yetkilendirme işlemleri için kullanılan açık standart (RFC 7519) bir token formatıdır. JWT, bilgileri JSON nesneleri olarak güvenli bir şekilde taraflar arasında iletmek için kompakt ve kendi kendini doğrulayan (self-contained) bir yöntem sağlar.

### JWT'nin Temel Özellikleri:

- **Stateless (Durumsuz)**: Server'da session bilgisi saklamaz
- **Self-contained (Kendi kendini içeren)**: Token içinde tüm gerekli bilgiler bulunur
- **Portable (Taşınabilir)**: Farklı domain'ler ve servisler arasında kullanılabilir
- **Compact (Kompakt)**: URL, POST parameter veya HTTP header olarak kolayca gönderilebilir

## Neden JWT'ye İhtiyacımız Var?

### Geleneksel Session-Based Authentication Problemleri:

```mermaid
sequenceDiagram
    participant Client as 👤 İstemci<br/>(Tarayıcı / Uygulama)
    participant Server as 🌐 Laravel API
    participant Store as 🗄️ Session Store<br/>(Redis / DB)

    Client->>Server: 1️⃣ Login (email & şifre)
    Server->>Store: 2️⃣ Session oluştur
    Store-->>Server: 3️⃣ Session ID (örnek: `abc123`)
    Server-->>Client: 4️⃣ Session ID (Cookie olarak)

    Client->>Server: 5️⃣ Yetkili İstek (Session ID ile)
    Server->>Store: 6️⃣ Session ID geçerli mi?
    Store-->>Server: 7️⃣ Session verisi
    Server-->>Client: 8️⃣ JSON yanıt (veri, onay, vs.)

```

**Problemler:**

- 🔴 **Scalability**: Her server'da session store gerekli
- 🔴 **Memory Usage**: Server'da session bilgileri saklanır
- 🔴 **Cross-Domain**: Farklı domain'lerde çalışmaz
- 🔴 **Mobile Apps**: Mobile uygulamalar için uygun değil
- 🔴 **Microservices**: Mikroservis mimarisinde karmaşık

### JWT ile Çözüm:

```mermaid
sequenceDiagram
    participant Client as 👤 İstemci<br/>(Tarayıcı / Uygulama)
    participant Server as 🌐 Laravel API (JWT)

    Client->>Server: 1️⃣ Login (email & şifre)
    Server->>Server: 2️⃣ Kimlik bilgilerini doğrula
    Server->>Server: 3️⃣ JWT oluştur (şifreli token)
    Server-->>Client: 4️⃣ JWT Token gönder (response içinde)

    Client->>Server: 5️⃣ Korunan API isteği (Authorization: Bearer token)
    Server->>Server: 6️⃣ JWT doğrula (geçerli, süresi dolmamış?)
    Server-->>Client: 7️⃣ JSON yanıt (veri, onay, vs.)

```

**Avantajlar:**

- ✅ **Stateless**: Server'da bilgi saklamaya gerek yok
- ✅ **Scalable**: Horizontal scaling kolay
- ✅ **Cross-Domain**: Farklı domain'lerde çalışır
- ✅ **Mobile Friendly**: Mobile uygulamalar için ideal
- ✅ **Microservices**: Mikroservisler arası kolay paylaşım

## JWT'nin Avantajları

### 1. **Performans**

- Server'da session lookup işlemi yok
- Database sorgusu gerektirmez
- Hızlı token doğrulama

### 2. **Ölçeklenebilirlik**

- Horizontal scaling kolay
- Load balancer arkasında sorunsuz çalışır
- Server'lar arası session paylaşımı gerektirmez

### 3. **Güvenlik**

- Dijital imza ile doğrulama
- Tamper-proof (değiştirilemez)
- Expiration time kontrolü

### 4. **Esneklik**

- Custom claims eklenebilir
- Farklı servisler arasında paylaşılabilir
- Platform bağımsız

### 5. **Mobil Uyumluluk**

- Cookie gerektirmez
- HTTP header ile gönderilir
- Native mobile apps için ideal

## JWT'nin Dezavantajları

### 1. **Token Boyutu**

- Session ID'den daha büyük
- Her request'te gönderilir
- Network overhead

### 2. **Token İptali**

- Logout işlemi karmaşık
- Blacklist mekanizması gerekli
- Immediate revocation zor

### 3. **Güvenlik Riskleri**

- XSS saldırılarına açık (localStorage)
- Token çalınırsa risk
- Secret key güvenliği kritik

### 4. **Debugging**

- Token içeriği encoded
- Debug etmek daha zor
- Log'larda sensitive data riski

## JWT Yapısı

JWT üç bölümden oluşur ve nokta (.) ile ayrılır:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c
```

### 1. Header (Başlık)

```json
{
  "alg": "HS256",
  "typ": "JWT"
}
```

- **alg**: Kullanılan algoritma (HMAC SHA256, RSA, vb.)
- **typ**: Token tipi (JWT)

### 2. Payload (Veri)

```json
{
  "sub": "1234567890",
  "name": "John Doe",
  "iat": 1516239022,
  "exp": 1516242622
}
```

**Standart Claims:**

- **iss** (issuer): Token'ı oluşturan
- **sub** (subject): Token'ın konusu (genelde user ID)
- **aud** (audience): Token'ın hedef kitlesi
- **exp** (expiration): Son kullanma tarihi
- **nbf** (not before): Geçerlilik başlangıcı
- **iat** (issued at): Oluşturulma zamanı
- **jti** (JWT ID): Benzersiz token ID'si

**Custom Claims:**

- **name**: Kullanıcı adı
- **email**: Email adresi
- **role**: Kullanıcı rolü
- **permissions**: İzinler

### 3. Signature (İmza)

```javascript
HMACSHA256(base64UrlEncode(header) + "." + base64UrlEncode(payload), secret);
```

#### 🔍 İmza Nedir? (Basit Açıklama)

İmza, JWT'nin **güvenlik kalkanı**dır. Günlük hayattan bir örnekle açıklayalım:

**📝 Gerçek Hayat Örneği:**

- Bir mektup yazıyorsunuz ve altına imzanızı atıyorsunuz
- Bu imza, mektubun sizden geldiğini ve kimsenin içeriği değiştirmediğini kanıtlar
- Başkası imzanızı taklit etmeye çalışsa, fark edilir

**🔐 JWT'de İmza:**

#### 👶 Çocuğa Anlatır Gibi :)

**🍪 Kurabiye Kalıbı Örneği:**

- Anneniz özel bir kalıpla kurabiye yapar
- Bu kalıp sadece sizin evinizde var
- Aynı şekilli kurabiye gören herkes "Bu bizim evden!" der
- JWT imzası da böyle bir "kalıp" gibidir

**🎨 Parmak İzi Örneği:**

- Herkesin parmak izi farklıdır
- Parmak izinizi gören herkes sizin olduğunu anlar
- JWT imzası da token'ın "parmak izi" gibidir

#### 🔧 Teknik Detay - Hash Alma (İmza) Nasıl Oluyor?

**1️⃣ Adım: Veri Hazırlama**

```text
Header: {"alg":"HS256","typ":"JWT"}
Payload: {"user_id":123,"name":"Ahmet"}
```

**2️⃣ Adım: Base64 Kodlama**

```text
Header (Base64): eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9
Payload (Base64): eyJ1c2VyX2lkIjoxMjMsIm5hbWUiOiJBaG1ldCJ9
```

**3️⃣ Adım: Birleştirme**

```text
Birleşik Veri: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMjMsIm5hbWUiOiJBaG1ldCJ9
```

**4️⃣ Adım: Gizli Anahtar ile Hash**

```text
Gizli Anahtar: "super-secret-key-123"
Hash Algoritması: HMAC-SHA256

İmza = HMAC-SHA256(Birleşik Veri + Gizli Anahtar)
Sonuç: SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c
```

**5️⃣ Final Token:**

```text
Header.Payload.İmza
= eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMjMsIm5hbWUiOiJBaG1ldCJ9.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c
```

**🔍 Hash Nedir?**

- Hash = Parmak izi gibidir
- Hash = Veriyi "karıştırıp" sabit uzunlukta kod üretme
- Aynı veri → Aynı hash
- Farklı veri → Farklı hash
- Hash'ten orijinal veriyi bulmak imkansız

**🛡️ Neden Güvenli?**

- Gizli anahtarı bilmeyen kimse aynı hash'i üretemez
- Token'da tek karakter değişse, hash tamamen değişir
- Bu sayede token'ın değiştirilip değiştirilmediği anlaşılır

**⚡ Nasıl Çalışır:**

1. **Token Oluşturma:**

   ```text
   Header + Payload + Secret Key = İmza
   ```

2. **Token Doğrulama:**
   ```text
   Gelen Token'daki İmza == Yeniden Hesaplanan İmza ?
   ✅ Eşitse: Token geçerli
   ❌ Eşit değilse: Token sahte/değiştirilmiş
   ```

**🛡️ Güvenlik Özellikleri:**

- **Tamper-Proof**: Token'da tek bir karakter bile değişirse imza bozulur
- **Authenticity**: Token'ın gerçekten server'dan geldiğini kanıtlar
- **Integrity**: İçeriğin değiştirilmediğini garanti eder

**🚨 Önemli Not:**

- Secret key'i bilen herkes token oluşturabilir
- Bu yüzden secret key **mutlaka gizli** tutulmalıdır
- `.env` dosyasında saklanır ve asla paylaşılmaz

## JWT Çalışma Mantığı

### Genel JWT Akışı:

```mermaid
sequenceDiagram
    participant C as 👤 İstemci<br/>(Client)
    participant S as 🌐 Laravel API<br/>(Server)
    participant DB as 🗄️ Veritabanı<br/>(Database)

    Note over C,DB: 1️⃣ Kimlik Doğrulama Aşaması
    C->>S: POST /login (email, şifre)
    S->>DB: Kimlik bilgilerini doğrula
    DB-->>S: Kullanıcı verisi
    S->>S: JWT Token oluştur
    S-->>C: JWT Token + Kullanıcı bilgisi

    Note over C,DB: 2️⃣ Yetkilendirme Aşaması
    C->>S: GET /api/protected (Authorization: Bearer JWT)
    S->>S: JWT imzasını doğrula
    S->>S: Süre dolmuş mu kontrol et
    S->>S: Token'dan kullanıcı bilgisini çıkar
    S-->>C: Korumalı kaynak

    Note over C,DB: 3️⃣ Token Yenileme
    C->>S: POST /refresh (Authorization: Bearer JWT)
    S->>S: Mevcut token'ı doğrula
    S->>S: Yeni JWT oluştur
    S-->>C: Yeni JWT Token
```

### JWT Doğrulama Süreci:

```mermaid
flowchart TD
    A[🔑 JWT Token Alındı] --> B{📋 Token Formatı Geçerli?}
    B -->|❌ Hayır| C[🚫 401 Yetkisiz Erişim]
    B -->|✅ Evet| D[🔓 Header & Payload Çöz]
    D --> E{✍️ İmza Geçerli?}
    E -->|❌ Hayır| C
    E -->|✅ Evet| F{⏰ Token Süresi Dolmuş?}
    F -->|❌ Evet| C
    F -->|✅ Hayır| G{👤 Kullanıcı Var mı?}
    G -->|❌ Hayır| C
    G -->|✅ Evet| H[🎉 Erişim İzni Ver]

```

## Laravel'de JWT Akışı

### 1. Kullanıcı Kaydı ve Girişi:

```mermaid
sequenceDiagram
    participant C as 👤 İstemci<br/>(Client)
    participant AC as 🔐 AuthController
    participant U as 👥 User Model
    participant JWT as 🔑 JWT Servisi

    Note over C,JWT: 📝 Kullanıcı Kayıt Ağı
    C->>AC: POST /api/auth/register
    AC->>AC: Giriş verilerini doğrula
    AC->>U: Kullanıcı oluştur
    U-->>AC: Kullanıcı oluşturuldu
    AC-->>C: Başarı yanıtı

    Note over C,JWT: 🔓 Giriş Ağı
    C->>AC: POST /api/auth/login
    AC->>AC: Kimlik bilgilerini doğrula
    AC->>JWT: Token oluştur
    JWT-->>AC: JWT token
    AC-->>C: Token + kullanıcı bilgisi
```

### 2. Korumalı Endpoint Erişimi:

```mermaid
sequenceDiagram
    participant C as 👤 İstemci<br/>(Client)
    participant M as 🔒 JWT Middleware
    participant CC as 📁 CategoryController
    participant Cat as 🗄️ Category Model

    C->>M: GET /api/categories (Bearer Token)
    M->>M: Header'dan token'ı çıkar
    M->>M: JWT imzasını doğrula
    M->>M: Süre dolmuş mu kontrol et
    M->>M: Kimlik doğrulanmış kullanıcıyı ayarla
    M->>CC: İsteği yönlendir
    CC->>Cat: Kategorileri getir
    Cat-->>CC: Kategori verisi
    CC-->>C: JSON yanıt
```

### 3. Laravel JWT Middleware Akışı:

```mermaid
flowchart TD
    A[🔐 JWT ile İstek] --> B[🔒 JWT Middleware]
    B --> C{📋 Authorization Header Var mı?}
    C -->|❌ Hayır| D[🚫 401 Yetkisiz Döndür]
    C -->|✅ Evet| E[🔑 Token'ı Çıkar]
    E --> F{📝 Token Formatı Geçerli?}
    F -->|❌ Hayır| D
    F -->|✅ Evet| G[✍️ İmzayı Doğrula]
    G --> H{🔐 İmza Geçerli?}
    H -->|❌ Hayır| D
    H -->|✅ Evet| I{⏰ Token Süresi Dolmuş?}
    I -->|❌ Evet| D
    I -->|✅ Hayır| J[👤 Token'dan Kullanıcıyı Al]
    J --> K{👥 Kullanıcı Var mı?}
    K -->|❌ Hayır| D
    K -->|✅ Evet| L[🎯 Auth Kullanıcısını Ayarla]
    L --> M[➡️ Controller'a Devam Et]

```

## Güvenlik Konuları

### 1. **Token Saklama**

#### ❌ Güvensiz Yöntemler:

```javascript
// localStorage - XSS saldırılarına açık
localStorage.setItem("token", jwtToken);

// sessionStorage - XSS saldırılarına açık
sessionStorage.setItem("token", jwtToken);
```

#### ✅ Güvenli Yöntemler:

```javascript
// httpOnly cookie (en güvenli)
// Server tarafında set edilir
Set-Cookie: token=jwt_token; HttpOnly; Secure; SameSite=Strict

// Memory'de saklama (SPA için)
// Sayfa yenilendiğinde kaybolur
let authToken = jwtToken;
```

### 2. **Secret Key Güvenliği**

```bash
# .env dosyasında güçlü secret key
JWT_SECRET=very_long_random_string_at_least_256_bits

# Production'da environment variable olarak
export JWT_SECRET="production_secret_key"
```

### 3. **Token Expiration**

```php
// Kısa süreli access token
'ttl' => 60, // 1 saat

// Refresh token ile yenileme
'refresh_ttl' => 20160, // 2 hafta
```

### 4. **HTTPS Kullanımı**

```nginx
# Nginx konfigürasyonu
server {
    listen 443 ssl;
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # JWT token'ları sadece HTTPS üzerinden
    add_header Strict-Transport-Security "max-age=31536000";
}
```

## Best Practices

### 1. **Token Yapısı**

```php
// Minimal payload - sadece gerekli bilgiler
$payload = [
    'sub' => $user->id,           // User ID
    'iat' => time(),              // Issued at
    'exp' => time() + 3600,       // Expires in 1 hour
    'jti' => Str::uuid(),         // Unique token ID
    // Sensitive bilgileri eklemeyin!
];
```

### 2. **Error Handling**

```php
public function refresh()
{
    try {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());
        return $this->createNewToken($newToken);
    } catch (TokenExpiredException $e) {
        return response()->json(['error' => 'Token expired'], 401);
    } catch (TokenInvalidException $e) {
        return response()->json(['error' => 'Token invalid'], 401);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Token error'], 500);
    }
}
```

### 3. **Rate Limiting**

```php
// routes/api.php
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
});
```

### 4. **Token Blacklisting**

```php
// Logout işleminde token'ı blacklist'e ekle
public function logout()
{
    try {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Failed to logout'], 500);
    }
}
```

## Alternatif Yöntemler

### 1. **Session-Based Authentication**

```mermaid
graph LR
    A[Client] --> B[Server]
    B --> C[Session Store]
    C --> D[Database/Redis]

```

**Kullanım Alanları:**

- Geleneksel web uygulamaları
- Monolitik mimariler
- Yüksek güvenlik gerektiren uygulamalar

### 2. **OAuth 2.0**

```mermaid
sequenceDiagram
    participant C as 👤 İstemci<br/>(Client)
    participant AS as 🔐 Auth Server<br/>(Yetkilendirme Sunucusu)
    participant RS as 🌐 Resource Server<br/>(Kaynak Sunucusu)

    C->>AS: Yetkilendirme İsteği
    AS-->>C: Yetkilendirme Kodu
    C->>AS: Token İsteği (kod ile)
    AS-->>C: Erişim Token'ı
    C->>RS: API İsteği (token ile)
    RS-->>C: Korumalı Kaynak
```

**Kullanım Alanları:**

- Third-party integrations
- Social login (Google, Facebook)
- Enterprise applications

### 3. **API Keys**

```http
GET /api/data
Authorization: Bearer api_key_here
```

**Kullanım Alanları:**

- Basit API'ler
- Machine-to-machine communication
- Public API'ler

## Performans Karşılaştırması

### JWT vs Session Performance:

```mermaid
graph TD
    subgraph "JWT Authentication"
        A1[Request] --> B1[Extract JWT]
        B1 --> C1[Verify Signature]
        C1 --> D1[Check Expiration]
        D1 --> E1[Grant Access]
    end

    subgraph "Session Authentication"
        A2[Request] --> B2[Extract Session ID]
        B2 --> C2[Database Lookup]
        C2 --> D2[Validate Session]
        D2 --> E2[Grant Access]
    end

```

**JWT Avantajları:**

- ✅ No database lookup
- ✅ Faster verification
- ✅ Stateless

**Session Avantajları:**

- ✅ Smaller request size
- ✅ Easy revocation
- ✅ Server-side control

## Gerçek Dünya Senaryoları

### 1. **E-commerce Uygulaması**

```mermaid
graph TD
    A[Mobile App] --> B[API Gateway]
    B --> C[Auth Service]
    B --> D[Product Service]
    B --> E[Order Service]
    B --> F[Payment Service]

    C --> G[(User DB)]
    D --> H[(Product DB)]
    E --> I[(Order DB)]
    F --> J[(Payment DB)]

```

**JWT Kullanım Alanları:**

- User authentication
- Service-to-service communication
- Mobile app authorization

### 2. **Mikroservis Mimarisi**

```mermaid
graph TD
    A[Client] --> B[API Gateway]
    B --> C[Auth Service]
    B --> D[User Service]
    B --> E[Product Service]
    B --> F[Order Service]

    C -.->|JWT Validation| D
    C -.->|JWT Validation| E
    C -.->|JWT Validation| F

```

**Avantajlar:**

- Decentralized authentication
- Service independence
- Horizontal scaling

## Sonuç

JWT, modern web uygulamaları için güçlü ve esnek bir authentication çözümüdür. Özellikle:

### JWT Kullanın Eğer:

- ✅ Stateless architecture istiyorsanız
- ✅ Mikroservis mimarisi kullanıyorsanız
- ✅ Mobile API geliştiriyorsanız
- ✅ Cross-domain authentication gerekiyorsa
- ✅ Horizontal scaling planlıyorsanız

### Session Kullanın Eğer:

- ✅ Geleneksel web uygulaması geliştiriyorsanız
- ✅ Maksimum güvenlik gerekiyorsa
- ✅ Immediate token revocation önemliyse
- ✅ Basit monolitik mimari kullanıyorsanız

### Önemli Hatırlatmalar:

1. **Güvenlik her zaman öncelik** - HTTPS kullanın, secret key'leri koruyun
2. **Token expiration süresini optimize edin** - Güvenlik vs kullanılabilirlik dengesi
3. **Error handling'i ihmal etmeyin** - Kullanıcı deneyimi için kritik
4. **Performance'ı izleyin** - Token boyutu ve verification süreleri
5. **Logging ve monitoring** - Güvenlik olaylarını takip edin

JWT doğru kullanıldığında, modern web uygulamaları için mükemmel bir authentication çözümü sunar! 🚀
