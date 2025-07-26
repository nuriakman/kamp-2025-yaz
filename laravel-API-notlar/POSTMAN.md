# Postman ile API Test Talimatları

## Giriş

**Proje Adresi:**
Kurulum adımlarında Apache için sanal konak (`VirtualHost`) ayarladıysanız, projenize `http://laravel-api.test` gibi bir adresten erişebilirsiniz.

Eğer, Apache için sanal konak (`VirtualHost`) ayarlamadıysanız, projenize `http://localhost:8000` gibi bir adresten erişebilirsiniz. Aşağıdaki örneklerde bu adres kullanılacaktır.

## 1. Yeni Kategori Oluşturma (POST)

- **Metod:** `POST`
- **URL:** `http://localhost:8000/api/categories`
- **Body (JSON):**
  ```json
  {
    "name": "Elektronik"
  }
  ```
- **Başarılı Yanıt (201 Created):**
  ```json
  {
    "name": "Elektronik",
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
      "created_at": "2025-07-24T12:00:00.000000Z",
      "updated_at": "2025-07-24T12:00:00.000000Z"
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
    "category_id": 1
  }
  ```
- **Başarılı Yanıt (201 Created):**
  ```json
  {
    "name": "Akıllı Telefon",
    "description": "Son model, güçlü işlemcili",
    "price": 25000,
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
