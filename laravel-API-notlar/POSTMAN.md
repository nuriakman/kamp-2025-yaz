# Postman ile Laravel API Test Rehberi

## 📋 Giriş

**Proje Adresi:**

- Apache sanal konak (`VirtualHost`) ayarladıysanız: `http://laravel-api.test`
- Geliştirme sunucusu kullanıyorsanız: `http://localhost:8000`
- Docker kullanıyorsanız: `http://localhost:8001` (veya atanan port)

**Önemli Notlar:**

- Tüm isteklerde `Accept: application/json` header'ı ekleyin
- JWT korumalı endpoint'ler için `Authorization: Bearer {token}` header'ı gereklidir
- Aşağıdaki örneklerde `http://localhost:8000` adresi kullanılacaktır

## 1. Yeni Kategori Oluşturma (POST)

- **Metod:** `POST`
- **URL:** `http://localhost:8000/api/categories`
- **Body (JSON):**
  ```json
  {
    "name": "Elektronik",
    "description": "Elektronik ürünler kategorisi"
  }
  ```
- **Başarılı Yanıt (201 Created):**
  ```json
  {
    "name": "Elektronik",
    "description": "Elektronik ürünler kategorisi",
    "updated_at": "2025-07-24T12:00:00.000000Z",
    "created_at": "2025-07-24T12:00:00.000000Z",
    "id": 1
  }
  ```

## 2. Tüm Kategorileri Listeleme (GET)

- **Metod:** `GET`
- **URL:** `http://localhost:8000/api/categories`
- **Başarılı Yanıt:**
  ```json
  [
    {
      "id": 1,
      "name": "Elektronik",
      "description": "Elektronik ürünler kategorisi",
      "created_at": "2025-07-24T12:00:00.000000Z",
      "updated_at": "2025-07-24T12:00:00.000000Z",
      "products": [
        {
          "id": 1,
          "name": "Akıllı Telefon",
          "description": "Son model, güçlü işlemcili",
          "price": "25000.00",
          "stock": 50,
          "category_id": 1,
          "created_at": "2025-07-24T12:05:00.000000Z",
          "updated_at": "2025-07-24T12:05:00.000000Z"
        }
      ]
    }
  ]
  ```

## 3. Yeni Ürün Oluşturma (POST)

- **Metod:** `POST`
- **URL:** `http://localhost:8000/api/products`
- **Body (JSON):** (category_id'nin `1` olduğuna dikkat edin)
  ```json
  {
    "name": "Akıllı Telefon",
    "description": "Son model, güçlü işlemcili",
    "price": 25000.0,
    "stock": 50,
    "category_id": 1
  }
  ```
- **Başarılı Yanıt (201 Created):**
  ```json
  {
    "name": "Akıllı Telefon",
    "description": "Son model, güçlü işlemcili",
    "price": 25000,
    "stock": 50,
    "category_id": 1,
    "updated_at": "2025-07-24T12:05:00.000000Z",
    "created_at": "2025-07-24T12:05:00.000000Z",
    "id": 1
  }
  ```

## 4. Tüm Ürünleri Listeleme (GET)

- **Metod:** `GET`
- **URL:** `http://localhost:8000/api/products`
- **Başarılı Yanıt:**
  ```json
  [
    {
      "id": 1,
      "name": "Akıllı Telefon",
      "description": "Son model, güçlü işlemcili",
      "price": "25000.00",
      "stock": 50,
      "category_id": 1,
      "created_at": "...",
      "updated_at": "...",
      "category": {
        "id": 1,
        "name": "Elektronik",
        "created_at": "...",
        "updated_at": "..."
      }
    }
  ]
  ```

## 🔐 JWT Kimlik Doğrulama Testleri

### 1. Kullanıcı Kaydı (Register)

- **Metod:** `POST`
- **URL:** `http://localhost:8000/api/auth/register`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON):**
  ```json
  {
    "name": "Test Kullanıcı",
    "email": "test@example.com",
    "password": "123456",
    "password_confirmation": "123456"
  }
  ```
- **Başarılı Yanıt (201 Created):**
  ```json
  {
    "message": "User successfully registered",
    "user": {
      "name": "Test Kullanıcı",
      "email": "test@example.com",
      "updated_at": "2025-07-26T16:00:00.000000Z",
      "created_at": "2025-07-26T16:00:00.000000Z",
      "id": 1
    }
  }
  ```

### 2. Kullanıcı Girişi (Login)

- **Metod:** `POST`
- **URL:** `http://localhost:8000/api/auth/login`
- **Headers:**
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON):**
  ```json
  {
    "email": "test@example.com",
    "password": "123456"
  }
  ```
- **Başarılı Yanıt (200 OK):**
  ```json
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "Test Kullanıcı",
      "email": "test@example.com",
      "email_verified_at": null,
      "created_at": "2025-07-26T16:00:00.000000Z",
      "updated_at": "2025-07-26T16:00:00.000000Z"
    }
  }
  ```

**Önemli:** `access_token` değerini kopyalayın, korumalı endpoint'lerde kullanacaksınız!

### 3. Kullanıcı Profili Görüntüleme (Me)

- **Metod:** `GET`
- **URL:** `http://localhost:8000/api/auth/me`
- **Headers:**
  ```
  Authorization: Bearer {yukarıdaki_access_token}
  Accept: application/json
  ```
- **Başarılı Yanıt (200 OK):**
  ```json
  {
    "id": 1,
    "name": "Test Kullanıcı",
    "email": "test@example.com",
    "email_verified_at": null,
    "created_at": "2025-07-26T16:00:00.000000Z",
    "updated_at": "2025-07-26T16:00:00.000000Z"
  }
  ```

### 4. Çıkış Yapma (Logout)

- **Metod:** `POST`
- **URL:** `http://localhost:8000/api/auth/logout`
- **Headers:**
  ```
  Authorization: Bearer {access_token}
  Accept: application/json
  ```
- **Başarılı Yanıt (200 OK):**
  ```json
  {
    "message": "User successfully signed out"
  }
  ```

### 5. JWT ile Korumalı Endpoint'lere Erişim

JWT sistemi aktif olduktan sonra, kategoriler ve ürünler endpoint'leri korunur. Bu endpoint'lere erişmek için:

**Örnek: Kategorileri Listeleme (JWT ile)**

- **Metod:** `GET`
- **URL:** `http://localhost:8000/api/categories`
- **Headers:**
  ```
  Authorization: Bearer {access_token}
  Accept: application/json
  ```

**Token olmadan erişim denemesi:**

```json
{
  "message": "Unauthenticated."
}
```

**Geçersiz token ile erişim denemesi:**

```json
{
  "message": "Token is Invalid"
}
```

## 📚 Postman Collection Dosyaları

Manuel test yapmak yerine hazır Postman collection dosyalarını kullanabilirsiniz:

- **V1:** Temel API testleri (JWT yok)
- **V2:** Test verileri ile genişletilmiş testler
- **V3:** Değişken destekli esnek testler
- **V4:** JWT kimlik doğrulama testleri dahil

Detaylı bilgi için: [POSTMAN-COLLECTION.md](./POSTMAN-COLLECTION.md)

## ✨ Tebrikler!

Artık tam özellikli Laravel API'nizi Postman ile test edebilirsiniz:

- ✅ Temel CRUD işlemleri
- ✅ JWT kimlik doğrulama sistemi
- ✅ Korumalı endpoint'ler
- ✅ Kullanıcı yönetimi

Sonraki adımlar için diğer ders notlarını inceleyebilirsiniz!
